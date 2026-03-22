<?php
/**
 * 数据库迁移脚本
 * 运行：php database/migrate.php
 */

$db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "开始迁移数据库...\n";

// 用户表
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role INTEGER DEFAULT 0,
    avatar VARCHAR(255),
    phone VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
echo "创建 users 表...\n";

// 分类表
$db->exec("CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    sort INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
echo "创建 categories 表...\n";

// 商品表
$db->exec("CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price INTEGER NOT NULL,
    stock INTEGER DEFAULT 0,
    sales INTEGER DEFAULT 0,
    main_image VARCHAR(255),
    content TEXT,
    is_show INTEGER DEFAULT 1,
    sort INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
)");
echo "创建 products 表...\n";

// 购物车表
$db->exec("CREATE TABLE IF NOT EXISTS cart (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)");
echo "创建 cart 表...\n";

// 地址表
$db->exec("CREATE TABLE IF NOT EXISTS addresses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    province VARCHAR(50),
    city VARCHAR(50),
    district VARCHAR(50),
    detail VARCHAR(255),
    is_default INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");
echo "创建 addresses 表...\n";

// 订单表
$db->exec("CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_no VARCHAR(50) UNIQUE NOT NULL,
    user_id INTEGER NOT NULL,
    address_id INTEGER NOT NULL,
    total_amount INTEGER NOT NULL,
    pay_amount INTEGER NOT NULL,
    status INTEGER DEFAULT 0,
    remark TEXT,
    paid_at DATETIME,
    shipped_at DATETIME,
    completed_at DATETIME,
    cancelled_at DATETIME,
    cancel_reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (address_id) REFERENCES addresses(id)
)");
echo "创建 orders 表...\n";

// 订单商品表
$db->exec("CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    price INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)");
echo "创建 order_items 表...\n";

// 创建管理员账户
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$db->exec("INSERT OR IGNORE INTO users (id, name, email, password, role) VALUES (1, '管理员', 'admin@example.com', '$adminPassword', 1)");
echo "创建管理员账户 (admin@example.com / admin123)...\n";

// 创建测试分类
$db->exec("INSERT OR IGNORE INTO categories (id, name, description, sort) VALUES (1, '电子产品', '手机、电脑等', 1)");
$db->exec("INSERT OR IGNORE INTO categories (id, name, description, sort) VALUES (2, '服装', '男装、女装', 2)");
$db->exec("INSERT OR IGNORE INTO categories (id, name, description, sort) VALUES (3, '食品', '零食、生鲜', 3)");
echo "创建测试分类...\n";

// 创建测试商品
$testProducts = [
    ['iPhone 15 Pro', '苹果手机', 799900, 100, 1],
    ['MacBook Pro', '苹果笔记本', 1499900, 50, 1],
    ['T 恤', '纯棉短袖', 9900, 500, 2],
    ['牛仔裤', '休闲长裤', 19900, 300, 2],
];

foreach ($testProducts as $i => $product) {
    $db->exec("INSERT OR IGNORE INTO products (id, category_id, name, description, price, stock, sales, is_show) 
               VALUES ($i+1, " . ($product[4] == 1 ? 1 : 2) . ", '{$product[0]}', '{$product[1]}', {$product[2]}, {$product[3]}, 0, 1)");
}
echo "创建测试商品...\n";

echo "\n迁移完成！\n";
echo "管理员账户：admin@example.com / admin123\n";
