<?php
$pageTitle = '忘记密码';
ob_start();
?>

<div style="max-width: 400px; margin: 0 auto;">
    <h1 style="margin-bottom: 1.5rem; text-align: center;">忘记密码</h1>
    
    <p style="color: #666; margin-bottom: 1.5rem; text-align: center;">
        请输入您的邮箱，我们将发送重置密码链接
    </p>
    
    <form action="/forgot-password" method="POST">
        <div class="form-group">
            <label for="email">邮箱</label>
            <input type="email" id="email" name="email" required autofocus>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">发送重置链接</button>
    </form>
    
    <p style="text-align: center; margin-top: 1rem;">
        <a href="/login">返回登录</a>
    </p>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
