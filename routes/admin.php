<?php
/**
 * 后台管理路由文件
 */

use Core\Router;

// 后台首页
Router::get('/admin', [\Controllers\Admin\DashboardController::class, 'index'], 'admin.dashboard');

// 商品管理
Router::get('/admin/products', [\Controllers\Admin\ProductController::class, 'index'], 'admin.products.index');
Router::get('/admin/products/create', [\Controllers\Admin\ProductController::class, 'create'], 'admin.products.create');
Router::post('/admin/products/store', [\Controllers\Admin\ProductController::class, 'store'], 'admin.products.store');
Router::post('/admin/products/image/delete', [\Controllers\Admin\ProductController::class, 'deleteImage'], 'admin.products.image.delete');
Router::get('/admin/products/{id}/edit', [\Controllers\Admin\ProductController::class, 'edit'], 'admin.products.edit');
Router::post('/admin/products/{id}/update', [\Controllers\Admin\ProductController::class, 'update'], 'admin.products.update');
Router::post('/admin/products/{id}/delete', [\Controllers\Admin\ProductController::class, 'destroy'], 'admin.products.destroy');
Router::post('/admin/products/{id}/set-main-image', [\Controllers\Admin\ProductController::class, 'setMainImage'], 'admin.products.set.main.image');

// 分类管理
Router::get('/admin/categories', [\Controllers\Admin\CategoryController::class, 'index'], 'admin.categories.index');
Router::get('/admin/categories/create', [\Controllers\Admin\CategoryController::class, 'create'], 'admin.categories.create');
Router::post('/admin/categories/store', [\Controllers\Admin\CategoryController::class, 'store'], 'admin.categories.store');
Router::get('/admin/categories/{id}/edit', [\Controllers\Admin\CategoryController::class, 'edit'], 'admin.categories.edit');
Router::post('/admin/categories/{id}/update', [\Controllers\Admin\CategoryController::class, 'update'], 'admin.categories.update');
Router::post('/admin/categories/{id}/delete', [\Controllers\Admin\CategoryController::class, 'destroy'], 'admin.categories.destroy');

// 订单管理
Router::get('/admin/orders', [\Controllers\Admin\OrderController::class, 'index'], 'admin.orders.index');
Router::get('/admin/orders/{id}', [\Controllers\Admin\OrderController::class, 'show'], 'admin.orders.show');
Router::post('/admin/orders/{id}/ship', [\Controllers\Admin\OrderController::class, 'ship'], 'admin.orders.ship');
Router::post('/admin/orders/{id}/complete', [\Controllers\Admin\OrderController::class, 'complete'], 'admin.orders.complete');
