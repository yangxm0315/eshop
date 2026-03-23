<?php
$pageTitle = '支付成功';
ob_start();
?>

<style>
    .success-container {
        max-width: 500px;
        margin: 0 auto;
        padding: 3rem 0;
        text-align: center;
    }
    .success-icon {
        width: 80px;
        height: 80px;
        background: #28a745;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        margin: 0 auto 1.5rem;
    }
    .success-title {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 1rem;
    }
    .order-info {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin: 2rem 0;
        text-align: left;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
</style>

<div class="success-container">
    <div class="success-icon">✓</div>
    <h1 class="success-title">支付成功</h1>
    <p style="color: #666;">感谢您的购买</p>

    <div class="order-info">
        <div class="info-row">
            <span>订单编号</span>
            <span><?= htmlspecialchars($order['order_no']) ?></span>
        </div>
        <div class="info-row">
            <span>订单金额</span>
            <span style="color: #dc3545; font-weight: bold;">¥<?= number_format($order['pay_amount'] / 100, 2) ?></span>
        </div>
        <div class="info-row">
            <span>支付时间</span>
            <span><?= $order['paid_at'] ?? date('Y-m-d H:i:s') ?></span>
        </div>
        <div class="info-row">
            <span>订单状态</span>
            <span style="color: #28a745;">待发货</span>
        </div>
    </div>

    <div style="display: flex; gap: 1rem; justify-content: center;">
        <a href="/orders/<?= $order['id'] ?>" class="btn btn-primary">查看订单</a>
        <a href="/products" class="btn">继续购物</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
?>
