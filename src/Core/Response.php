<?php

namespace Core;

/**
 * Response 类
 */
class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $body = '';

    /**
     * 设置状态码
     */
    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * 设置响应头
     */
    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * 设置 JSON 响应
     */
    public function json(array $data): self
    {
        $this->header('Content-Type', 'application/json');
        $this->body = json_encode($data);
        return $this;
    }

    /**
     * 设置 HTML 响应
     */
    public function html(string $html): self
    {
        $this->header('Content-Type', 'text/html');
        $this->body = $html;
        return $this;
    }

    /**
     * 设置 XML 响应
     */
    public function xml(array $data): self
    {
        $this->header('Content-Type', 'text/xml');
        $this->body = arrayToXml($data);
        return $this;
    }

    /**
     * 重定向
     */
    public function redirect(string $url): self
    {
        $this->statusCode = 302;
        $this->header('Location', $url);
        return $this;
    }

    /**
     * 下载文件
     */
    public function download(string $filepath, string $filename = ''): self
    {
        if (!file_exists($filepath)) {
            return $this->status(404)->html('File not found');
        }

        $filename = $filename ?: basename($filepath);
        $this->header('Content-Type', 'application/octet-stream');
        $this->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->body = file_get_contents($filepath);
        return $this;
    }

    /**
     * 发送响应
     */
    public function send(): void
    {
        // 设置状态码
        http_response_code($this->statusCode);

        // 设置响应头
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // 输出 body
        echo $this->body;
    }

    /**
     * 输出视图
     */
    public function view(string $view, array $data = []): self
    {
        extract($data);
        ob_start();
        include __DIR__ . "/../../views/{$view}.php";
        $this->body = ob_get_clean();
        return $this;
    }
}
