<?php

namespace Core;

use PDO;
use PDOException;

/**
 * 数据库类 - 使用 PDO
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';

        try {
            $dsn = "sqlite:" . $config['database']['sqlite'];
            $this->connection = new PDO($dsn, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die("数据库连接失败：" . $e->getMessage());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * 查询多行记录
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * 查询单行记录
     */
    public function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * 插入记录
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * 更新记录
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $columns = [];
        foreach (array_keys($data) as $column) {
            $columns[] = "{$column} = :{$column}";
        }
        $columnsStr = implode(', ', $columns);

        $sql = "UPDATE {$table} SET {$columnsStr} WHERE {$where}";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_merge($data, $whereParams));

        return $stmt->rowCount();
    }

    /**
     * 删除记录
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    /**
     * 开始事务
     */
    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    /**
     * 提交事务
     */
    public function commit(): void
    {
        $this->connection->commit();
    }

    /**
     * 回滚事务
     */
    public function rollback(): void
    {
        $this->connection->rollBack();
    }
}
