<?php
$pageTitle = $product['name'];
ob_start();
?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <div>
        <img src="<?= htmlspecialchars($product['main_image'] ?: '/placeholder.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 100%; border-radius: 8px;">
    </div>
    
    <div>
        <h1 style="margin-bottom: 1rem;"><?= htmlspecialchars($product['name']) ?></h1>
        <p style="color: #dc3545; font-size: 2rem; font-weight: bold; margin-bottom: 1rem;">¥<?= number_format($product['price'] / 100, 2) ?></p>
        <p style="color: #666; margin-bottom: 1rem;">库存：<?= $product['stock'] ?? 0 ?></p>
        <p style="color: #666; margin-bottom: 1rem;">销量：<?= $product['sales'] ?? 0 ?></p>
        
        <div style="margin: 2rem 0;">
            <h3 style="margin-bottom: 0.5rem;">商品描述</h3>
            <p style="color: #333; line-height: 1.8;"><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>
        </div>
        
        <?php if ($product['stock'] > 0): ?>
            <form action="/cart/<?= $product['id'] ?>/add" method="POST" style="display: flex; gap: 1rem; align-items: center;">
                <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" style="width: 80px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                <button type="submit" class="btn btn-primary">加入购物车</button>
            </form>
        <?php else: ?>
            <button class="btn" disabled style="background: #ccc; cursor: not-allowed;">缺货</button>
        <?php endif; ?>
        
        <a href="/products" style="display: inline-block; margin-top: 1rem; color: #007bff;">&larr; 返回列表</a>
    </div>
</div>

<div style="margin-top: 2rem;">
    <h3>商品详情</h3>
    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-top: 1rem;">
        <?= nl2br(htmlspecialchars($product['content'] ?? '')) ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
