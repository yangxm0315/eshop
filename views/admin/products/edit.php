<?php
$pageTitle = '编辑商品';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">编辑商品</h1>

<div class="card">
    <div class="card-body">
        <form action="/admin/products/<?= $product['id'] ?>/update" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>商品名称</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>所属分类</label>
                <select name="category_id">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>价格（元）</label>
                <input type="number" name="price" step="0.01" value="<?= number_format($product['price'] / 100, 2) ?>" required>
            </div>
            
            <div class="form-group">
                <label>库存</label>
                <input type="number" name="stock" value="<?= $product['stock'] ?>" required>
            </div>
            
            <div class="form-group">
                <label>商品描述</label>
                <textarea name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label>商品详情</label>
                <textarea name="content" rows="5"><?= htmlspecialchars($product['content']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label>主图</label>
                <?php if ($product['main_image']): ?>
                    <img src="<?= htmlspecialchars($product['main_image']) ?>" style="max-width: 200px; margin-bottom: 0.5rem;">
                <?php endif; ?>
                <input type="file" name="main_image" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_show" value="1" <?= $product['is_show'] ? 'checked' : '' ?>> 上架销售
                </label>
            </div>
            
            <div class="form-group">
                <label>排序</label>
                <input type="number" name="sort" value="<?= $product['sort'] ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">保存修改</button>
            <a href="/admin/products" class="btn" style="color: #666;">取消</a>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/admin.php';
