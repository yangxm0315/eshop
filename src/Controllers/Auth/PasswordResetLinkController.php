<?php

namespace Controllers\Auth;

use Core\Controller;
use Core\Response;
use Core\Database;

class PasswordResetLinkController extends Controller
{
    public function create(): Response
    {
        return $this->view('auth/forgot-password');
    }

    public function store(): Response
    {
        $email = $this->request->post('email');

        $db = Database::getInstance();
        $user = $db->queryOne("SELECT * FROM users WHERE email = :email", ['email' => $email]);

        // 无论是否找到用户，都显示成功消息（防止枚举邮箱）
        $this->session->flash('success', '如果该邮箱已注册，您将收到重置密码邮件');
        return $this->response->redirect('/forgot-password');
    }
}
