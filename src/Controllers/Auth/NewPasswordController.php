<?php

namespace Controllers\Auth;

use Core\Controller;
use Core\Response;
use Core\Database;

class NewPasswordController extends Controller
{
    public function create(int $token): Response
    {
        return $this->view('auth/reset-password', ['token' => $token]);
    }

    public function store(): Response
    {
        $token = $this->request->post('token');
        $email = $this->request->post('email');
        $password = $this->request->post('password');
        $passwordConfirmation = $this->request->post('password_confirmation');

        if ($password !== $passwordConfirmation) {
            $this->session->flash('error', '两次密码输入不一致');
            return $this->response->redirect('/reset-password/' . $token);
        }

        $db = Database::getInstance();
        $user = $db->queryOne("SELECT * FROM users WHERE email = :email", ['email' => $email]);

        if (!$user) {
            $this->session->flash('error', '无效的重置链接');
            return $this->response->redirect('/reset-password/' . $token);
        }

        $db->update('users', [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ], 'id = :id', ['id' => $user['id']]);

        $this->session->flash('success', '密码已重置，请登录');
        return $this->response->redirect('/login');
    }
}
