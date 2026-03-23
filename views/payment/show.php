<?php
$pageTitle = '订单支付';
ob_start();
?>

<style>
    .payment-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 2rem 0;
    }
    .payment-methods {
        display: flex;
        gap: 1rem;
        margin: 2rem 0;
    }
    .payment-method {
        flex: 1;
        border: 2px solid #ddd;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .payment-method:hover {
        border-color: #28a745;
        background: #f8fff9;
    }
    .payment-method.active {
        border-color: #28a745;
        background: #f8fff9;
    }
    .payment-method img {
        width: 60px;
        height: 60px;
        margin-bottom: 0.5rem;
    }
    .payment-method-name {
        font-weight: 500;
        font-size: 1.1rem;
    }
    .qrcode-container {
        text-align: center;
        padding: 2rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin-top: 1rem;
    }
    .qrcode-container img {
        width: 200px;
        height: 200px;
        border: 1px solid #ddd;
        padding: 10px;
        background: white;
    }
    .order-summary {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-top: 1rem;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    .summary-total {
        border-top: 1px solid #ddd;
        padding-top: 1rem;
        margin-top: 1rem;
        font-size: 1.2rem;
        font-weight: bold;
        color: #dc3545;
    }
    .payment-status {
        text-align: center;
        padding: 2rem;
    }
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #28a745;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .countdown {
        text-align: center;
        color: #dc3545;
        font-weight: 500;
        margin-top: 1rem;
    }
</style>

<div class="payment-container">
    <h1 style="margin-bottom: 0.5rem;">订单支付</h1>
    <p style="color: #666; margin-bottom: 2rem;">订单号：<?= htmlspecialchars($order['order_no']) ?></p>

    <div class="order-summary">
        <div class="summary-row">
            <span>订单金额</span>
            <span>¥<?= number_format($order['pay_amount'] / 100, 2) ?></span>
        </div>
        <div class="summary-row">
            <span>创建时间</span>
            <span><?= $order['created_at'] ?></span>
        </div>
        <div class="summary-total">
            <span>应付总额</span>
            <span>¥<?= number_format($order['pay_amount'] / 100, 2) ?></span>
        </div>
    </div>

    <div class="payment-methods">
        <div class="payment-method active" onclick="selectPayment('wechat')">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">💚</div>
            <div class="payment-method-name">微信支付</div>
        </div>
        <div class="payment-method" onclick="selectPayment('alipay')" style="opacity: 0.5; cursor: not-allowed;">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">💙</div>
            <div class="payment-method-name">支付宝（暂未开通）</div>
        </div>
    </div>

    <div id="wechatPay" class="payment-panel">
        <div class="qrcode-container">
            <div id="qrcode">
                <div class="spinner"></div>
                <p>正在生成支付码...</p>
            </div>
            <p style="margin-top: 1rem; color: #666;">请使用微信扫描二维码进行支付</p>
            <div class="countdown" id="countdown"></div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 2rem;">
        <a href="/orders/<?= $order['id'] ?>" class="btn">返回订单详情</a>
        <a href="/orders" class="btn">查看订单列表</a>
    </div>
</div>

<script>
let checkTimer = null;
let countdownTimer = null;
const orderNo = '<?= htmlspecialchars($order['order_no']) ?>';
const orderId = <?= $order['id'] ?>;
const expireTime = 1800; // 30 分钟过期

// 选择支付方式
function selectPayment(method) {
    if (method !== 'wechat') {
        alert('该支付方式暂未开通');
        return;
    }
    document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('active'));
    event.currentTarget.classList.add('active');
}

// 生成二维码
function generateQRCode(url) {
    // 使用 Google Chart API 生成二维码（生产环境建议使用本地库如 qrcode.js）
    const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(url);
    return `<img src="${qrUrl}" alt="支付二维码" onclick="zoomQR()">`;
}

// 加载支付二维码
function loadQRCode() {
    fetch('/payment/wechat/create/' + orderId, {
        method: 'POST',
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('qrcode').innerHTML = generateQRCode(data.code_url);
            startStatusCheck();
            startCountdown();
        } else {
            document.getElementById('qrcode').innerHTML = '<p style="color: red;">' + data.message + '</p>';
        }
    })
    .catch(err => {
        document.getElementById('qrcode').innerHTML = '<p style="color: red;">加载失败，请刷新页面</p>';
    });
}

// 开始检查支付状态
function startStatusCheck() {
    checkTimer = setInterval(() => {
        fetch('/payment/wechat/query?order_no=' + orderNo)
            .then(res => res.json())
            .then(data => {
                if (data.paid) {
                    clearInterval(checkTimer);
                    window.location.href = '/payment/success/' + orderId;
                }
            })
            .catch(err => console.log('检查支付状态失败'));
    }, 3000); // 每 3 秒检查一次
}

// 开始倒计时
function startCountdown() {
    let remaining = expireTime;
    countdownTimer = setInterval(() => {
        remaining--;
        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        document.getElementById('countdown').textContent =
            '支付倒计时：' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

        if (remaining <= 0) {
            clearInterval(countdownTimer);
            clearInterval(checkTimer);
            document.getElementById('countdown').textContent = '支付已过期，请重新下单';
            document.getElementById('qrcode').innerHTML = '<p style="color: red;">二维码已过期</p>';
        }
    }, 1000);
}

// 放大二维码
function zoomQR() {
    const img = document.querySelector('#qrcode img');
    if (img) {
        const newWindow = window.open();
        newWindow.document.write('<img src="' + img.src + '" style="width: 400px; height: 400px;">');
    }
}

// 页面加载时生成二维码
document.addEventListener('DOMContentLoaded', loadQRCode);
</script>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/app.php';
?>
