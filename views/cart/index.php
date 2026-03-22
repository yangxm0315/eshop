<?php
$pageTitle = '购物车';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">购物车</h1>

<?php if (!empty($cartItems)): ?>
    <table style="width: 100%; margin-bottom: 1.5rem;">
        <thead>
            <tr>
                <th>商品</th>
                <th>单价</th>
                <th>数量</th>
                <th>小计</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <img src="<?= htmlspecialchars($item['main_image'] ?: '/placeholder.jpg') ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                            <span><?= htmlspecialchars($item['name']) ?></span>
                        </div>
                    </td>
                    <td>¥<?= number_format($item['price'] / 100, 2) ?></td>
                    <td>
                        <form action="/cart/<?= $item['id'] ?>" method="POST" style="display: flex; gap: 0.5rem;">
                            <input type="hidden" name="_method" value="PUT">
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" style="width: 60px; padding: 0.25rem; border: 1px solid #ddd; border-radius: 4px;">
                            <button type="submit" class="btn btn-primary" style="padding: 0.25rem 0.5rem;">更新</button>
                        </form>
                    </td>
                    <td style="color: #dc3545; font-weight: bold;">¥<?= number_format($item['subtotal'] / 100, 2) ?></td>
                    <td>
                        <form action="/cart/<?= $item['id'] ?>" method="POST" style="display: inline;">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem;">删除</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; background: #f8f9fa; border-radius: 8px;">
        <div>
            <span style="font-size: 1.2rem;">总计：</span>
            <span style="color: #dc3545; font-size: 1.5rem; font-weight: bold;">¥<?= number_format($total / 100, 2) ?></span>
        </div>
        <div style="display: flex; gap: 1rem;">
            <form action="/cart/clear" method="POST">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-danger">清空购物车</button>
            </form>
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['id']): ?>
                <a href="/checkout" class="btn btn-success">去结算</a>
            <?php else: ?>
                <a href="/login" class="btn btn-success">登录后结算</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div style="text-align: center; padding: 3rem;">
        <p style="font-size: 1.2rem; color: #666; margin-bottom: 1rem;">购物车空空如也</p>
        <a href="/products" class="btn btn-primary">去逛逛</a>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
