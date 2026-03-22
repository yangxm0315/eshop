<?php
$pageTitle = '登录';
ob_start();
?>

<div style="max-width: 400px; margin: 0 auto;">
    <h1 style="margin-bottom: 1.5rem; text-align: center;">用户登录</h1>
    
    <?php if (isset($_SESSION['_flash_errors']) && !empty($_SESSION['_flash_errors'])): ?>
        <div class="flash error">
            <?php foreach ($_SESSION['_flash_errors'] as $field => $errors): ?>
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        <?php unset($_SESSION['_flash_errors']); ?>
    <?php endif; ?>
    
    <form action="/login" method="POST">
        <div class="form-group">
            <label for="email">邮箱</label>
            <input type="email" id="email" name="email" required autofocus>
        </div>
        
        <div class="form-group">
            <label for="password">密码</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">登录</button>
    </form>
    
    <p style="text-align: center; margin-top: 1rem;">
        还没有账号？<a href="/register">立即注册</a>
    </p>
    
    <p style="text-align: center; margin-top: 0.5rem;">
        <a href="/forgot-password">忘记密码？</a>
    </p>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
