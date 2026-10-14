<?php

class Task
{
    /**
     * Run a task asynchronously in the background (fire-and-forget).
     * The web request will NOT wait for this to finish.
     *
     * @param string $route The route to execute via CLI (e.g. 'mail/send', 'log/cleanup')
     * @param array $params Data passed to the background process (received via JSON)
     */
    public static function async(string $route, array $params = []): void
    {
        $phpBinary = self::detectPhpBinary();
        $cliScript = BASEPATH . '/cli.php';
        $jsonParam = escapeshellarg(json_encode($params));

        if (stripos(PHP_OS, 'WIN') === 0) {
            // Windows background detached process
            $command = "start /B \"\" \"{$phpBinary}\" \"{$cliScript}\" \"{$route}\" {$jsonParam} > NUL 2>&1";
            pclose(popen($command, 'r'));
        } else {
            // Linux / Unix background detached process
            $command = "\"{$phpBinary}\" \"{$cliScript}\" \"{$route}\" {$jsonParam} > /dev/null 2>&1 &";
            exec($command);
        }
    }

    /**
     * Run multiple callables concurrently using PHP 8.1 Fibers.
     *
     * @param array<string, callable> $tasks Key-value array of named callables
     * @return array Results indexed by task keys: ['result' => mixed, 'error' => ?string]
     */
    public static function parallel(array $tasks): array
    {
        $fibers = [];
        $results = [];

        foreach ($tasks as $key => $callable) {
            $fiber = new \Fiber(function () use ($callable) {
                return $callable();
            });

            $fibers[$key] = $fiber;
        }

        // Start all fibers
        foreach ($fibers as $key => $fiber) {
            try {
                $value = $fiber->start();
                if ($fiber->isTerminated()) {
                    $results[$key] = [
                        'success' => true,
                        'data'    => $fiber->getReturn(),
                    ];
                }
            } catch (\Throwable $e) {
                $results[$key] = [
                    'success' => false,
                    'error'   => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Fetch multiple URLs in parallel using non-blocking curl_multi.
     * All requests run simultaneously over the network.
     *
     * @param array<string, string> $urls Associative array of ['id' => 'https://...']
     * @param int $timeoutSeconds Timeout per request
     * @return array<string, array> Results: ['statusCode' => int, 'body' => string, 'error' => ?string]
     */
    public static function parallelHttp(array $urls, int $timeoutSeconds = 10): array
    {
        $multiHandle = curl_multi_init();
        $channels = [];
        $results = [];

        foreach ($urls as $key => $url) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => $timeoutSeconds,
                CURLOPT_USERAGENT      => 'Sandbox/1.0',
            ]);

            curl_multi_add_handle($multiHandle, $ch);
            $channels[$key] = $ch;
        }

        // Execute all queries simultaneously
        $active = null;
        do {
            $status = curl_multi_exec($multiHandle, $active);
            if ($active) {
                curl_multi_select($multiHandle);
            }
        } while ($active && $status == CURLM_OK);

        // Collect responses
        foreach ($channels as $key => $ch) {
            $body = curl_multi_getcontent($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            $results[$key] = [
                'statusCode' => $statusCode,
                'body'       => $body,
                'error'      => !empty($error) ? $error : null,
            ];

            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandle);

        return $results;
    }

    /**
     * Locate the running PHP executable
     */
    private static function detectPhpBinary(): string
    {
        if (defined('PHP_BINARY') && file_exists(PHP_BINARY)) {
            return PHP_BINARY;
        }

        // Fallback for Windows Laragon
        $laragonPhp = 'C:\\laragon\\bin\\php\\php-8.1.10-Win32-vs16-x64\\php.exe';
        if (file_exists($laragonPhp)) {
            return $laragonPhp;
        }

        return 'php';
    }
}

