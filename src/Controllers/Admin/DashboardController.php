<?php

namespace Controllers\Admin;

use Core\Controller;
use Core\Response;
use Core\Database;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();

        // 统计数据
        $stats = [
            'users' => $db->queryOne("SELECT COUNT(*) as count FROM users")['count'],
            'products' => $db->queryOne("SELECT COUNT(*) as count FROM products")['count'],
            'orders' => $db->queryOne("SELECT COUNT(*) as count FROM orders")['count'],
            'pending_orders' => $db->queryOne("SELECT COUNT(*) as count FROM orders WHERE status = 0")['count'],
        ];

        // 最近订单
        $recentOrders = $db->query("SELECT o.*, u.name as user_name FROM orders o
                                    LEFT JOIN users u ON o.user_id = u.id
                                    ORDER BY o.id DESC LIMIT 10");

        return $this->view('admin/dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
