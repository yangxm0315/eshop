<?php

/**
 * 应用入口文件
 */

// 错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 定义基础路径
define('BASE_PATH', dirname(__DIR__));

// 自动加载
spl_autoload_register(function ($class) {
    $baseDir = BASE_PATH . '/src/';
    
    // 将命名空间转换为文件路径
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// 启动 Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 加载路由文件
require BASE_PATH . '/routes/web.php';
require BASE_PATH . '/routes/auth.php';
require BASE_PATH . '/routes/admin.php';

// 分发请求
$router = Core\Router::getInstance();
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$response = $router->dispatch($uri, $method);

if ($response instanceof Core\Response) {
    $response->send();
} else {
    echo $response;
}
