<?php
/**
 * 数据库配置文件
 */

return [
    // SQLite 配置
    'database' => [
        'sqlite' => __DIR__ . '/../database/database.sqlite',

        // MySQL 配置
        'mysql' => [
            'host' => 'localhost',
            'port' => 3306,
            'dbname' => 'eshop',
            'username' => 'eshop_user',
            'password' => 'eShop@2026',
            'charset' => 'utf8mb4',
        ]
    ],

    // 应用配置
    'app' => [
        'debug' => true,
        'url' => 'http://localhost:8000',
        'timezone' => 'Asia/Shanghai',
    ],
];
