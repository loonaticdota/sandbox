<?php

class Router
{
    /**
     * Map of route paths to [Controller File Relative to app/controller/, ClassName, MethodName]
     */
    private array $routes = [
        // =========================================================================
        // ACTIVE ROUTES
        // =========================================================================
        'home'    => ['home/home.php', 'Home', 'home'],
        'example' => ['example/example.php', 'Example', 'index'],

        // =========================================================================
        // EXAMPLES FOR COMMON ROUTING SCENARIOS (Uncomment and adapt as needed)
        // =========================================================================
        //
        // 1. Simple Exact Route:
        //    URL: /about
        //    'about' => ['about/about.php', 'About', 'index'],
        //
        // 2. Nested Module Route:
        //    URL: /dashboard/analytics
        //    'dashboard/analytics' => ['dashboard/analytics.php', 'Analytics', 'view'],
        //
        // 3. Auth & Action Endpoints:
        //    URL: /login
        //    URL: /logout
        //    'login'  => ['auth/login.php', 'Login', 'loginPage'],
        //    'logout' => ['auth/login.php', 'Login', 'logout'],
        //
        // 4. Single Dynamic Parameter (Slug or Numeric ID):
        //    URL: /news/breaking-update -> Method receives: function show(string $slug)
        //    URL: /user/42              -> Method receives: function profile(string $id)
        //    'news/{slug}' => ['news/news.php', 'News', 'show'],
        //    'user/{id}'   => ['user/user.php', 'User', 'profile'],
        //
        // 5. Multiple Dynamic Parameters:
        //    URL: /article/tech/ai-trends -> Method receives: function view(string $category, string $slug)
        //    'article/{category}/{slug}' => ['article/article.php', 'Article', 'view'],
        //
        // 6. Action on a Parameterized Resource:
        //    URL: /post/99/edit -> Method receives: function edit(string $id)
        //    'post/{id}/edit' => ['post/post.php', 'Post', 'edit'],
    ];

    public function load(string $request, array $cliParams = []): void
    {
        // 1. Ensure base Controller is available before loading child controllers
        require_once BASEPATH . '/core/controller.php';

        // 2. Check for exact route match
        if (isset($this->routes[$request])) {
            [$file, $class, $method] = $this->routes[$request];
            $this->dispatch($file, $class, $method, $cliParams);
            return;
        }

        // 3. Check for dynamic pattern routes (e.g., news/{slug})
        foreach ($this->routes as $pattern => $handler) {
            if (str_contains($pattern, '{')) {
                $regex = '#^' . preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $pattern) . '$#';
                if (preg_match($regex, $request, $matches)) {
                    array_shift($matches); // Remove the full match
                    [$file, $class, $method] = $handler;
                    $params = !empty($cliParams) ? array_merge($matches, $cliParams) : $matches;
                    $this->dispatch($file, $class, $method, $params);
                    return;
                }
            }
        }

        // 4. Fallback 404
        $this->notFound();
    }

    private function dispatch(string $file, string $class, string $method, array $params = []): void
    {
        $filePath = BASEPATH . '/app/controller/' . $file;
        if (!file_exists($filePath)) {
            $this->notFound("Controller file [{$file}] not found.");
            return;
        }

        require_once $filePath;

        if (!class_exists($class)) {
            $this->notFound("Class [{$class}] not found in file [{$file}].");
            return;
        }

        $instance = new $class();

        if (!method_exists($instance, $method)) {
            $this->notFound("Method [{$method}] not found in [{$class}].");
            return;
        }

        // Call method with unpacked parameters
        $instance->$method(...$params);
    }

    private function notFound(string $message = 'Page not found'): void
    {
        http_response_code(404);
        echo $message;
    }
}
