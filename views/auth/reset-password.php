<?php
$pageTitle = '重置密码';
ob_start();
?>

<div style="max-width: 400px; margin: 0 auto;">
    <h1 style="margin-bottom: 1.5rem; text-align: center;">重置密码</h1>
    
    <form action="/reset-password" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        
        <div class="form-group">
            <label for="email">邮箱</label>
            <input type="email" id="email" name="email" required autofocus>
        </div>
        
        <div class="form-group">
            <label for="password">新密码</label>
            <input type="password" id="password" name="password" required minlength="6">
        </div>
        
        <div class="form-group">
            <label for="password_confirmation">确认密码</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">重置密码</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
