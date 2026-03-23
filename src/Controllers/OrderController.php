<?php

namespace Controllers;

use Core\Controller;
use Core\Response;
use Core\Database;

class OrderController extends Controller
{
    public function checkout(): Response
    {
        $userId = $this->session->userId();
        
        // 获取购物车商品
        $db = Database::getInstance();
        $sql = "SELECT c.*, p.name, p.price, p.stock, p.main_image
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = :user_id";
        $cartItems = $db->query($sql, ['user_id' => $userId]);

        if (empty($cartItems)) {
            $this->session->flash('error', '购物车为空');
            return $this->response->redirect('/cart');
        }

        // 计算总价
        $totalAmount = 0;
        foreach ($cartItems as &$item) {
            $item['subtotal'] = $item['price'] * $item['quantity'];
            $totalAmount += $item['subtotal'];
        }

        // 获取用户地址
        $addresses = $db->query("SELECT * FROM addresses WHERE user_id = :user_id", ['user_id' => $userId]);
        $defaultAddress = null;
        foreach ($addresses as $addr) {
            if ($addr['is_default']) {
                $defaultAddress = $addr;
                break;
            }
        }

        return $this->view('orders/checkout', [
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'user' => $this->user(),
        ]);
    }

    public function store(): Response
    {
        $userId = $this->session->userId();
        
        $addressId = (int) $this->request->post('address_id');
        $remark = $this->request->post('remark', '');

        if (!$addressId) {
            $this->session->flash('error', '请选择收货地址');
            return $this->response->redirect('/checkout');
        }

        $db = Database::getInstance();

        // 获取购物车商品
        $sql = "SELECT c.*, p.price, p.stock FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = :user_id";
        $cartItems = $db->query($sql, ['user_id' => $userId]);

        if (empty($cartItems)) {
            $this->session->flash('error', '购物车为空');
            return $this->response->redirect('/cart');
        }

        // 检查库存
        foreach ($cartItems as $item) {
            if ($item['stock'] < $item['quantity']) {
                $this->session->flash('error', '商品库存不足');
                return $this->response->redirect('/cart');
            }
        }

        // 计算总金额
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        try {
            $db->beginTransaction();

            // 创建订单
            $orderId = $db->insert('orders', [
                'order_no' => 'ORD' . date('Ymd') . strtoupper(substr(uniqid(), -6)),
                'user_id' => $userId,
                'address_id' => $addressId,
                'total_amount' => $totalAmount,
                'pay_amount' => $totalAmount,
                'status' => 0,
                'remark' => $remark,
            ]);

            // 创建订单商品
            foreach ($cartItems as $item) {
                $db->insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_image' => $item['main_image'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);

                // 扣减库存
                $product = $db->queryOne("SELECT sales FROM products WHERE id = :id", ['id' => $item['product_id']]);
                $db->update('products', [
                    'stock' => $item['stock'] - $item['quantity'],
                    'sales' => $product['sales'] + $item['quantity'],
                ], 'id = :id', ['id' => $item['product_id']]);
            }

            // 清空购物车
            $db->delete('cart', 'user_id = :user_id', ['user_id' => $userId]);

            $db->commit();

            $this->session->flash('success', '订单创建成功');
            return $this->response->redirect('/orders/' . $orderId);

        } catch (\Exception $e) {
            $db->rollback();
            $this->session->flash('error', '订单创建失败：' . $e->getMessage());
            return $this->response->redirect('/checkout');
        }
    }

    public function index(): Response
    {
        $userId = $this->session->userId();
        
        $db = Database::getInstance();
        $sql = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC";
        $orders = $db->query($sql, ['user_id' => $userId]);

        return $this->view('orders/index', [
            'orders' => $orders,
            'user' => $this->user(),
        ]);
    }

    public function show(int $id): Response
    {
        $userId = $this->session->userId();
        
        $db = Database::getInstance();
        $order = $db->queryOne("SELECT * FROM orders WHERE id = :id AND user_id = :user_id", [
            'id' => $id,
            'user_id' => $userId,
        ]);

        if (!$order) {
            return $this->response->status(404)->html('订单不存在');
        }

        $orderItems = $db->query(
            "SELECT oi.*, p.name, p.main_image FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = :order_id",
            ['order_id' => $id]
        );

        $address = $db->queryOne("SELECT * FROM addresses WHERE id = :id", ['id' => $order['address_id']]);

        return $this->view('orders/show', [
            'order' => $order,
            'orderItems' => $orderItems,
            'address' => $address,
            'user' => $this->user(),
        ]);
    }

    public function cancel(int $id): Response
    {
        $userId = $this->session->userId();
        
        $db = Database::getInstance();
        $order = $db->queryOne("SELECT * FROM orders WHERE id = :id AND user_id = :user_id", [
            'id' => $id,
            'user_id' => $userId,
        ]);

        if (!$order) {
            $this->session->flash('error', '订单不存在');
            return $this->response->redirect('/orders');
        }

        if ($order['status'] !== 0) {
            $this->session->flash('error', '该订单不能取消');
            return $this->response->redirect('/orders/' . $id);
        }

        $db->update('orders', [
            'status' => 4,
            'cancelled_at' => date('Y-m-d H:i:s'),
            'cancel_reason' => $this->request->post('reason', '用户主动取消'),
        ], 'id = :id', ['id' => $id]);

        $this->session->flash('success', '订单已取消');
        return $this->response->redirect('/orders');
    }
}
