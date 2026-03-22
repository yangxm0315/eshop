<?php
$pageTitle = '管理后台';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">管理后台</h1>

<div class="grid grid-4" style="margin-bottom: 2rem;">
    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="card-body">
            <h3 style="font-size: 2rem;"><?= $stats['users'] ?></h3>
            <p>用户总数</p>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div class="card-body">
            <h3 style="font-size: 2rem;"><?= $stats['products'] ?></h3>
            <p>商品总数</p>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <div class="card-body">
            <h3 style="font-size: 2rem;"><?= $stats['orders'] ?></h3>
            <p>订单总数</p>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
        <div class="card-body">
            <h3 style="font-size: 2rem;"><?= $stats['pending_orders'] ?></h3>
            <p>待处理订单</p>
        </div>
    </div>
</div>

<h2 style="margin-bottom: 1rem;">最近订单</h2>
<table style="width: 100%;">
    <thead>
        <tr>
            <th>订单号</th>
            <th>用户</th>
            <th>金额</th>
            <th>状态</th>
            <th>时间</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentOrders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['order_no']) ?></td>
                <td><?= htmlspecialchars($order['user_name'] ?? '未知') ?></td>
                <td>¥<?= number_format($order['pay_amount'] / 100, 2) ?></td>
                <td>
                    <?php
                    $statusTexts = ['待支付', '待发货', '已发货', '已完成', '已取消'];
                    $statusColors = ['#ffc107', '#17a2b8', '#28a745', '#6c757d', '#dc3545'];
                    ?>
                    <span style="background: <?= $statusColors[$order['status']] ?>; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">
                        <?= $statusTexts[$order['status']] ?>
                    </span>
                </td>
                <td><?= $order['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="margin-top: 1.5rem;">
    <a href="/admin/orders" class="btn btn-primary">查看全部订单</a>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/admin.php';
