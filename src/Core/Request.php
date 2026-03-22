<?php

namespace Core;

/**
 * Request 类
 */
class Request
{
    private array $query = [];
    private array $request = [];
    private array $files = [];

    public function __construct()
    {
        $this->query = $_GET;
        $this->request = $_POST;
        $this->files = $_FILES;

        // 处理 JSON 请求
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $this->request = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        // 处理 PUT/DELETE
        if (in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'DELETE'])) {
            parse_str(file_get_contents('php://input'), $data);
            $this->request = array_merge($this->request, $data);
        }
    }

    /**
     * 获取 GET 参数
     */
    public function get(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * 获取 POST 参数
     */
    public function post(string $key, $default = null)
    {
        return $this->request[$key] ?? $default;
    }

    /**
     * 获取输入参数（GET 或 POST）
     */
    public function input(string $key, $default = null)
    {
        return $this->request[$key] ?? $this->query[$key] ?? $default;
    }

    /**
     * 获取所有输入
     */
    public function all(): array
    {
        return array_merge($this->query, $this->request);
    }

    /**
     * 获取上传文件
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * 获取请求方法
     */
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 获取请求 URI
     */
    public function uri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * 获取请求头
     */
    public function header(string $key, $default = null)
    {
        $key = strtoupper(str_replace('-', '_', $key));
        return $_SERVER['HTTP_' . $key] ?? $default;
    }

    /**
     * 检查是否是 AJAX 请求
     */
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * 验证 CSRF Token
     */
    public function validateCsrf(string $token): bool
    {
        $session = Session::getInstance();
        return $token === $session->get('csrf_token');
    }
}
