<?php

namespace Core;

/**
 * 简单路由类
 */
class Router
{
    private static array $routes = [];
    private static array $middlewareGroups = [];
    private static ?Router $instance = null;

    private function __construct() {}

    public static function getInstance(): Router
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 注册 GET 路由
     */
    public static function get(string $path, callable|array $callback, string $name = ''): void
    {
        self::register('GET', $path, $callback, $name);
    }

    /**
     * 注册 POST 路由
     */
    public static function post(string $path, callable|array $callback, string $name = ''): void
    {
        self::register('POST', $path, $callback, $name);
    }

    /**
     * 注册 PUT 路由
     */
    public static function put(string $path, callable|array $callback, string $name = ''): void
    {
        self::register('PUT', $path, $callback, $name);
    }

    /**
     * 注册 DELETE 路由
     */
    public static function delete(string $path, callable|array $callback, string $name = ''): void
    {
        self::register('DELETE', $path, $callback, $name);
    }

    /**
     * 注册路由
     */
    private static function register(string $method, string $path, callable|array $callback, string $name): void
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
            'name' => $name,
            'middlewares' => [],
        ];
    }

    /**
     * 设置路由中间件
     */
    public static function middleware(array $middlewares): void
    {
        self::$middlewareGroups = $middlewares;
    }

    /**
     * 注册带中间件的路由组
     */
    public static function group(array $attributes, callable $callback): void
    {
        $previousMiddlewares = self::$middlewareGroups;
        $middlewares = $attributes['middleware'] ?? [];

        if (isset($attributes['prefix'])) {
            $prefix = $attributes['prefix'];
        }

        if (isset($attributes['name'])) {
            $namePrefix = $attributes['name'];
        }

        // 合并中间件
        self::$middlewareGroups = array_merge(self::$middlewareGroups, $middlewares);

        // 执行回调注册路由
        $callback();

        // 恢复之前的中间件
        self::$middlewareGroups = $previousMiddlewares;
    }

    /**
     * 分发请求到对应路由
     */
    public function dispatch(string $uri, string $method): mixed
    {
        // 移除查询字符串
        $uri = parse_url($uri, PHP_URL_PATH);

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            // 检查路由是否匹配
            $params = $this->matchRoute($route['path'], $uri);
            if ($params === false) {
                continue;
            }

            // 执行中间件
            foreach ($route['middlewares'] as $middleware) {
                $result = $this->runMiddleware($middleware);
                if ($result !== null) {
                    return $result;
                }
            }

            // 执行路由回调
            return $this->executeCallback($route['callback'], $params);
        }

        // 404
        http_response_code(404);
        return $this->renderView('errors/404');
    }

    /**
     * 匹配路由参数
     */
    private function matchRoute(string $routePath, string $uri): array|false
    {
        // 处理动态参数路由 /products/{product}
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            return $params;
        }

        return false;
    }

    /**
     * 执行中间件
     */
    private function runMiddleware(string $middleware): mixed
    {
        $className = "\\Middleware\\{$middleware}";
        if (class_exists($className)) {
            return (new $className())->handle();
        }
        return null;
    }

    /**
     * 执行路由回调
     */
    private function executeCallback(callable|array $callback, array $params): mixed
    {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        if (is_array($callback)) {
            $controller = new $callback[0]();
            $method = $callback[1];
            // 将关联数组参数转换为位置参数
            return call_user_func_array([$controller, $method], array_values($params));
        }

        throw new \Exception("Invalid callback");
    }

    /**
     * 渲染视图
     */
    public function renderView(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        include __DIR__ . "/../../views/{$view}.php";
        return ob_get_clean();
    }

    /**
     * 生成 URL
     */
    public static function route(string $name, array $params = []): string
    {
        foreach (self::$routes as $route) {
            if ($route['name'] === $name) {
                $path = $route['path'];
                foreach ($params as $key => $value) {
                    $path = str_replace('{' . $key . '}', $value, $path);
                }
                return $path;
            }
        }
        return '/';
    }
}
