<?php

namespace Controllers;

use Core\Controller;
use Core\Response;
use Core\Database;
use Payment\WechatPay;

class PaymentController extends Controller
{
    /**
     * 显示支付页面
     */
    public function show(int $orderId): Response
    {
        $userId = $this->session->userId();
        if (!$userId) {
            $this->session->flash('error', '请先登录');
            return $this->response->redirect('/login');
        }

        $db = Database::getInstance();
        $order = $db->queryOne("SELECT * FROM orders WHERE id = :id AND user_id = :user_id", [
            'id' => $orderId,
            'user_id' => $userId,
        ]);

        if (!$order) {
            $this->session->flash('error', '订单不存在');
            return $this->response->redirect('/orders');
        }

        // 检查订单状态
        if ($order['status'] != 0) {
            $this->session->flash('info', '该订单无需支付');
            return $this->response->redirect('/orders/' . $orderId);
        }

        return $this->view('payment/show', [
            'order' => $order,
        ]);
    }

    /**
     * 创建微信支付二维码
     */
    public function createWechatQr(int $orderId): Response
    {
        $userId = $this->session->userId();
        if (!$userId) {
            return $this->json(['success' => false, 'message' => '请先登录'], 401);
        }

        $db = Database::getInstance();
        $order = $db->queryOne("SELECT * FROM orders WHERE id = :id AND user_id = :user_id", [
            'id' => $orderId,
            'user_id' => $userId,
        ]);

        if (!$order) {
            return $this->json(['success' => false, 'message' => '订单不存在']);
        }

        if ($order['status'] != 0) {
            return $this->json(['success' => false, 'message' => '订单状态异常']);
        }

        // 创建微信支付订单
        $wechatPay = new WechatPay();
        $result = $wechatPay->createNativePay([
            'body' => '商城订单支付',
            'out_trade_no' => $order['order_no'],
            'total_fee' => (int) ($order['pay_amount']), // 单位：分
            'order_id' => $order['id'],
        ]);

        if ($result['success']) {
            return $this->json([
                'success' => true,
                'code_url' => $result['code_url'],
                'trade_no' => $result['trade_no'],
            ]);
        }

        return $this->json(['success' => false, 'message' => '创建支付订单失败']);
    }

    /**
     * 查询支付状态
     */
    public function queryPayStatus(string $orderNo): Response
    {
        $wechatPay = new WechatPay();
        $result = $wechatPay->queryOrder($orderNo);

        if ($result['success'] && $result['trade_state'] === 'SUCCESS') {
            return $this->json([
                'success' => true,
                'paid' => true,
                'message' => '支付成功',
            ]);
        }

        return $this->json([
            'success' => true,
            'paid' => false,
            'message' => '等待支付',
        ]);
    }

    /**
     * 微信支付回调
     */
    public function wechatNotify(): Response
    {
        $wechatPay = new WechatPay();
        $result = $wechatPay->handleNotify();

        if ($result['return_code'] === 'SUCCESS') {
            return $this->response->xml($result);
        }

        return $this->response->xml(['return_code' => 'FAIL', 'return_msg' => $result['return_msg']]);
    }

    /**
     * 支付成功页面
     */
    public function success(int $orderId): Response
    {
        $userId = $this->session->userId();
        $db = Database::getInstance();

        $order = $db->queryOne("SELECT * FROM orders WHERE id = :id AND user_id = :user_id", [
            'id' => $orderId,
            'user_id' => $userId,
        ]);

        if (!$order) {
            $this->session->flash('error', '订单不存在');
            return $this->response->redirect('/orders');
        }

        return $this->view('payment/success', [
            'order' => $order,
        ]);
    }
}
