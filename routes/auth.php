<?php

// 访客路由（已登录用户不能访问）
Core\Router::get('/register', function() {
    $session = Core\Session::getInstance();
    if ($session->isLoggedIn()) {
        $response = new Core\Response();
        return $response->redirect('/');
    }
    $controller = new Controllers\Auth\RegisteredUserController();
    return $controller->create();
}, 'register');

Core\Router::post('/register', [Controllers\Auth\RegisteredUserController::class, 'store']);

Core\Router::get('/login', function() {
    $session = Core\Session::getInstance();
    if ($session->isLoggedIn()) {
        $response = new Core\Response();
        return $response->redirect('/');
    }
    $controller = new Controllers\Auth\AuthenticatedSessionController();
    return $controller->create();
}, 'login');

Core\Router::post('/login', [Controllers\Auth\AuthenticatedSessionController::class, 'store']);

Core\Router::get('/forgot-password', [Controllers\Auth\PasswordResetLinkController::class, 'create'], 'password.request');
Core\Router::post('/forgot-password', [Controllers\Auth\PasswordResetLinkController::class, 'store']);

Core\Router::get('/reset-password/{token}', [Controllers\Auth\NewPasswordController::class, 'create'], 'password.reset');
Core\Router::post('/reset-password', [Controllers\Auth\NewPasswordController::class, 'store']);

// 需要登录的路由
Core\Router::post('/logout', function() {
    $controller = new Controllers\Auth\AuthenticatedSessionController();
    return $controller->destroy();
}, 'logout');
