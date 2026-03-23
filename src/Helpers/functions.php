<?php
/**
 * 辅助函数
 */

/**
 * 数组转 XML
 */
function arrayToXml(array $data): string
{
    if (empty($data)) {
        return '';
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<xml>';
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $xml .= '<' . $key . '>' . arrayToXml($value) . '</' . $key . '>';
        } else {
            $xml .= '<' . $key . '>' . (is_numeric($value) ? $value : '<![CDATA[' . $value . ']])') . '</' . $key . '>';
        }
    }
    $xml .= '</xml>';
    return $xml;
}

/**
 * XML 转数组
 */
function xml2array(string $xml): array
{
    $data = [];
    $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    if ($xml === false) {
        return [];
    }
    $data = json_decode(json_encode((array)$xml), true);
    return $data ?: [];
}

/**
 * 重定向到指定 URL
 */
function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

/**
 * 返回 JSON 响应
 */
function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * 获取 CSRF token
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * 生成 CSRF input 字段
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

/**
 * 检查是否是 POST 请求
 */
function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * 获取 GET 参数
 */
function get(string $key, $default = null)
{
    return $_GET[$key] ?? $default;
}

/**
 * 获取 POST 参数
 */
function post(string $key, $default = null)
{
    return $_POST[$key] ?? $default;
}

/**
 * 格式化价格
 */
function format_price(int $cents): string
{
    return '¥' . number_format($cents / 100, 2);
}

/**
 * 格式化日期
 */
function format_date(string $date, string $format = 'Y-m-d H:i:s'): string
{
    return date($format, strtotime($date));
}

/**
 * 截断字符串
 */
function str_limit(string $str, int $length = 100, string $end = '...'): string
{
    if (mb_strlen($str) <= $length) {
        return $str;
    }
    return mb_substr($str, 0, $length) . $end;
}

/**
 * 获取文件扩展名
 */
function file_extension(string $filename): string
{
    return pathinfo($filename, PATHINFO_EXTENSION);
}

/**
 * 生成随机字符串
 */
function random_string(int $length = 16): string
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * 检查用户是否登录
 */
function is_logged_in(): bool
{
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

/**
 * 获取当前用户
 */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * 检查是否是管理员
 */
function is_admin(): bool
{
    return is_logged_in() && ($_SESSION['user']['role'] ?? 0) === 1;
}

/**
 * 要求登录
 */
function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['_flash_error'] = '请先登录';
        redirect('/login');
    }
}

/**
 * 要求管理员
 */
function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        $_SESSION['_flash_error'] = '无权访问';
        redirect('/');
    }
}
