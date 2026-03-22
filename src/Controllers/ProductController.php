<?php

namespace Controllers;

use Core\Controller;
use Core\Response;
use Core\Database;

class ProductController extends Controller
{
    public function index(): Response
    {
        $db = Database::getInstance();
        
        $categoryId = $this->request->get('category_id');
        $keyword = $this->request->get('keyword');

        $sql = "SELECT * FROM products WHERE is_show = 1";
        $params = [];

        if ($categoryId) {
            $sql .= " AND category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        if ($keyword) {
            $sql .= " AND (name LIKE :keyword OR description LIKE :keyword)";
            $params['keyword'] = "%{$keyword}%";
        }

        $sql .= " ORDER BY sort ASC, id DESC";
        $products = $db->query($sql, $params);

        $categories = $db->query("SELECT * FROM categories ORDER BY sort ASC");

        return $this->view('products/index', [
            'products' => $products,
            'categories' => $categories,
            'currentCategoryId' => $categoryId,
            'keyword' => $keyword,
            'user' => $this->user(),
        ]);
    }

    public function show(int $id): Response
    {
        $db = Database::getInstance();
        $product = $db->queryOne("SELECT * FROM products WHERE id = :id", ['id' => $id]);

        if (!$product) {
            return $this->response->status(404)->html('商品不存在');
        }

        return $this->view('products/show', [
            'product' => $product,
            'user' => $this->user(),
        ]);
    }
}
