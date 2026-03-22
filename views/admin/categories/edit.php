<?php
$pageTitle = '编辑分类';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">编辑分类</h1>

<div class="card">
    <div class="card-body">
        <form action="/admin/categories/<?= $category['id'] ?>/update" method="POST">
            <div class="form-group">
                <label>分类名称</label>
                <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>分类描述</label>
                <textarea name="description" rows="2"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label>排序</label>
                <input type="number" name="sort" value="<?= $category['sort'] ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">保存修改</button>
            <a href="/admin/categories" class="btn" style="color: #666;">取消</a>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/admin.php';
