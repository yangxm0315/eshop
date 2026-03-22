<?php
$pageTitle = '我的订单';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">我的订单</h1>

<?php if (!empty($orders)): ?>
    <table style="width: 100%;">
        <thead>
            <tr>
                <th>订单号</th>
                <th>下单时间</th>
                <th>金额</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_no']) ?></td>
                    <td><?= $order['created_at'] ?></td>
                    <td style="color: #dc3545;">¥<?= number_format($order['pay_amount'] / 100, 2) ?></td>
                    <td>
                        <?php
                        $statusTexts = ['待支付', '待发货', '已发货', '已完成', '已取消'];
                        $statusColors = ['#ffc107', '#17a2b8', '#28a745', '#6c757d', '#dc3545'];
                        $status = $order['status'];
                        ?>
                        <span style="background: <?= $statusColors[$status] ?>; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">
                            <?= $statusTexts[$status] ?>
                        </span>
                    </td>
                    <td>
                        <a href="/orders/<?= $order['id'] ?>" class="btn btn-primary" style="padding: 0.25rem 0.5rem;">详情</a>
                        <?php if ($order['status'] == 0): ?>
                            <form action="/orders/<?= $order['id'] ?>/cancel" method="POST" style="display: inline;">
                                <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem;">取消</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div style="text-align: center; padding: 3rem;">
        <p style="font-size: 1.2rem; color: #666; margin-bottom: 1rem;">暂无订单</p>
        <a href="/products" class="btn btn-primary">去购物</a>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
