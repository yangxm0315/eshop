<?php
$pageTitle = '商品列表';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">商品列表</h1>

<div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
    <form action="/products" method="GET" style="display: flex; gap: 0.5rem;">
        <input type="text" name="keyword" placeholder="搜索商品" value="<?= htmlspecialchars($keyword ?? '') ?>" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; width: 250px;">
        <button type="submit" class="btn btn-primary">搜索</button>
    </form>
    
    <?php if (!empty($categories)): ?>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <span>分类:</span>
            <a href="/products" class="btn <?= !$currentCategoryId ? 'btn-primary' : '' ?>" style="background: <?= !$currentCategoryId ? '#007bff' : '#fff' ?>; color: <?= !$currentCategoryId ? '#fff' : '#333' ?>;">全部</a>
            <?php foreach ($categories as $cat): ?>
                <a href="/products?category_id=<?= $cat['id'] ?>" class="btn" style="background: <?= $currentCategoryId == $cat['id'] ? '#007bff' : '#fff' ?>; color: <?= $currentCategoryId == $cat['id'] ? '#fff' : '#333' ?>;"><?= htmlspecialchars($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="grid grid-4">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($product['main_image'] ?: '/placeholder.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="card-img">
                <div class="card-body">
                    <h3 style="font-size: 1rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($product['name']) ?></h3>
                    <p style="color: #dc3545; font-weight: bold; font-size: 1.2rem;">¥<?= number_format($product['price'] / 100, 2) ?></p>
                    <p style="color: #666; font-size: 0.9rem;">库存：<?= $product['stock'] ?? 0 ?></p>
                    <a href="/products/<?= $product['id'] ?>" class="btn btn-primary" style="width: 100%; text-align: center; margin-top: 0.5rem;">查看详情</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>暂无商品</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
