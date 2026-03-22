<?php

namespace Controllers\Auth;

use Core\Controller;
use Core\Response;
use Core\Session;
use Core\Database;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return $this->view('auth/login');
    }

    public function store(): Response
    {
        $email = $this->request->post('email');
        $password = $this->request->post('password');
        $remember = $this->request->post('remember');

        // 验证
        $errors = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], ['email' => $email, 'password' => $password]);

        if (!empty($errors)) {
            $this->session->flash('errors', $errors);
            return $this->response->redirect('/login');
        }

        // 查找用户
        $db = Database::getInstance();
        $user = $db->queryOne("SELECT * FROM users WHERE email = :email", ['email' => $email]);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->session->flash('error', '邮箱或密码错误');
            return $this->response->redirect('/login');
        }

        // 登录成功，设置 Session
        $session = Session::getInstance();
        $session->set('user', [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'avatar' => $user['avatar'],
        ]);

        $this->session->flash('success', '登录成功');
        return $this->response->redirect('/');
    }

    public function destroy(): Response
    {
        $this->session->destroy();
        session_start(); // 重新开始一个空 session
        $this->session->flash('success', '已退出登录');
        return $this->response->redirect('/');
    }
}
