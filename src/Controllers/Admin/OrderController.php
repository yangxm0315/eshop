<?php

namespace Controllers\Admin;

use Core\Controller;
use Core\Response;
use Core\Database;

class OrderController extends Controller
{
    public function index(): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $orders = $db->query("SELECT o.*, u.name as user_name FROM orders o
                              LEFT JOIN users u ON o.user_id = u.id
                              ORDER BY o.id DESC");

        return $this->view('admin/orders/index', [
            'orders' => $orders,
        ]);
    }

    public function show(int $id): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $order = $db->queryOne("SELECT * FROM orders WHERE id = :id", ['id' => $id]);
        $orderItems = $db->query("SELECT oi.*, p.name FROM order_items oi
                                  LEFT JOIN products p ON oi.product_id = p.id
                                  WHERE oi.order_id = :order_id", ['order_id' => $id]);
        $address = $db->queryOne("SELECT * FROM addresses WHERE id = :id", ['id' => $order['address_id']]);

        return $this->view('admin/orders/show', [
            'order' => $order,
            'orderItems' => $orderItems,
            'address' => $address,
        ]);
    }

    public function ship(int $id): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $db->update('orders', [
            'status' => 2,
            'shipped_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', ['id' => $id]);

        $this->session->flash('success', '订单已发货');
        return $this->response->redirect('/admin/orders/' . $id);
    }

    public function complete(int $id): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $db->update('orders', [
            'status' => 3,
            'completed_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', ['id' => $id]);

        $this->session->flash('success', '订单已完成');
        return $this->response->redirect('/admin/orders/' . $id);
    }
}
