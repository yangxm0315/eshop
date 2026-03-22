<?php

return [
    'database' => [
        'sqlite' => __DIR__ . '/../database/database.sqlite',
        'mysql' => [
            'driver'    =>'mysql',
            'host'      => 'localhost',
            'dbname'  => 'eshop',
            'username'  => 'eshop_user',
            'password'  => 'eShop@2026',
            'charset'   => 'utf8mb4',
        ],
    ],
];
