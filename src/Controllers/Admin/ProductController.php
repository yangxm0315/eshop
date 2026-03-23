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

        $db = Database::getInstance();

        // 处理主图
        $file = $this->request->file('main_image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], __DIR__ . '/../../../public/uploads/' . $filename);
            $data['main_image'] = '/uploads/' . $filename;
        }

        // 插入商品
        $productId = $db->insert('products', $data);

        // 处理多图
        $this->uploadImages($productId, $this->request->file('images'));

        $this->session->flash('success', '商品已创建');
        return $this->response->redirect('/admin/products');
    }

    public function edit(int $id): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $product = $db->queryOne("SELECT * FROM products WHERE id = :id", ['id' => $id]);
        // 将分转换为元用于显示
        $product['price'] = $product['price'] / 100;
        // 加载商品图片（只返回 image 列的数组）
        $images = $db->query("SELECT image FROM product_images WHERE product_id = :product_id ORDER BY sort, id", ['product_id' => $id]);
        $product['images'] = array_column($images, 'image');
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

        // 移除无效字段
        unset($data['_token']);
        unset($data['images']);

        $db = Database::getInstance();

        // 处理主图
        $file = $this->request->file('main_image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], __DIR__ . '/../../../public/uploads/' . $filename);
            $data['main_image'] = '/uploads/' . $filename;
        }

        // 更新商品
        $db->update('products', $data, 'id = :id', ['id' => $id]);

        // 处理新上传的图片
        $this->uploadImages($id, $this->request->file('images'));

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

    /**
     * 上传商品图片
     */
    private function uploadImages(int $productId, array $files): void
    {
        if (empty($files) || empty($files['name'])) {
            return;
        }

        $db = Database::getInstance();
        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $uploadPath = __DIR__ . '/../../../public/uploads/' . $filename;

            if (move_uploaded_file($files['tmp_name'][$i], $uploadPath)) {
                $db->insert('product_images', [
                    'product_id' => $productId,
                    'image' => '/uploads/' . $filename,
                    'sort' => $i,
                ]);
            }
        }
    }

    /**
     * 删除商品图片
     */
    public function deleteImage(): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $data = $this->request->all();
        $imagePath = $data['image_path'] ?? '';

        if (empty($imagePath)) {
            return $this->json(['success' => false, 'message' => '图片路径不能为空']);
        }

        $db = Database::getInstance();
        $image = $db->queryOne("SELECT * FROM product_images WHERE image = :image", ['image' => $imagePath]);

        if ($image) {
            // 删除文件
            $filePath = __DIR__ . '/../../../public' . $imagePath;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            // 删除记录
            $db->delete('product_images', 'id = :id', ['id' => $image['id']]);
        }

        return $this->json(['success' => true]);
    }

    /**
     * 设置主图
     */
    public function setMainImage(int $productId): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $data = $this->request->all();
        $imagePath = $data['image'] ?? '';

        if (empty($imagePath)) {
            return $this->json(['success' => false, 'message' => '图片路径不能为空']);
        }

        $db = Database::getInstance();
        $db->update('products', ['main_image' => $imagePath], 'id = :id', ['id' => $productId]);

        return $this->json(['success' => true]);
    }
}
