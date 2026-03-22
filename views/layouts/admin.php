<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? '管理后台' ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; background: #f5f5f5; }
        .wrapper { display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 250px; background: #343a40; color: #fff; padding: 1rem 0; }
        .sidebar h2 { padding: 0 1.5rem; margin-bottom: 1.5rem; font-size: 1.25rem; }
        .sidebar a { display: block; padding: 0.75rem 1.5rem; color: #adb5bd; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background: #495057; color: #fff; }
        
        /* Main */
        .main { flex: 1; padding: 1.5rem; }
        .container { max-width: 1400px; margin: 0 auto; }
        
        /* Flash messages */
        .flash { padding: 1rem; margin: 1rem 0; border-radius: 4px; }
        .flash.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .flash.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Cards */
        .card { background: #fff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-body { padding: 1.5rem; }
        
        /* Buttons */
        .btn { display: inline-block; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-primary:hover { background: #0056b3; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-warning { background: #ffc107; color: #333; }
        
        /* Forms */
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; font-size: 0.9rem; }
        
        /* Table */
        table { width: 100%; background: #fff; border-radius: 4px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; }
        
        /* Grid */
        .grid { display: grid; gap: 1.5rem; }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
    </style>
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h2>管理后台</h2>
            <a href="/admin" class="<?= ($_SERVER['REQUEST_URI'] === '/admin') ? 'active' : '' ?>">控制台</a>
            <a href="/admin/products">商品管理</a>
            <a href="/admin/categories">分类管理</a>
            <a href="/admin/orders">订单管理</a>
            <a href="/">返回前台</a>
            <form action="/logout" method="POST">
                <button type="submit" style="width: 100%; background: none; border: none; color: #adb5bd; padding: 0.75rem 1.5rem; text-align: left; cursor: pointer;">退出登录</button>
            </form>
        </aside>
        
        <main class="main">
            <div class="container">
                <?php if (isset($_SESSION['_flash_success'])): ?>
                    <div class="flash success"><?= htmlspecialchars($_SESSION['_flash_success']) ?></div>
                    <?php unset($_SESSION['_flash_success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['_flash_error'])): ?>
                    <div class="flash error"><?= htmlspecialchars($_SESSION['_flash_error']) ?></div>
                    <?php unset($_SESSION['_flash_error']); ?>
                <?php endif; ?>

                <?= $content ?>
            </div>
        </main>
    </div>
</body>
</html>
