# 支付功能说明

## 功能概述

已为订单系统添加完整的支付功能，支持微信扫码支付（模拟）。

## 文件结构

```
eshop/
├── config/
│   └── payment.php              # 支付配置文件
├── src/
│   ├── Controllers/
│   │   └── PaymentController.php # 支付控制器
│   ├── Payment/
│   │   └── WechatPay.php        # 微信支付类
│   └── Helpers/
│       └── functions.php        # 辅助函数（添加 XML 处理）
└── views/
    ├── orders/
    │   ├── index.php            # 订单列表页（添加支付按钮）
    │   └── show.php             # 订单详情页（添加支付按钮）
    └── payment/
        ├── show.php             # 支付页面（含二维码）
        └── success.php          # 支付成功页
```

## 路由配置

| 路由 | 方法 | 说明 |
|------|------|------|
| `/payment/{orderId}` | GET | 支付页面 |
| `/payment/wechat/create/{orderId}` | POST | 创建微信支付二维码 |
| `/payment/wechat/query` | GET | 查询支付状态 |
| `/payment/wechat/notify` | POST | 微信支付回调 |
| `/payment/success/{orderId}` | GET | 支付成功页 |

## 功能特点

### 1. 订单详情页支付按钮
- 待支付订单显示"立即支付"按钮
- 已支付订单显示对应状态（待发货、已发货、已完成）

### 2. 订单列表页支付按钮
- 待支付订单显示"支付"按钮
- 可直接从列表页进入支付

### 3. 支付页面功能
- 显示微信支付二维码
- 自动轮询支付状态（每 3 秒）
- 支付倒计时（30 分钟）
- 支付成功后自动跳转

### 4. 支付成功页面
- 显示订单信息
- 显示支付金额和时间
- 提供"查看订单"和"继续购物"按钮

## 配置说明

编辑 `config/payment.php` 配置支付参数：

```php
'wechat' => [
    'app_id' => '你的微信 AppID',
    'mch_id' => '你的商户号',
    'api_key' => '你的 API 密钥',
    'notify_url' => 'http://your-domain.com/payment/wechat/notify',
],
```

## 接入真实微信支付

当前实现使用模拟模式，接入真实支付需要：

1. 申请微信支付商户账号
2. 获取 AppID 和 MchID
3. 配置 API 密钥
4. 在 `WechatPay.php` 中实现真实的统一下单 API 调用
5. 配置 HTTPS 回调地址
6. 实现签名验证

## 测试步骤

1. 登录用户账号
2. 创建订单（结算页面提交）
3. 在订单列表或详情页点击"支付"按钮
4. 扫码支付（模拟）
5. 等待支付成功跳转

## 注意事项

1. 当前为模拟支付，实际项目需要接入真实 API
2. 支付回调需要配置公网可访问的 URL
3. 建议使用 HTTPS 保障支付安全
4. 支付状态轮询间隔建议 3-5 秒
5. 订单支付过期时间为 30 分钟
