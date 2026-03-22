<?php

namespace Middleware;

use Core\Session;
use Core\Response;

/**
 * 访客中间件（已登录用户重定向）
 */
class GuestMiddleware
{
    public function handle(): ?Response
    {
        $session = Session::getInstance();

        if ($session->isLoggedIn()) {
            $response = new Response();
            return $response->redirect('/');
        }

        return null;
    }
}
