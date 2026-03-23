<?php

namespace Payment;

// 引入依赖库
require_once('vendor/autoload.php');
use WeChatPay\Builder;
use WeChatPay\Crypto\Rsa;

/**
 * 微信支付模拟类
 * 实际项目中需要接入真正的微信支付 API
 */
class WechatPay
{
    private Builder $client;
    private string $appId;
    private string $mchId;
    private string $merchantCertificateSerial;
    private string $platformCertificateSerial;
    private string $platformPublicKeyId;
    private Rsa $merchantPrivateKeyInstance;
    private Rsa $platformPublicKeyInstance;
    private Rsa $platformCertificateInstance;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/payment.php';
        $this->appId = $config['wechat']['app_id'];
        $this->mchId = $config['wechat']['mch_id'];
        $this->merchantCertificateSerial = $config['wechat']['mch_cert_serial_no'];
        $this->platformCertificateSerial = $config['wechat']['platform_cert_serial_no'];
        $this->platformPublicKeyId = $config['wechat']['platform_public_key_id'];

        $this->merchantPrivateKeyInstance = Rsa::from($config['wechat']['merchant_private_key_path'], Rsa::KEY_TYPE_PUBLIC);
        $this->platformPublicKeyInstance = Rsa::from($config['wechat']['platform_public_key_path'], Ras::KEY_TYPE_PUBLIC);
        $this->platformCertificateInstance = Rsa::from($config['wechat']['platform_cert_path'], Ras::KEY_TYPE_PUBLIC);
        // 构造一个 APIv3 客户端实例
        $this->client = Builder::factory([
            'mchid'      => $this->mchId,
            'serial'     => $this->merchantCertificateSerial,
            'privateKey' => $this->merchantPrivateKeyInstance,
            'certs'      => [
                $this->platformCertificateSerial => $this->platformCertificateInstance,
                $this->platformPublicKeyId       => $this->platformPublicKeyInstance,
            ],
        ]);    
    }

    /**
     * 创建 Native 支付二维码
     * 实际项目需要调用微信统一下单 API
     */
    public function createNativePay(array $orderInfo): array
    {
        // 模拟微信支付的 code_url
        // 实际项目中需要：
        // 1. 构建统一下单请求参数
        // 2. 签名
        // 3. 调用微信 API
        // 4. 解析返回的 code_url

        $params = [
            'appid' => $this->appId,
            'mch_id' => $this->mchId,
            'nonce_str' => $this->createNonceStr(),
            'body' => $orderInfo['body'],
            'out_trade_no' => $orderInfo['out_trade_no'],
            'total_fee' => $orderInfo['total_fee'],
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'notify_url' => $this->getNotifyUrl(),
            'trade_type' => 'NATIVE',
        ];

        try {
            $resp = $instance->chain('v3/pay/transactions/native')->post(['json' => [
                'mchid'         => $this->mchId,
                'appid'         => $this->appId, 
                'description'   => '智箱云网-订阅服务费',               // 商品描述
                'out_trade_no'  => $orderInfo['out_trade_no'],    // 商户订单号
                'notify_url'    => 'https://www.ucontainers.com.cn/wechatpay/pay_notify.php', // 通知地址
                'amount'        => [
                    'total'    => $orderInfo['total_fee'],      // 订单总金额, 单位为分
                    'currency' => 'CNY', // 订单币种
                ]
            ]]);
            
            $data = json_decode((string) $resp->getBody(), true);
            return $data;
            
            $codeUrl = $data['code_url'];
            header('Content-Type: application/json');
            echo json_encode(['code_url' => $codeUrl]);
            
        } catch(\Exception $e) {
            // 进行异常捕获并进行错误判断处理
            echo $e->getMessage(), PHP_EOL;
            if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
                $r = $e->getResponse();
                echo $r->getStatusCode() . ' ' . $r->getReasonPhrase(), PHP_EOL;
                echo (string) $r->getBody(), PHP_EOL, PHP_EOL, PHP_EOL;
            }
            echo $e->getTraceAsString(), PHP_EOL;
        }

        /*
        // 生成签名（模拟）
        $params['sign'] = $this->makeSign($params);

        // 模拟返回的 code_url
        // 真实场景：$result = $this->callWxApi($params);
        $codeUrl = 'wxp://f2f' . base64_encode(json_encode([
            'order_no' => $orderInfo['out_trade_no'],
            'amount' => $orderInfo['total_fee'],
            'time' => time(),
        ]));
        */

        return [
            'success' => true,
            'code_url' => $codeUrl,
            'trade_no' => $orderInfo['out_trade_no'],
        ];
    }

    /**
     * 查询订单支付状态
     */
    public function queryOrder(string $outTradeNo): array
    {
        // 模拟查询，实际项目需要调用微信查询 API
        $db = \Core\Database::getInstance();
        $order = $db->queryOne("SELECT * FROM orders WHERE order_no = :order_no", [
            'order_no' => $outTradeNo
        ]);

        if (!$order) {
            return ['success' => false, 'message' => '订单不存在'];
        }

        if ($order['status'] >= 1) {
            return [
                'success' => true,
                'trade_state' => 'SUCCESS',
                'total_fee' => $order['pay_amount'],
            ];
        }

        return [
            'success' => true,
            'trade_state' => 'NOTPAY',
        ];
    }

    /**
     * 处理支付回调
     */
    public function handleNotify(): array
    {
        // 获取微信回调数据
        $xml = file_get_contents('php://input');
        $data = xml2array($xml);

        // 验证签名
        if (!$this->verifySign($data)) {
            return ['return_code' => 'FAIL', 'return_msg' => '签名失败'];
        }

        // 处理支付成功逻辑
        if ($data['return_code'] === 'SUCCESS' && $data['result_code'] === 'SUCCESS') {
            $this->handlePaySuccess($data);
            return ['return_code' => 'SUCCESS', 'return_msg' => 'OK'];
        }

        return ['return_code' => 'FAIL', 'return_msg' => '支付失败'];
    }

    /**
     * 处理支付成功
     */
    private function handlePaySuccess(array $data): void
    {
        $db = \Core\Database::getInstance();
        $outTradeNo = $data['out_trade_no'];

        $order = $db->queryOne("SELECT * FROM orders WHERE order_no = :order_no", [
            'order_no' => $outTradeNo
        ]);

        if ($order && $order['status'] == 0) {
            $db->update('orders', [
                'status' => 1,
                'paid_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', ['id' => $order['id']]);

            // 记录支付日志
            $db->insert('payment_logs', [
                'order_id' => $order['id'],
                'trade_no' => $data['transaction_id'] ?? '',
                'amount' => $order['pay_amount'],
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * 生成签名
     */
    private function makeSign(array $params): string
    {
        ksort($params);
        $stringToBeSigned = http_build_query($params) . '&key=' . $this->apiKey;
        return strtoupper(md5($stringToBeSigned));
    }

    /**
     * 验证签名
     */
    private function verifySign(array $params): bool
    {
        if (!isset($params['sign'])) {
            return false;
        }
        $sign = $params['sign'];
        unset($params['sign']);
        return $this->makeSign($params) === $sign;
    }

    /**
     * 生成随机字符串
     */
    private function createNonceStr(int $length = 32): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 获取回调 URL
     */
    private function getNotifyUrl(): string
    {
        $config = require __DIR__ . '/../../config/payment.php';
        return $config['wechat']['notify_url'];
    }
}
