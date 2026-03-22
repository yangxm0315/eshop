<?php
$pageTitle = '分类管理';
ob_start();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1>分类管理</h1>
    <button class="btn btn-primary" onclick="document.getElementById('categoryForm').style.display='block'">添加分类</button>
</div>

<div id="categoryForm" style="display: none; background: #fff; padding: 1.5rem; border-radius: 4px; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 1rem;">添加分类</h3>
    <form action="/admin/categories" method="POST">
        <div class="form-group">
            <label>分类名称</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>分类描述</label>
            <textarea name="description" rows="2"></textarea>
        </div>
        <div class="form-group">
            <label>排序</label>
            <input type="number" name="sort" value="0">
        </div>
        <button type="submit" class="btn btn-primary">保存</button>
        <button type="button" onclick="document.getElementById('categoryForm').style.display='none'" class="btn" style="color: #666;">取消</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>描述</th>
            <th>排序</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= $cat['id'] ?></td>
                <td><?= htmlspecialchars($cat['name']) ?></td>
                <td><?= htmlspecialchars($cat['description'] ?? '-') ?></td>
                <td><?= $cat['sort'] ?></td>
                <td>
                    <a href="/admin/categories/<?= $cat['id'] ?>/edit" class="btn btn-warning" style="padding: 0.25rem 0.5rem;">编辑</a>
                    <form action="/admin/categories/<?= $cat['id'] ?>/delete" method="POST" style="display: inline;" onsubmit="return confirm('确定删除？')">
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
