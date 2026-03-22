<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? '原生 PHP 电商' ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        
        /* Header */
        header { background: #333; color: #fff; padding: 1rem 0; }
        header nav { display: flex; justify-content: space-between; align-items: center; }
        header .logo { font-size: 1.5rem; font-weight: bold; color: #fff; text-decoration: none; }
        header ul { list-style: none; display: flex; gap: 1.5rem; }
        header a { color: #fff; text-decoration: none; }
        header a:hover { color: #ddd; }
        
        /* Flash messages */
        .flash { padding: 1rem; margin: 1rem 0; border-radius: 4px; }
        .flash.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .flash.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Main content */
        main { padding: 2rem 0; min-height: calc(100vh - 200px); }
        
        /* Footer */
        footer { background: #f8f9fa; padding: 1rem 0; text-align: center; margin-top: 2rem; }
        
        /* Buttons */
        .btn { display: inline-block; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 1rem; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-primary:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-success { background: #28a745; color: #fff; }
        
        /* Forms */
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: #007bff; }
        
        /* Cards */
        .card { border: 1px solid #ddd; border-radius: 4px; overflow: hidden; }
        .card-body { padding: 1rem; }
        .card-img { width: 100%; height: 200px; object-fit: cover; }
        
        /* Grid */
        .grid { display: grid; gap: 1.5rem; }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        
        @media (max-width: 768px) {
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
            header nav { flex-direction: column; gap: 1rem; }
        }
        
        /* Table */
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: 600; }
        tr:hover { background: #f8f9fa; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="/" class="logo">原生 PHP 电商</a>
                <ul>
                    <li><a href="/">首页</a></li>
                    <li><a href="/products">商品</a></li>
                    <li><a href="/cart">购物车</a></li>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['id']): ?>
                        <li><a href="/orders">我的订单</a></li>
                        <li><a href="/profile">个人中心</a></li>
                        <?php if ($_SESSION['user']['role'] === 1): ?>
                            <li><a href="/admin">管理后台</a></li>
                        <?php endif; ?>
                        <li>
                            <form action="/logout" method="POST" style="display:inline;">
                                <button type="submit" style="background:none;border:none;color:#fff;cursor:pointer;">退出</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="/login">登录</a></li>
                        <li><a href="/register">注册</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
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

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> 原生 PHP 电商 - 非 Laravel 实现</p>
        </div>
    </footer>
</body>
</html>
