<?php
$pageTitle = '注册';
ob_start();
?>

<div style="max-width: 400px; margin: 0 auto;">
    <h1 style="margin-bottom: 1.5rem; text-align: center;">用户注册</h1>
    
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
    
    <form action="/register" method="POST">
        <div class="form-group">
            <label for="name">用户名</label>
            <input type="text" id="name" name="name" required autofocus>
        </div>
        
        <div class="form-group">
            <label for="email">邮箱</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">密码</label>
            <input type="password" id="password" name="password" required minlength="6">
        </div>
        
        <div class="form-group">
            <label for="password_confirmation">确认密码</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">注册</button>
    </form>
    
    <p style="text-align: center; margin-top: 1rem;">
        已有账号？<a href="/login">立即登录</a>
    </p>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
