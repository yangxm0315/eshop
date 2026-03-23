<?php
$pageTitle = '添加商品';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">添加商品</h1>

<div class="card">
    <div class="card-body">
        <form action="/admin/products" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>商品名称</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>所属分类</label>
                <select name="category_id">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>价格（元）</label>
                <input type="number" name="price" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label>库存</label>
                <input type="number" name="stock" required>
            </div>
            
            <div class="form-group">
                <label>商品描述</label>
                <textarea name="description" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label>商品详情</label>
                <textarea name="content" rows="5"></textarea>
            </div>
            
            <div class="form-group">
                <label>主图</label>
                <input type="file" name="main_image" accept="image/*">
            </div>

            <div class="form-group">
                <label>商品图片（可上传多张）</label>
                <input type="file" name="images[]" accept="image/*" multiple>
                <small style="color: #666;">可多选，按住 Ctrl/Cmd 选择多张图片</small>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_show" value="1" checked> 上架销售
                </label>
            </div>
            
            <div class="form-group">
                <label>排序</label>
                <input type="number" name="sort" value="0">
            </div>
            
            <button type="submit" class="btn btn-primary">保存商品</button>
            <a href="/admin/products" class="btn" style="color: #666;">取消</a>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/admin.php';
