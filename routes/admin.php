<?php

// 后台管理路由
Core\Router::get('/admin', function() {
    $session = Core\Session::getInstance();
    if (!$session->isLoggedIn()) {
        $session->flash('error', '请先登录');
        $response = new Core\Response();
        return $response->redirect('/login');
    }
    if (!$session->isAdmin()) {
        $session->flash('error', '无权访问');
        $response = new Core\Response();
        return $response->redirect('/');
    }
    $controller = new Controllers\Admin\DashboardController();
    return $controller->index();
}, 'admin.dashboard');

// 分类管理
Core\Router::get('/admin/categories', [Controllers\Admin\CategoryController::class, 'index'], 'admin.categories.index');
Core\Router::post('/admin/categories', [Controllers\Admin\CategoryController::class, 'store'], 'admin.categories.store');
Core\Router::get('/admin/categories/{id}/edit', [Controllers\Admin\CategoryController::class, 'edit'], 'admin.categories.edit');
Core\Router::post('/admin/categories/{id}/update', [Controllers\Admin\CategoryController::class, 'update'], 'admin.categories.update');
Core\Router::post('/admin/categories/{id}/delete', [Controllers\Admin\CategoryController::class, 'destroy'], 'admin.categories.destroy');

// 商品管理
Core\Router::get('/admin/products', [Controllers\Admin\ProductController::class, 'index'], 'admin.products.index');
Core\Router::get('/admin/products/create', [Controllers\Admin\ProductController::class, 'create'], 'admin.products.create');
Core\Router::post('/admin/products', [Controllers\Admin\ProductController::class, 'store'], 'admin.products.store');
Core\Router::get('/admin/products/{id}/edit', [Controllers\Admin\ProductController::class, 'edit'], 'admin.products.edit');
Core\Router::post('/admin/products/{id}/update', [Controllers\Admin\ProductController::class, 'update'], 'admin.products.update');
Core\Router::post('/admin/products/{id}/delete', [Controllers\Admin\ProductController::class, 'destroy'], 'admin.products.destroy');
Core\Router::post('/admin/products/{id}/toggle', [Controllers\Admin\ProductController::class, 'toggleStatus'], 'admin.products.toggle');

// 订单管理
Core\Router::get('/admin/orders', [Controllers\Admin\OrderController::class, 'index'], 'admin.orders.index');
Core\Router::get('/admin/orders/{id}', [Controllers\Admin\OrderController::class, 'show'], 'admin.orders.show');
Core\Router::post('/admin/orders/{id}/ship', [Controllers\Admin\OrderController::class, 'ship'], 'admin.orders.ship');
Core\Router::post('/admin/orders/{id}/complete', [Controllers\Admin\OrderController::class, 'complete'], 'admin.orders.complete');
