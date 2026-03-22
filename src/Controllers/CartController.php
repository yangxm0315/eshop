<?php

namespace Controllers;

use Core\Controller;
use Core\Response;
use Core\Session;
use Core\Database;

class CartController extends Controller
{
    public function index(): Response
    {
        $userId = $this->session->userId();
        $cartItems = [];

        if ($userId) {
            $db = Database::getInstance();
            $sql = "SELECT c.*, p.name, p.price, p.main_image, p.stock
                    FROM cart c
                    JOIN products p ON c.product_id = p.id
                    WHERE c.user_id = :user_id";
            $cartItems = $db->query($sql, ['user_id' => $userId]);
        }

        $total = 0;
        foreach ($cartItems as &$item) {
            $item['subtotal'] = $item['price'] * $item['quantity'];
            $total += $item['subtotal'];
        }

        return $this->view('cart/index', [
            'cartItems' => $cartItems,
            'total' => $total,
            'user' => $this->user(),
        ]);
    }

    public function add(int $id): Response
    {
        $userId = $this->session->userId();
        if (!$userId) {
            $this->session->flash('error', '请先登录');
            return $this->response->redirect('/login');
        }

        $productId = $id;
        $quantity = (int) $this->request->post('quantity', 1);

        $db = Database::getInstance();
        $product = $db->queryOne("SELECT * FROM products WHERE id = :id", ['id' => $productId]);
        
        if (!$product) {
            $this->session->flash('error', '商品不存在');
            return $this->response->redirect('/products');
        }

        // 检查购物车是否已有该商品
        $existing = $db->queryOne(
            "SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id",
            ['user_id' => $userId, 'product_id' => $productId]
        );

        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            $db->update('cart', ['quantity' => $newQuantity], 'id = :id', ['id' => $existing['id']]);
        } else {
            $db->insert('cart', [
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        $this->session->flash('success', '已添加到购物车');
        return $this->response->redirect('/cart');
    }

    public function update(int $id): Response
    {
        $userId = $this->session->userId();
        if (!$userId) {
            return $this->json(['error' => '请先登录'], 401);
        }

        $cartId = $id;
        $quantity = (int) $this->request->post('quantity', 1);

        $db = Database::getInstance();
        $db->update('cart', ['quantity' => $quantity], 'id = :id AND user_id = :user_id', [
            'id' => $cartId,
            'user_id' => $userId,
        ]);

        $this->session->flash('success', '购物车已更新');
        return $this->response->redirect('/cart');
    }

    public function destroy(int $id): Response
    {
        $userId = $this->session->userId();
        if (!$userId) {
            return $this->json(['error' => '请先登录'], 401);
        }

        $cartId = $id;
        $db = Database::getInstance();
        $db->delete('cart', 'id = :id AND user_id = :user_id', [
            'id' => $cartId,
            'user_id' => $userId,
        ]);

        $this->session->flash('success', '商品已移除');
        return $this->response->redirect('/cart');
    }

    public function clear(): Response
    {
        $userId = $this->session->userId();
        if (!$userId) {
            return $this->json(['error' => '请先登录'], 401);
        }

        $db = Database::getInstance();
        $db->delete('cart', 'user_id = :user_id', ['user_id' => $userId]);

        $this->session->flash('success', '购物车已清空');
        return $this->response->redirect('/cart');
    }
}
