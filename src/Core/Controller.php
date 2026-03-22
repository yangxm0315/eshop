<?php

namespace Core;

/**
 * 基础控制器类
 */
abstract class Controller
{
    protected Request $request;
    protected Response $response;
    protected Session $session;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = Session::getInstance();
    }

    /**
     * 返回视图
     */
    protected function view(string $view, array $data = []): Response
    {
        return $this->response->view($view, $data);
    }

    /**
     * 返回 JSON 响应
     */
    protected function json(array $data, int $status = 200): Response
    {
        return $this->response->status($status)->json($data);
    }

    /**
     * 重定向
     */
    protected function redirect(string $url): Response
    {
        return $this->response->redirect($url);
    }

    /**
     * 重定向到命名路由
     */
    protected function route(string $name, array $params = []): Response
    {
        $url = Router::route($name, $params);
        return $this->response->redirect($url);
    }

    /**
     * 设置 Flash 消息
     */
    protected function withFlash(string $key, mixed $value): void
    {
        $this->session->flash($key, $value);
    }

    /**
     * 获取当前用户
     */
    protected function user(): ?array
    {
        $userId = $this->session->userId();
        if (!$userId) {
            return null;
        }

        // 从 Session 获取用户信息
        return $this->session->get('user');
    }

    /**
     * 检查是否登录
     */
    protected function isLoggedIn(): bool
    {
        return $this->session->isLoggedIn();
    }

    /**
     * 检查是否是管理员
     */
    protected function isAdmin(): bool
    {
        return $this->session->isAdmin();
    }

    /**
     * 要求登录
     */
    protected function requireAuth(): ?Response
    {
        if (!$this->isLoggedIn()) {
            $this->withFlash('error', '请先登录');
            return $this->route('login');
        }
        return null;
    }

    /**
     * 要求管理员
     */
    protected function requireAdmin(): ?Response
    {
        if (!$this->isAdmin()) {
            $this->withFlash('error', '无权访问');
            return $this->route('home');
        }
        return null;
    }

    /**
     * 验证数据
     */
    protected function validate(array $rules, array $data): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $ruleParts = explode('|', $rule);

            foreach ($ruleParts as $r) {
                if ($r === 'required' && empty($data[$field])) {
                    $errors[$field][] = "{$field} 不能为空";
                }

                if (strpos($r, 'min:') === 0) {
                    $min = (int) substr($r, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field][] = "{$field} 长度不能小于 {$min}";
                    }
                }

                if (strpos($r, 'max:') === 0) {
                    $max = (int) substr($r, 4);
                    if (strlen($data[$field]) > $max) {
                        $errors[$field][] = "{$field} 长度不能超过 {$max}";
                    }
                }

                if ($r === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "{$field} 格式不正确";
                }

                if ($r === 'numeric' && !is_numeric($data[$field])) {
                    $errors[$field][] = "{$field} 必须是数字";
                }
            }
        }

        return $errors;
    }
}
