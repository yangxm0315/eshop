<?php
$pageTitle = '订单详情';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">订单详情</h1>

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <p><strong>订单号：</strong><?= htmlspecialchars($order['order_no']) ?></p>
                <p><strong>下单时间：</strong><?= $order['created_at'] ?></p>
            </div>
            <div>
                <p>
                    <strong>状态：</strong>
                    <?php
                    $statusTexts = ['待支付', '待发货', '已发货', '已完成', '已取消'];
                    $statusColors = ['#ffc107', '#17a2b8', '#28a745', '#6c757d', '#dc3545'];
                    ?>
                    <span style="background: <?= $statusColors[$order['status']] ?>; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">
                        <?= $statusTexts[$order['status']] ?>
                    </span>
                </p>
                <?php if ($order['paid_at']): ?>
                    <p><strong>支付时间：</strong><?= $order['paid_at'] ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="background: #f8f9fa; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            <strong>收货地址：</strong>
            <p><?= htmlspecialchars($address['name']) ?> <?= htmlspecialchars($address['phone']) ?></p>
            <p><?= htmlspecialchars($address['province'] . $address['city'] . $address['district'] . $address['detail']) ?></p>
        </div>
        
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th>商品</th>
                    <th>单价</th>
                    <th>数量</th>
                    <th>小计</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>¥<?= number_format($item['price'] / 100, 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>¥<?= number_format(($item['price'] * $item['quantity']) / 100, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">合计：</td>
                    <td style="color: #dc3545; font-weight: bold;">¥<?= number_format($order['pay_amount'] / 100, 2) ?></td>
                </tr>
            </tfoot>
        </table>
        
        <?php if ($order['remark']): ?>
            <div style="margin-top: 1rem;">
                <strong>备注：</strong>
                <p><?= htmlspecialchars($order['remark']) ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($order['cancel_reason']): ?>
            <div style="margin-top: 1rem; color: #dc3545;">
                <strong>取消原因：</strong>
                <p><?= htmlspecialchars($order['cancel_reason']) ?></p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 1.5rem;">
            <a href="/orders" class="btn">返回列表</a>
            <?php if ($order['status'] == 0): ?>
                <form action="/orders/<?= $order['id'] ?>/cancel" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">取消订单</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
