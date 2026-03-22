<?php

namespace Middleware;

use Core\Session;
use Core\Response;

/**
 * 管理员中间件
 */
class AdminMiddleware
{
    public function handle(): ?Response
    {
        $session = Session::getInstance();

        if (!$session->isLoggedIn()) {
            $session->flash('error', '请先登录');
            $response = new Response();
            return $response->redirect('/login');
        }

        if (!$session->isAdmin()) {
            $session->flash('error', '无权访问管理后台');
            $response = new Response();
            return $response->redirect('/');
        }

        return null;
    }
}
