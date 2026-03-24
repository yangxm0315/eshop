<?php
/**
 * 支付配置文件
 */

return [
    'wechat' => [
        // 微信公众号 AppID
        'app_id' => '',
        // 微信支付商户号
        'mch_id' => '',
        // 「商户API证书」的「证书序列号」
        'mch_cert_serial_no' => '',
        // 「微信支付平台证书」的「平台证书序列号, 可以从「微信支付平台证书」文件解析，也可以在 商户平台 -> 账户中心 -> API安全 查询到
        'platform_cert_serial_no' => '',
        // 「微信支付公钥」的「微信支付公钥ID, 需要在 商户平台 -> 账户中心 -> API安全 查询
        'platform_public_key_id' => '',
        //「微信支付公钥」
        'platform_public_key_path' => 'file://pub_key.pem',
        // 商户私钥
        'merchant_private_key_path' => 'file://apiclient_key.pem',
        // 「微信支付平台证书」，用来验证微信支付应答的签名
        'platform_cert_path' => 'file://certificate.pem',
        // 支付结果回调地址
        'notify_url' => 'https://www.*****.cn/payment/wechat/notify',
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
