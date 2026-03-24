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
    private string $appId;
    private string $mchId;
    private string $notify_url;

    private string $mchCerSerial;
    private string $platformCertificateSerial;
    private string $platformPublicKeyId;

    private string $mchPrivateKeyPath;
    private string $platformCertificatePath;
    private string $platformPublicKeyPath;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/payment.php';
        $this->appId = $config['wechat']['app_id'];
        $this->mchId = $config['wechat']['mch_id'];
        $this->notify_url = $config['wechat']['notify_url'];

        $this->mchCerSerial = $config['wechat']['mch_cert_serial_no'];
        $this->platformCertificateSerial = $config['wechat']['platform_cert_serial_no'];
        $this->platformPublicKeyId = $config['wechat']['platform_public_key_id'];

        $this->mchPrivateKeyPath = $config['wechat']['merchant_private_key_path'];
        $this->platformCertificatePath = $config['wechat']['platform_cert_path'];
        $this->platformPublicKeyPath = $config['wechat']['platform_public_key_path'];
    }

    /**
     * 创建 Native 支付二维码
     * 实际项目需要调用微信统一下单 API
     */
    public function createNativePay(array $orderInfo): array
    {
        // 从本地文件中加载「商户API私钥」，「商户API私钥」会用来生成请求的签名
        $merchantPrivateKey = Rsa::from($this->mchPrivateKeyPath,Rsa::KEY_TYPE_PRIVATE);
        $platformCertificateKey = Rsa::from($this->platformCertificatePath, Rsa::KEY_TYPE_PUBLIC);
        $platformPublicKey = Rsa::from($this->platformPublicKeyPath, Rsa::KEY_TYPE_PUBLIC);
        // 构造一个 APIv3 客户端实例
        $client = Builder::factory([
            'mchid'      => $this->mchId,
            'serial'     => $this->mchCerSerial,
            'privateKey' => $merchantPrivateKey,
            'certs'      => [
                $this->platformCertificateSerial => $platformCertificateKey,
                $this->platformPublicKeyId => $platformPublicKey,
            ],
        ]);

        // 发送请求, 统一下单
        try {
            $resp = $client->chain('v3/pay/transactions/native')->post(['json' => [
                'mchid'         => $this->mchId,
                'appid'         => $this->appId, 
                'description'   => '智箱云网-订阅服务费',              // 商品描述
                'out_trade_no'  => $orderInfo['out_trade_no'],      // 商户订单号
                'notify_url'    => $this->notify_url,               // 通知地址
                'amount'        => [
                    'total'    => $orderInfo['total_fee'],          // 订单总金额, 单位为分
                    'currency' => 'CNY',                            // 订单币种
                ]
            ]]);

            $data = json_decode((string) $resp->getBody(), true);
            $codeUrl = $data['code_url'];
            return [
                'success' => true,
                'code_url' => $codeUrl,
                'trade_no' => $orderInfo['out_trade_no'],
            ];
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
        // 模拟微信支付的 code_url
        $codeUrl = 'wxp://f2f' . base64_encode(json_encode([
            'order_no' => $orderInfo['out_trade_no'],
            'amount' => $orderInfo['total_fee'],
            'time' => time(),
        ]));
        */
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
