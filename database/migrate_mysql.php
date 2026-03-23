<?php
/**
 * MySQL 数据库迁移脚本
 * 使用方法：
 * 1. 复制 config.example.php 为 config.php
 * 2. 修改 config.php 中的数据库配置
 * 3. 运行：php database/migrate_mysql.php
 */

// 加载配置
$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    echo "错误：配置文件不存在\n";
    echo "请复制 config.example.php 为 config.php 并配置数据库连接信息\n";
    exit(1);
}
$config = require $configFile;

echo "开始迁移 MySQL 数据库...\n\n";

try {
    // 连接 MySQL 数据库
    $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
    $db = new PDO($dsn, $config['username'], $config['password']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 创建数据库（如果不存在）
    $db->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $db->exec("USE `{$config['database']}`");
    echo "创建/选择数据库：{$config['database']}\n";

    // 禁用外键检查
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");

    // 删除现有表（可选，如果需要重新创建）
    // $db->exec("DROP TABLE IF EXISTS order_items");
    // $db->exec("DROP TABLE IF EXISTS orders");
    // $db->exec("DROP TABLE IF EXISTS addresses");
    // $db->exec("DROP TABLE IF EXISTS cart");
    // $db->exec("DROP TABLE IF EXISTS products");
    // $db->exec("DROP TABLE IF EXISTS categories");
    // $db->exec("DROP TABLE IF EXISTS users");

    // 用户表
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL COMMENT '用户名',
        email VARCHAR(255) UNIQUE NOT NULL COMMENT '邮箱',
        password VARCHAR(255) NOT NULL COMMENT '密码',
        role TINYINT DEFAULT 0 COMMENT '角色：0=普通用户，1=管理员',
        avatar VARCHAR(255) COMMENT '头像',
        phone VARCHAR(20) COMMENT '手机号',
        email_verified_at TIMESTAMP NULL COMMENT '邮箱验证时间',
        remember_token VARCHAR(100) COMMENT '记住我令牌',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表'");
    echo "创建 users 表...\n";

    // 分类表
    $db->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL COMMENT '分类名称',
        description TEXT COMMENT '分类描述',
        parent_id INT UNSIGNED DEFAULT 0 COMMENT '父级 ID',
        sort INT DEFAULT 0 COMMENT '排序',
        icon VARCHAR(255) COMMENT '图标',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_parent_id (parent_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品分类表'");
    echo "创建 categories 表...\n";

    // 商品表
    $db->exec("CREATE TABLE IF NOT EXISTS products (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        category_id INT UNSIGNED COMMENT '分类 ID',
        name VARCHAR(255) NOT NULL COMMENT '商品名称',
        description TEXT COMMENT '商品描述',
        price INT NOT NULL COMMENT '价格（分）',
        stock INT DEFAULT 0 COMMENT '库存',
        sales INT DEFAULT 0 COMMENT '销量',
        main_image VARCHAR(255) COMMENT '主图',
        images JSON COMMENT '图片列表',
        content TEXT COMMENT '商品详情',
        is_show TINYINT DEFAULT 1 COMMENT '是否上架：0=下架，1=上架',
        sort INT DEFAULT 0 COMMENT '排序',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_category_id (category_id),
        INDEX idx_is_show (is_show),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品表'");
    echo "创建 products 表...\n";

    // 商品图片表
    $db->exec("CREATE TABLE IF NOT EXISTS product_images (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        product_id INT UNSIGNED NOT NULL COMMENT '商品 ID',
        image VARCHAR(255) NOT NULL COMMENT '图片路径',
        sort INT DEFAULT 0 COMMENT '排序',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_product_id (product_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品图片表'");
    echo "创建 product_images 表...\n";

    // 购物车表
    $db->exec("CREATE TABLE IF NOT EXISTS cart (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL COMMENT '用户 ID',
        product_id INT UNSIGNED NOT NULL COMMENT '商品 ID',
        quantity INT DEFAULT 1 COMMENT '数量',
        is_checked TINYINT DEFAULT 0 COMMENT '是否选中：0=未选中，1=选中',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uk_user_product (user_id, product_id),
        INDEX idx_user_id (user_id),
        INDEX idx_product_id (product_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='购物车表'");
    echo "创建 cart 表...\n";

    // 收货地址表
    $db->exec("CREATE TABLE IF NOT EXISTS addresses (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL COMMENT '用户 ID',
        name VARCHAR(100) NOT NULL COMMENT '收货人姓名',
        phone VARCHAR(20) NOT NULL COMMENT '收货人电话',
        province VARCHAR(50) COMMENT '省份',
        city VARCHAR(50) COMMENT '城市',
        district VARCHAR(50) COMMENT '区县',
        detail VARCHAR(255) COMMENT '详细地址',
        is_default TINYINT DEFAULT 0 COMMENT '是否默认：0=否，1=是',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='收货地址表'");
    echo "创建 addresses 表...\n";

    // 订单表
    $db->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        order_no VARCHAR(50) UNIQUE NOT NULL COMMENT '订单号',
        user_id INT UNSIGNED NOT NULL COMMENT '用户 ID',
        address_id INT UNSIGNED NOT NULL COMMENT '地址 ID',
        total_amount INT NOT NULL COMMENT '订单总金额（分）',
        pay_amount INT NOT NULL COMMENT '实付金额（分）',
        freight_amount INT DEFAULT 0 COMMENT '运费（分）',
        discount_amount INT DEFAULT 0 COMMENT '优惠金额（分）',
        status TINYINT DEFAULT 0 COMMENT '订单状态：0=待支付，1=待发货，2=待收货，3=已完成，4=已取消',
        remark TEXT COMMENT '订单备注',
        paid_at TIMESTAMP NULL COMMENT '支付时间',
        shipped_at TIMESTAMP NULL COMMENT '发货时间',
        completed_at TIMESTAMP NULL COMMENT '完成时间',
        cancelled_at TIMESTAMP NULL COMMENT '取消时间',
        cancel_reason VARCHAR(255) COMMENT '取消原因',
        express_company VARCHAR(50) COMMENT '快递公司',
        express_no VARCHAR(50) COMMENT '快递单号',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_order_no (order_no),
        INDEX idx_user_id (user_id),
        INDEX idx_status (status),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单表'");
    echo "创建 orders 表...\n";

    // 订单商品表
    $db->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        order_id INT UNSIGNED NOT NULL COMMENT '订单 ID',
        product_id INT UNSIGNED NOT NULL COMMENT '商品 ID',
        product_name VARCHAR(255) NOT NULL COMMENT '商品名称（快照）',
        product_image VARCHAR(255) COMMENT '商品图片（快照）',
        quantity INT NOT NULL COMMENT '数量',
        price INT NOT NULL COMMENT '单价（分）',
        total_price INT NOT NULL COMMENT '总价（分）',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_order_id (order_id),
        INDEX idx_product_id (product_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单商品表'");
    echo "创建 order_items 表...\n";

    // 密码重置令牌表
    $db->exec("CREATE TABLE IF NOT EXISTS password_reset_tokens (
        email VARCHAR(255) PRIMARY KEY,
        token VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='密码重置令牌表'");
    echo "创建 password_reset_tokens 表...\n";

    // 重新启用外键检查
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "\n--- 插入初始数据 ---\n\n";

    // 创建管理员账户
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $db->exec("INSERT INTO users (id, name, email, password, role) VALUES
        (1, '管理员', 'admin@example.com', '$adminPassword', 1),
        (2, '测试用户', 'user@example.com', '" . password_hash('user123', PASSWORD_DEFAULT) . "', 0)");
    echo "创建管理员账户：admin@example.com / admin123\n";
    echo "创建测试用户：user@example.com / user123\n";

    // 创建测试分类
    $db->exec("INSERT INTO categories (id, name, description, sort) VALUES
        (1, '电子产品', '手机、电脑、数码配件', 1),
        (2, '服装', '男装、女装、童装', 2),
        (3, '食品', '零食、生鲜、饮料', 3),
        (4, '家居', '家具、家纺、厨具', 4)");
    echo "创建测试分类...\n";

    // 创建测试商品
    $db->exec("INSERT INTO products (category_id, name, description, price, stock, sales, is_show) VALUES
        (1, 'iPhone 15 Pro', '苹果手机，A17 Pro 芯片，钛金属边框', 799900, 100, 0, 1),
        (1, 'MacBook Pro 14', '苹果笔记本，M3 Pro 芯片，18GB 内存', 1499900, 50, 0, 1),
        (1, 'AirPods Pro 2', '苹果无线耳机，主动降噪', 189900, 200, 0, 1),
        (2, '纯棉 T 恤', '纯棉短袖，舒适透气，多色可选', 9900, 500, 0, 1),
        (2, '牛仔裤', '休闲长裤，修身版型', 19900, 300, 0, 1),
        (2, '运动鞋', '轻便跑鞋，缓震舒适', 29900, 150, 0, 1),
        (3, '坚果大礼包', '混合坚果 500g，营养健康', 9900, 1000, 0, 1),
        (3, '进口车厘子', '智利车厘子 5kg，新鲜水果', 29900, 200, 0, 1),
        (4, '四件套', '纯棉床上用品，简约风格', 19900, 100, 0, 1),
        (4, '不粘锅', '30cm 炒锅，少油烟', 15900, 150, 0, 1)");
    echo "创建测试商品...\n";

    echo "\n========================================\n";
    echo "迁移完成！\n";
    echo "========================================\n";
    echo "数据库：{$config['database']}\n";
    echo "管理员账户：admin@example.com / admin123\n";
    echo "测试用户：user@example.com / user123\n";
    echo "========================================\n";

} catch (PDOException $e) {
    echo "\n错误：" . $e->getMessage() . "\n";
    echo "\n请检查:\n";
    echo "1. MySQL 服务是否启动\n";
    echo "2. 数据库配置是否正确\n";
    echo "3. 数据库用户是否有足够权限\n";
    exit(1);
}
