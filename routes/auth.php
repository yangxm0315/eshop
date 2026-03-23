<?php
/**
 * 认证路由文件
 */

use Core\Router;

// 登录
Router::get('/login', [\Controllers\Auth\AuthenticatedSessionController::class, 'create'], 'login');
Router::post('/login', [\Controllers\Auth\AuthenticatedSessionController::class, 'store']);

// 注册
Router::get('/register', [\Controllers\Auth\RegisteredUserController::class, 'create'], 'register');
Router::post('/register', [\Controllers\Auth\RegisteredUserController::class, 'store']);

// 退出
Router::post('/logout', [\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'], 'logout');

// 忘记密码
Router::get('/forgot-password', [\Controllers\Auth\PasswordResetLinkController::class, 'create'], 'forgot-password');
Router::post('/forgot-password', [\Controllers\Auth\PasswordResetLinkController::class, 'store']);

// 重置密码
Router::get('/reset-password/{token}', [\Controllers\Auth\NewPasswordController::class, 'create'], 'reset-password');
Router::post('/reset-password', [\Controllers\Auth\NewPasswordController::class, 'store']);
