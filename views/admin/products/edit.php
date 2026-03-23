<?php
$pageTitle = '编辑商品';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">编辑商品</h1>

<div class="card">
    <div class="card-body">
        <form action="/admin/products/<?= $product['id'] ?>/update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
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
                <input type="number" name="price" step="0.01" value="<?= $product['price'] ?>" required>
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
                    <img src="<?= htmlspecialchars($product['main_image']) ?>" style="max-width: 200px; margin-bottom: 0.5rem; display: block;">
                <?php endif; ?>
                <input type="file" name="main_image" accept="image/*">
            </div>

            <div class="form-group">
                <label>商品图片</label>
                <?php if (!empty($product['images'])): ?>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                        <?php foreach ($product['images'] as $img): ?>
                            <div style="position: relative;">
                                <img src="<?= htmlspecialchars($img) ?>" style="width: 100px; height: 100px; object-fit: cover; border: 1px solid #ddd;">
                                <button type="button" onclick="deleteImage(this, '<?= htmlspecialchars($img) ?>')" style="position: absolute; top: -8px; right: -8px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; font-size: 14px;">&times;</button>
                                <?php if ($product['main_image'] !== $img): ?>
                                    <button type="button" onclick="setMainImage('<?= $product['id'] ?>', '<?= htmlspecialchars($img) ?>')" style="position: absolute; bottom: -8px; right: -8px; background: #28a745; color: white; border: none; border-radius: 4px; width: 24px; height: 24px; cursor: pointer; font-size: 12px;">设主</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <input type="file" name="images[]" accept="image/*" multiple>
                <small style="color: #666;">可多选，按住 Ctrl/Cmd 选择多张图片</small>
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
?>

<script>
function deleteImage(btn, imagePath) {
    if (!confirm('确定删除这张图片吗？')) return;

    // 找到包含 csrf_token 的表单
    var form = document.querySelector('form');
    var token = form.querySelector('input[name="_token"]');
    var tokenValue = token ? token.value : '';

    fetch('/admin/products/image/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: '_token=' + encodeURIComponent(tokenValue) + '&image_path=' + encodeURIComponent(imagePath)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // 删除整个图片容器
            btn.parentElement.remove();
        } else {
            alert(data.message || '删除失败');
        }
    });
}

function setMainImage(productId, imagePath) {
    var form = document.querySelector('form');
    var token = form.querySelector('input[name="_token"]');
    var tokenValue = token ? token.value : '';

    fetch('/admin/products/' + productId + '/set-main-image', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: '_token=' + encodeURIComponent(tokenValue) + '&image=' + encodeURIComponent(imagePath)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || '设置失败');
        }
    });
}
</script>
