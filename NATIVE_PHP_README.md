# 原生 PHP 电商项目

这是一个使用原生 PHP 实现的电商项目，不依赖任何框架。

## 项目结构

```
eshop/
├── public/                # 网站根目录
│   ├── index.php          # 入口文件
│   ├── .htaccess          # URL 重写规则
│   └── uploads/           # 上传文件目录
├── src/
│   ├── Core/              # 核心类库
│   │   ├── Router.php     # 路由类
│   │   ├── Database.php   # 数据库类
│   │   ├── Session.php    # Session 管理类
│   │   ├── Request.php    # 请求类
│   │   ├── Response.php   # 响应类
│   │   ├── Controller.php # 基础控制器
│   │   └── Model.php      # 基础模型
│   ├── Controllers/       # 控制器
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   ├── OrderController.php
│   │   ├── ProfileController.php
│   │   ├── Admin/         # 后台控制器
│   │   └── Auth/          # 认证控制器
│   ├── Models/            # 模型类
│   ├── Middleware/        # 中间件
│   └── Helpers/           # 辅助函数
├── views/                 # 视图模板
│   ├── layouts/           # 布局文件
│   ├── home/              # 首页视图
│   ├── products/          # 商品视图
│   ├── cart/              # 购物车视图
│   ├── orders/            # 订单视图
│   ├── profile/           # 用户中心视图
│   ├── auth/              # 认证视图
│   └── admin/             # 后台视图
├── routes/                # 路由文件
│   ├── web.php            # 前台路由
│   ├── auth.php           # 认证路由
│   └── admin.php          # 后台路由
├── config/                # 配置文件
└── database/
    ├── database.sqlite    # SQLite 数据库
    └── migrate.php        # 迁移脚本
```

## 功能特性

- 用户注册/登录/退出
- 商品浏览/搜索
- 购物车管理
- 订单创建/查看/取消
- 个人中心（个人信息、收货地址）
- 后台管理（商品、分类、订单管理）

## 安装步骤

### 1. 确保 PHP 环境

需要 PHP 8.1+ 和 PDO_SQLite 扩展

### 2. 初始化数据库

```bash
php database/migrate.php
```

### 3. 启动内置服务器

```bash
cd public
php -S localhost:8000
```

### 4. 访问网站

浏览器访问：http://localhost:8000

## 默认账户

- 管理员：admin@example.com / admin123

## 核心类说明

### Router - 路由类
```php
// 注册路由
Router::get('/path', [Controller::class, 'method']);
Router::post('/path', [Controller::class, 'method']);

// 生成 URL
Router::route('route_name', ['id' => 1]);
```

### Database - 数据库类
```php
$db = Database::getInstance();
$db->query("SELECT * FROM table");
$db->queryOne("SELECT * FROM table WHERE id = :id", ['id' => 1]);
$db->insert('table', ['column' => 'value']);
$db->update('table', ['column' => 'new_value'], 'id = :id', ['id' => 1]);
$db->delete('table', 'id = :id', ['id' => 1]);
```

### Session - Session 管理类
```php
$session = Session::getInstance();
$session->set('key', 'value');
$session->get('key', 'default');
$session->has('key');
$session->isLoggedIn();
$session->userId();
```

### Model - 基础模型类
```php
class Product extends Model {
    protected string $table = 'products';
    protected array $fillable = ['name', 'price'];
}

// 使用
Product::all();
Product::find(1);
Product::where(['is_show' => 1]);
Product::create(['name' => 'Test']);
```

## 与 Laravel 版本的区别

| 功能 | Laravel | 原生 PHP |
|------|---------|----------|
| 路由 | 功能丰富 | 基础实现 |
| ORM | Eloquent | 简化版 Model |
| 模板 | Blade | 原生 PHP |
| 中间件 | 完善 | 基础实现 |
| 验证 | 完善 | 基础实现 |
| Session | 完善 | 原生 Session |

## 扩展建议

1. 添加更多验证规则
2. 实现文件系统上传
3. 添加邮件发送功能
4. 实现分页功能
5. 添加 Redis 缓存支持
