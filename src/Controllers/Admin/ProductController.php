<?php

namespace Controllers\Admin;

use Core\Controller;
use Core\Response;
use Core\Database;

class ProductController extends Controller
{
    public function index(): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $products = $db->query("SELECT p.*, c.name as category_name FROM products p
                                LEFT JOIN categories c ON p.category_id = c.id
                                ORDER BY p.id DESC");

        return $this->view('admin/products/index', [
            'products' => $products,
        ]);
    }

    public function create(): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $categories = $db->query("SELECT * FROM categories ORDER BY sort ASC");

        return $this->view('admin/products/create', [
            'categories' => $categories,
        ]);
    }

    public function store(): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $data = $this->request->all();
        $data['price'] = (int) ($data['price'] * 100);
        $data['is_show'] = !empty($data['is_show']) ? 1 : 0;

        $file = $this->request->file('main_image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], __DIR__ . '/../../../public/uploads/' . $filename);
            $data['main_image'] = '/uploads/' . $filename;
        }

        $db = Database::getInstance();
        $db->insert('products', $data);

        $this->session->flash('success', '商品已创建');
        return $this->response->redirect('/admin/products');
    }

    public function edit(int $id): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $product = $db->queryOne("SELECT * FROM products WHERE id = :id", ['id' => $id]);
        $categories = $db->query("SELECT * FROM categories ORDER BY sort ASC");

        return $this->view('admin/products/edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(int $id): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $data = $this->request->all();
        $data['price'] = (int) ($data['price'] * 100);
        $data['is_show'] = !empty($data['is_show']) ? 1 : 0;

        $file = $this->request->file('main_image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], __DIR__ . '/../../../public/uploads/' . $filename);
            $data['main_image'] = '/uploads/' . $filename;
        }

        $db = Database::getInstance();
        $db->update('products', $data, 'id = :id', ['id' => $id]);

        $this->session->flash('success', '商品已更新');
        return $this->response->redirect('/admin/products');
    }

    public function destroy(int $id): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $db->delete('products', 'id = :id', ['id' => $id]);

        $this->session->flash('success', '商品已删除');
        return $this->response->redirect('/admin/products');
    }

    public function toggleStatus(int $id): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $product = $db->queryOne("SELECT is_show FROM products WHERE id = :id", ['id' => $id]);
        
        $db->update('products', [
            'is_show' => $product['is_show'] ? 0 : 1,
        ], 'id = :id', ['id' => $id]);

        return $this->json(['success' => true]);
    }
}
