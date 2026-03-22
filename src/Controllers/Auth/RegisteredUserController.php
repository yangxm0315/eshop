<?php

namespace Controllers\Auth;

use Core\Controller;
use Core\Response;
use Core\Database;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return $this->view('auth/register');
    }

    public function store(): Response
    {
        $name = $this->request->post('name');
        $email = $this->request->post('email');
        $password = $this->request->post('password');
        $passwordConfirmation = $this->request->post('password_confirmation');

        // 验证
        $errors = $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        if ($password !== $passwordConfirmation) {
            $errors['password'][] = '两次密码输入不一致';
        }

        // 检查邮箱是否已存在
        $db = Database::getInstance();
        $existingUser = $db->queryOne("SELECT * FROM users WHERE email = :email", ['email' => $email]);
        if ($existingUser) {
            $errors['email'][] = '该邮箱已被注册';
        }

        if (!empty($errors)) {
            $this->session->flash('errors', $errors);
            return $this->response->redirect('/register');
        }

        // 创建用户
        $userId = $db->insert('users', [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->session->flash('success', '注册成功，请登录');
        return $this->response->redirect('/login');
    }
}
