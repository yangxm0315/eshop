<?php $pageTitle = '404 - 页面不存在'; ob_start(); ?>
<div style="text-align: center; padding: 4rem;">
    <h1 style="font-size: 4rem; color: #333;">404</h1>
    <p style="font-size: 1.5rem; color: #666; margin: 1rem 0;">页面不存在</p>
    <a href="/" class="btn btn-primary">返回首页</a>
</div>
<?php $content = ob_get_clean(); include BASE_PATH . '/views/layouts/app.php'; ?>
