<?php
/**
 * 支付配置文件
 */

return [
    'wechat' => [
        'app_id' => 'wxeb087c17e5ca466d',
        // 微信支付商户号
        'mch_id' => '1711114086',
        // 「商户API证书」的「证书序列号」
        'mch_cert_serial_no' => '52EBADB1C74379779C1A061DD53BCEA87EBD9B82',
        // 「微信支付平台证书」的「平台证书序列号, 可以从「微信支付平台证书」文件解析，也可以在 商户平台 -> 账户中心 -> API安全 查询到
        'platform_cert_serial_no' => '7B92D53AE1C7A59A23000626E63D396E53E73B32',
        // 「微信支付公钥」的「微信支付公钥ID, 需要在 商户平台 -> 账户中心 -> API安全 查询
        'platform_public_key_id' => 'PUB_KEY_ID_0117111140862025031900389200002407',
        //「微信支付公钥」
        'platform_public_key_path' => 'file://' . __DIR__ . '/../src/Payment/pub_key.pem',
        // 商户私钥
        'merchant_private_key_path' => 'file://' . __DIR__ . '/../src/Payment/apiclient_key.pem',
        // 「微信支付平台证书」，用来验证微信支付应答的签名
        'platform_cert_path' => 'file://' . __DIR__ . '/../src/Payment/certificate.pem',
        // 支付结果回调地址
        'notify_url' => 'https://eshop.ucontainers.cn/payment/wechat/notify',
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
