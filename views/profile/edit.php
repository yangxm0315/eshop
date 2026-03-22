<?php
$pageTitle = '个人中心';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">个人中心</h1>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <div>
        <div class="card">
            <div class="card-body">
                <h3 style="margin-bottom: 1rem;">基本信息</h3>
                <form action="/profile" method="POST">
                    <div class="form-group">
                        <label for="name">用户名</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">邮箱</label>
                        <input type="email" id="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled style="background: #f8f9fa;">
                        <small style="color: #666;">邮箱不可修改</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">手机号</label>
                        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="avatar">头像 URL</label>
                        <input type="text" id="avatar" name="avatar" value="<?= htmlspecialchars($user['avatar'] ?? '') ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">保存修改</button>
                </form>
            </div>
        </div>
    </div>
    
    <div>
        <div class="card">
            <div class="card-body">
                <h3 style="margin-bottom: 1rem;">收货地址</h3>
                
                <button class="btn btn-primary" onclick="document.getElementById('addressForm').style.display='block'" style="margin-bottom: 1rem;">添加新地址</button>
                
                <div id="addressForm" style="display: none; background: #f8f9fa; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <form action="/profile/address" method="POST">
                        <div class="form-group">
                            <label>姓名</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>手机号</label>
                            <input type="text" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label>省份</label>
                            <input type="text" name="province" required>
                        </div>
                        <div class="form-group">
                            <label>城市</label>
                            <input type="text" name="city" required>
                        </div>
                        <div class="form-group">
                            <label>区县</label>
                            <input type="text" name="district" required>
                        </div>
                        <div class="form-group">
                            <label>详细地址</label>
                            <textarea name="detail" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_default" value="1"> 设为默认地址
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success">保存地址</button>
                        <button type="button" onclick="document.getElementById('addressForm').style.display='none'" class="btn" style="color: #666;">取消</button>
                    </form>
                </div>
                
                <?php if (!empty($addresses)): ?>
                    <?php foreach ($addresses as $addr): ?>
                        <div style="border: 1px solid #ddd; padding: 1rem; border-radius: 4px; margin-bottom: 0.5rem;">
                            <p>
                                <strong><?= htmlspecialchars($addr['name']) ?></strong>
                                <?= htmlspecialchars($addr['phone']) ?>
                                <?php if ($addr['is_default']): ?>
                                    <span style="background: #28a745; color: white; padding: 0.1rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">默认</span>
                                <?php endif; ?>
                            </p>
                            <p style="color: #666;"><?= htmlspecialchars($addr['province'] . $addr['city'] . $addr['district'] . $addr['detail']) ?></p>
                            <div style="margin-top: 0.5rem;">
                                <?php if (!$addr['is_default']): ?>
                                    <form action="/profile/address/<?= $addr['id'] ?>/default" method="POST" style="display: inline;">
                                        <button type="submit" class="btn btn-success" style="padding: 0.25rem 0.5rem; font-size: 0.85rem;">设为默认</button>
                                    </form>
                                <?php endif; ?>
                                <form action="/profile/address/<?= $addr['id'] ?>/delete" method="POST" style="display: inline;">
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.85rem;">删除</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #666; text-align: center; padding: 1rem;">暂无收货地址</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
