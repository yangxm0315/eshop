<?php
$pageTitle = '订单确认';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">订单确认</h1>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-body">
                <h3 style="margin-bottom: 1rem;">收货地址</h3>
                <form action="/orders" method="POST">
                    <?php if (!empty($addresses)): ?>
                        <?php foreach ($addresses as $addr): ?>
                            <label style="display: block; padding: 1rem; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 0.5rem; cursor: pointer;">
                                <input type="radio" name="address_id" value="<?= $addr['id'] ?>" <?= $defaultAddress && $defaultAddress['id'] == $addr['id'] ? 'checked' : '' ?> style="margin-right: 0.5rem;">
                                <strong><?= htmlspecialchars($addr['name']) ?></strong> <?= htmlspecialchars($addr['phone']) ?>
                                <br>
                                <span style="color: #666;"><?= htmlspecialchars($addr['province'] . $addr['city'] . $addr['district'] . $addr['detail']) ?></span>
                                <?php if ($addr['is_default']): ?>
                                    <span style="background: #28a745; color: white; padding: 0.1rem 0.5rem; border-radius: 4px; font-size: 0.8rem; margin-left: 0.5rem;">默认</span>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #666;">暂无收货地址，请先在个人中心添加</p>
                        <a href="/profile" class="btn btn-primary">添加地址</a>
                    <?php endif; ?>
                    
                    <div class="form-group" style="margin-top: 1rem;">
                        <label>备注</label>
                        <textarea name="remark" rows="2" placeholder="选填：订单备注信息"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 1rem;">提交订单</button>
                </form>
            </div>
        </div>
    </div>
    
    <div>
        <div class="card">
            <div class="card-body">
                <h3 style="margin-bottom: 1rem;">商品清单</h3>
                <?php foreach ($cartItems as $item): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid #eee;">
                        <div>
                            <span><?= htmlspecialchars($item['name']) ?></span>
                            <span style="color: #666;"> x<?= $item['quantity'] ?></span>
                        </div>
                        <span>¥<?= number_format($item['subtotal'] / 100, 2) ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div style="border-top: 2px solid #333; margin-top: 1rem; padding-top: 1rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold;">
                        <span>合计：</span>
                        <span style="color: #dc3545;">¥<?= number_format($totalAmount / 100, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="/cart" style="display: inline-block; margin-top: 1rem; color: #007bff;">&larr; 返回购物车</a>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
