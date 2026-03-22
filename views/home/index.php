<?php
$pageTitle = '首页';
ob_start();
?>

<div class="hero" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4rem 2rem; text-align: center; border-radius: 8px; margin-bottom: 2rem;">
    <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">欢迎来到原生 PHP 电商</h1>
    <p style="font-size: 1.2rem; opacity: 0.9;">无需框架，纯粹的原生 PHP 实现</p>
    <a href="/products" class="btn" style="background: white; color: #667eea; margin-top: 1.5rem; display: inline-block;">浏览商品</a>
</div>

<h2 style="margin-bottom: 1.5rem;">推荐商品</h2>
<div class="grid grid-4">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($product['main_image'] ?: '/placeholder.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="card-img">
                <div class="card-body">
                    <h3 style="font-size: 1rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($product['name']) ?></h3>
                    <p style="color: #dc3545; font-weight: bold; font-size: 1.2rem;">¥<?= number_format($product['price'] / 100, 2) ?></p>
                    <p style="color: #666; font-size: 0.9rem;">销量：<?= $product['sales'] ?? 0 ?></p>
                    <a href="/products/<?= $product['id'] ?>" class="btn btn-primary" style="width: 100%; text-align: center; margin-top: 0.5rem;">查看详情</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>暂无商品</p>
    <?php endif; ?>
</div>

<h2 style="margin: 2rem 0 1.5rem;">商品分类</h2>
<div class="grid grid-4">
    <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $category): ?>
            <a href="/products?category_id=<?= $category['id'] ?>" style="text-decoration: none;">
                <div class="card" style="text-align: center;">
                    <div class="card-body">
                        <h3 style="font-size: 1rem;"><?= htmlspecialchars($category['name']) ?></h3>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
