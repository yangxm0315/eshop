# 调试模式说明

## 服务器状态

- **服务地址**: http://localhost:8000
- **PHP 版本**: 8.4.4
- **状态**: 运行中

## 调试配置

### 错误报告
已在 `public/index.php` 中启用：
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### 数据库配置
配置文件：`config/database.php`

当前使用：SQLite
- 数据库文件：`database/database.sqlite`

如需切换到 MySQL，编辑 `src/Core/Database.php` 取消注释 MySQL 配置。

## 路由配置

| 路由 | 文件 |
|------|------|
| 前台路由 | routes/web.php |
| 认证路由 | routes/auth.php |
| 后台路由 | routes/admin.php |

## 测试账户

### 管理员
- 邮箱：admin@example.com
- 密码：admin123

### 普通用户
- 邮箱：user@example.com
- 密码：user123

## 访问地址

- **前台首页**: http://localhost:8000
- **后台管理**: http://localhost:8000/admin
- **登录页面**: http://localhost:8000/login

## 日志查看

运行以下命令查看实时日志：
```bash
# 查看服务器日志
tail -f /path/to/storage/logs/laravel.log

# 或者查看 PHP 内置服务器输出
# 服务器运行在前台时直接显示
```

## 常用调试命令

```bash
# 重新初始化数据库
php database/migrate.php

# 检查数据库连接
php -r "require 'config/database.php'; print_r(require 'config/database.php');"

# 清理 Session
php -r "session_start(); session_destroy(); echo 'Session cleared';"
```
