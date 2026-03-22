<?php

namespace Middleware;

use Core\Session;
use Core\Response;

/**
 * 认证中间件
 */
class AuthMiddleware
{
    public function handle(): ?Response
    {
        $session = Session::getInstance();

        if (!$session->isLoggedIn()) {
            $session->flash('error', '请先登录');
            $response = new Response();
            return $response->redirect('/login');
        }

        return null;
    }
}
