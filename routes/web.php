<?php

// 首页
Core\Router::get('/', [Controllers\HomeController::class, 'index'], 'home');

// 商品列表
Core\Router::get('/products', [Controllers\ProductController::class, 'index'], 'products.index');
Core\Router::get('/products/{id}', [Controllers\ProductController::class, 'show'], 'products.show');

// 购物车
Core\Router::get('/cart', [Controllers\CartController::class, 'index'], 'cart.index');
Core\Router::post('/cart/{id}/add', [Controllers\CartController::class, 'add'], 'cart.add');
Core\Router::put('/cart/{id}', [Controllers\CartController::class, 'update'], 'cart.update');
Core\Router::delete('/cart/{id}', [Controllers\CartController::class, 'destroy'], 'cart.destroy');
Core\Router::delete('/cart/clear', [Controllers\CartController::class, 'clear'], 'cart.clear');

// 需要登录的路由
Core\Router::get('/checkout', function() {
    $session = Core\Session::getInstance();
    if (!$session->isLoggedIn()) {
        $session->flash('error', '请先登录');
        $response = new Core\Response();
        return $response->redirect('/login');
    }
    $orderController = new Controllers\OrderController();
    return $orderController->checkout();
}, 'orders.checkout');

Core\Router::post('/orders', [Controllers\OrderController::class, 'store'], 'orders.store');
Core\Router::get('/orders', [Controllers\OrderController::class, 'index'], 'orders.index');
Core\Router::get('/orders/{id}', [Controllers\OrderController::class, 'show'], 'orders.show');
Core\Router::post('/orders/{id}/cancel', [Controllers\OrderController::class, 'cancel'], 'orders.cancel');

// 用户中心
Core\Router::get('/profile', [Controllers\ProfileController::class, 'edit'], 'profile.edit');
Core\Router::post('/profile', [Controllers\ProfileController::class, 'update'], 'profile.update');
Core\Router::post('/profile/address', [Controllers\ProfileController::class, 'addAddress'], 'profile.address.add');
Core\Router::post('/profile/address/{id}/update', [Controllers\ProfileController::class, 'updateAddress'], 'profile.address.update');
Core\Router::post('/profile/address/{id}/delete', [Controllers\ProfileController::class, 'deleteAddress'], 'profile.address.delete');
Core\Router::post('/profile/address/{id}/default', [Controllers\ProfileController::class, 'setDefaultAddress'], 'profile.address.default');
