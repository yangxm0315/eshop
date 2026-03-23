<?php
/**
 * 前台路由文件
 */

use Core\Router;

// 首页
Router::get('/', [\Controllers\HomeController::class, 'index'], 'home');

// 商品列表
Router::get('/products', [\Controllers\ProductController::class, 'index'], 'products.index');
Router::get('/products/{id}', [\Controllers\ProductController::class, 'show'], 'products.show');

// 搜索
Router::get('/search', [\Controllers\ProductController::class, 'search'], 'products.search');

// 分类
Router::get('/category/{id}', [\Controllers\ProductController::class, 'byCategory'], 'category.show');

// 购物车
Router::get('/cart', [\Controllers\CartController::class, 'index'], 'cart.index');
Router::post('/cart/add/{id}', [\Controllers\CartController::class, 'add'], 'cart.add');
Router::post('/cart/update/{id}', [\Controllers\CartController::class, 'update'], 'cart.update');
Router::post('/cart/remove/{id}', [\Controllers\CartController::class, 'destroy'], 'cart.remove');
Router::post('/cart/clear', [\Controllers\CartController::class, 'clear'], 'cart.clear');

// 订单
Router::get('/checkout', [\Controllers\OrderController::class, 'checkout'], 'checkout');
Router::get('/orders', [\Controllers\OrderController::class, 'index'], 'orders.index');
Router::get('/orders/{id}', [\Controllers\OrderController::class, 'show'], 'orders.show');
Router::post('/orders', [\Controllers\OrderController::class, 'store'], 'orders.store');
Router::post('/orders/{id}/cancel', [\Controllers\OrderController::class, 'cancel'], 'orders.cancel');

// 用户中心
Router::get('/profile', [\Controllers\ProfileController::class, 'index'], 'profile.index');
Router::get('/profile/edit', [\Controllers\ProfileController::class, 'edit'], 'profile.edit');
Router::post('/profile/update', [\Controllers\ProfileController::class, 'update'], 'profile.update');

// 收货地址
Router::post('/profile/addresses/create', [\Controllers\ProfileController::class, 'addAddress'], 'profile.addresses.create');
Router::post('/profile/addresses/update/{id}', [\Controllers\ProfileController::class, 'updateAddress'], 'profile.addresses.update');
Router::post('/profile/addresses/delete/{id}', [\Controllers\ProfileController::class, 'deleteAddress'], 'profile.addresses.delete');

// 支付
Router::get('/payment/{orderId}', [\Controllers\PaymentController::class, 'show'], 'payment.show');
Router::post('/payment/wechat/create/{orderId}', [\Controllers\PaymentController::class, 'createWechatQr'], 'payment.wechat.create');
Router::get('/payment/wechat/query', [\Controllers\PaymentController::class, 'queryPayStatus'], 'payment.wechat.query');
Router::post('/payment/wechat/notify', [\Controllers\PaymentController::class, 'wechatNotify'], 'payment.wechat.notify');
Router::get('/payment/success/{orderId}', [\Controllers\PaymentController::class, 'success'], 'payment.success');
