<?php
$pageTitle = '订单管理';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">订单管理</h1>

<table>
    <thead>
        <tr>
            <th>订单号</th>
            <th>用户</th>
            <th>金额</th>
            <th>状态</th>
            <th>下单时间</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['order_no']) ?></td>
                <td><?= htmlspecialchars($order['user_name'] ?? '-') ?></td>
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
                <td>
                    <a href="/admin/orders/<?= $order['id'] ?>" class="btn btn-primary" style="padding: 0.25rem 0.5rem;">详情</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/admin.php';
