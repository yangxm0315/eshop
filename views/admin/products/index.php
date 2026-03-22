<?php
$pageTitle = '商品管理';
ob_start();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1>商品管理</h1>
    <a href="/admin/products/create" class="btn btn-primary">添加商品</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>图片</th>
            <th>名称</th>
            <th>分类</th>
            <th>价格</th>
            <th>库存</th>
            <th>销量</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= $product['id'] ?></td>
                <td><img src="<?= htmlspecialchars($product['main_image'] ?: '/placeholder.jpg') ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['category_name'] ?? '-') ?></td>
                <td>¥<?= number_format($product['price'] / 100, 2) ?></td>
                <td><?= $product['stock'] ?></td>
                <td><?= $product['sales'] ?></td>
                <td>
                    <span style="background: <?= $product['is_show'] ? '#28a745' : '#dc3545' ?>; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">
                        <?= $product['is_show'] ? '上架' : '下架' ?>
                    </span>
                </td>
                <td>
                    <a href="/admin/products/<?= $product['id'] ?>/edit" class="btn btn-warning" style="padding: 0.25rem 0.5rem;">编辑</a>
                    <form action="/admin/products/<?= $product['id'] ?>/toggle" method="POST" style="display: inline;">
                        <button type="submit" class="btn btn-<?= $product['is_show'] ? 'secondary' : 'success' ?>" style="padding: 0.25rem 0.5rem;"><?= $product['is_show'] ? '下架' : '上架' ?></button>
                    </form>
                    <form action="/admin/products/<?= $product['id'] ?>/delete" method="POST" style="display: inline;" onsubmit="return confirm('确定删除？')">
                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem;">删除</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/admin.php';
