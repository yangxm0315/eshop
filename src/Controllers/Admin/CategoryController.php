<?php

namespace Controllers\Admin;

use Core\Controller;
use Core\Response;
use Core\Database;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $categories = $db->query("SELECT * FROM categories ORDER BY sort ASC, id DESC");

        return $this->view('admin/categories/index', [
            'categories' => $categories,
        ]);
    }

    public function store(): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $db->insert('categories', [
            'name' => $this->request->post('name'),
            'description' => $this->request->post('description', ''),
            'sort' => (int) $this->request->post('sort', 0),
        ]);

        $this->session->flash('success', '分类已创建');
        return $this->response->redirect('/admin/categories');
    }

    public function edit(int $id): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $category = $db->queryOne("SELECT * FROM categories WHERE id = :id", ['id' => $id]);

        return $this->view('admin/categories/edit', [
            'category' => $category,
        ]);
    }

    public function update(int $id): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $db->update('categories', [
            'name' => $this->request->post('name'),
            'description' => $this->request->post('description', ''),
            'sort' => (int) $this->request->post('sort', 0),
        ], 'id = :id', ['id' => $id]);

        $this->session->flash('success', '分类已更新');
        return $this->response->redirect('/admin/categories');
    }

    public function destroy(int $id): Response
    {
        $admin = $this->requireAdmin();
        if ($admin) return $admin;

        $db = Database::getInstance();
        $db->delete('categories', 'id = :id', ['id' => $id]);

        $this->session->flash('success', '分类已删除');
        return $this->response->redirect('/admin/categories');
    }
}
