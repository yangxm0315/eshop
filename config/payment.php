<?php
/**
 * 支付配置文件
 */

return [
    'wechat' => [
        // 微信公众号 AppID
        'app_id' => 'wx8888888888888888',
        // 微信支付商户号
        'mch_id' => '1234567890',
        // 微信支付 API 密钥
        'api_key' => 'your_32_character_api_key_here',
        // 支付结果回调地址
        'notify_url' => 'http://localhost:8000/payment/wechat/notify',
    ],

    'alipay' => [
        // 支付宝 AppID
        'app_id' => '2021000000000000',
        // 支付宝私钥
        'private_key' => '',
        // 支付宝公钥
        'public_key' => '',
        // 异步通知地址
        'notify_url' => 'http://localhost:8000/payment/alipay/notify',
        // 同步返回地址
        'return_url' => 'http://localhost:8000/payment/alipay/return',
    ],
];
