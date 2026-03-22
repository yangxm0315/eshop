<?php

namespace Controllers;

use Core\Controller;
use Core\Response;
use Core\Database;

class HomeController extends Controller
{
    public function index(): Response
    {
        $db = Database::getInstance();
        // 获取推荐商品
        $products = $db->query("SELECT * FROM products WHERE is_show = 1 ORDER BY sort ASC, id DESC LIMIT 8");
        $categories = $db->query("SELECT * FROM categories ORDER BY sort ASC");

        return $this->view('home/index', [
            'products' => $products,
            'categories' => $categories,
            'user' => $this->user(),
        ]);
    }
}
