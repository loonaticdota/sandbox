<?php

class Controller
{
    /**
     * Render a view template file from app/view/
     *
     * @param string $view Relative view file without .php extension (e.g. 'home', 'dashboard/index')
     * @param array $data Associative array of variables made available to the view
     * @param bool $return If true, returns rendered output string instead of echoing (useful for workers/emails)
     * @return string|null Returns string if $return is true, null otherwise
     */
    protected function render(string $view, array $data = [], bool $return = false): ?string
    {
        $viewPath = BASEPATH . '/app/view/' . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new \InvalidArgumentException("View file [{$viewPath}] not found.");
        }

        extract($data);

        if ($return) {
            ob_start();
            require $viewPath;
            return ob_get_clean();
        }

        require $viewPath;
        return null;
    }

    /**
     * Run an asynchronous task in the background (fire-and-forget)
     */
    protected function async(string $route, array $params = []): void
    {
        Task::async($route, $params);
    }

    /**
     * Run multiple callables concurrently using PHP 8.1 Fibers
     */
    protected function parallel(array $tasks): array
    {
        return Task::parallel($tasks);
    }

    /**
     * Fetch multiple external URLs in parallel using curl_multi
     */
    protected function parallelHttp(array $urls, int $timeoutSeconds = 10): array
    {
        return Task::parallelHttp($urls, $timeoutSeconds);
    }

    /**
     * Output a JSON response and terminate execution
     *
     * @param mixed $data Data to encode as JSON
     * @param int $statusCode HTTP status code (default: 200)
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect to another URL or route and terminate execution
     *
     * @param string $path Target route or external URL (e.g. 'login', 'dashboard', 'https://...')
     */
    protected function redirect(string $path): void
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $url = $path;
        } else {
            $url = BASEURL . ltrim($path, '/');
        }

        header('Location: ' . $url);
        exit;
    }

    // =========================================================================
    // AUTHENTICATION & JWT UTILITIES
    // =========================================================================

    /**
     * Generate a signed JWT token
     *
     * @param array $payload Custom user data to encode into the token
     * @param int $expirySeconds Token lifetime in seconds (default: 3600 / 1 hour)
     * @return string Signed JWT token string
     */
    protected function generateToken(array $payload, int $expirySeconds = 3600): string
    {
        $secret = $GLOBALS['config']['secret_key'] ?? 'default-secret-key';

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        $issuedAt = time();
        $payload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $issuedAt + $expirySeconds,
        ]);

        $encodedHeader = $this->base64UrlEncode(json_encode($header));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', "{$encodedHeader}.{$encodedPayload}", $secret, true);
        $encodedSignature = $this->base64UrlEncode($signature);

        return "{$encodedHeader}.{$encodedPayload}.{$encodedSignature}";
    }

    /**
     * Validate JWT token from Authorization header or cookie
     *
     * @param string|null $token Optional explicit token; if null, extracted automatically
     * @return array|null Returns decoded payload on success, or null if invalid/expired
     */
    protected function validateToken(?string $token = null): ?array
    {
        $token = $token ?? $this->extractToken();
        if (!$token) {
            return null;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

        $secret = $GLOBALS['config']['secret_key'] ?? 'default-secret-key';
        $expectedSignature = hash_hmac('sha256', "{$encodedHeader}.{$encodedPayload}", $secret, true);

        // Timing-attack safe comparison
        if (!hash_equals($expectedSignature, $this->base64UrlDecode($encodedSignature))) {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($encodedPayload), true);
        if (!is_array($payload)) {
            return null;
        }

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    /**
     * Require authentication; halts or redirects if token is missing or invalid
     *
     * @param string|null $redirectRoute Route to redirect unauthenticated users (defaults to 'login')
     * @return array Returns decoded token payload (user data)
     */
    protected function requireAuth(?string $redirectRoute = 'login'): array
    {
        $user = $this->validateToken();

        if ($user === null) {
            // If it's an API/AJAX request, return 401 JSON
            if ($this->isAjaxOrJsonRequest()) {
                $this->json(['error' => 'Unauthorized access.'], 401);
            }

            // Otherwise, redirect to login page
            $this->redirect($redirectRoute ?? 'login');
        }

        return $user;
    }

    /**
     * Check if the current user is authenticated
     */
    protected function isAuthenticated(): bool
    {
        return $this->validateToken() !== null;
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Extract bearer token from Authorization header, apache headers, or cookie
     */
    private function extractToken(): ?string
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? null;

        if (!$authHeader && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        }

        if ($authHeader && preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
            return $matches[1];
        }

        // Fallback to cookie
        return $_COOKIE['jwt_token'] ?? $_COOKIE['token'] ?? null;
    }

    private function isAjaxOrJsonRequest(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return str_contains($accept, 'application/json') || strtolower($requestedWith) === 'xmlhttprequest';
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}