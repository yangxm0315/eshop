<?php

namespace Core;

/**
 * Session 管理类
 */
class Session
{
    private static ?Session $instance = null;

    private function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function getInstance(): Session
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 设置 Session 值
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * 获取 Session 值
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * 检查 Session 是否存在
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * 删除 Session
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * 清空 Session
     */
    public function flush(): void
    {
        $_SESSION = [];
    }

    /**
     * 销毁 Session
     */
    public function destroy(): void
    {
        session_destroy();
    }

    /**
     * 获取用户 ID
     */
    public function userId(): ?int
    {
        $user = $this->get('user');
        return $user['id'] ?? null;
    }

    /**
     * 检查是否登录
     */
    public function isLoggedIn(): bool
    {
        $user = $this->get('user');
        return isset($user['id']);
    }

    /**
     * 检查是否是管理员
     */
    public function isAdmin(): bool
    {
        $user = $this->get('user');
        return ($user['role'] ?? 0) === 1;
    }

    /**
     * 获取当前用户
     */
    public function user(): ?array
    {
        return $this->get('user');
    }

    /**
     * 设置 Flash 消息
     */
    public function flash(string $key, mixed $value): void
    {
        $this->set('_flash_' . $key, $value);
    }

    /**
     * 获取并清除 Flash 消息
     */
    public function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $this->get('_flash_' . $key, $default);
        $this->remove('_flash_' . $key);
        return $value;
    }
}
