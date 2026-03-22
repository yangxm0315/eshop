<?php

namespace Core;

/**
 * 基础模型类
 */
class Model
{
    protected string $table;
    protected array $attributes = [];
    protected ?int $id = null;

    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    /**
     * 填充属性
     */
    public function fill(array $data): self
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key) || in_array($key, $this->getFillable())) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * 获取可填充字段
     */
    protected function getFillable(): array
    {
        return $this->fillable ?? [];
    }

    /**
     * 获取属性
     */
    public function __get(string $name): mixed
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return null;
    }

    /**
     * 设置属性
     */
    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * 检查属性是否存在
     */
    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * 获取所有属性
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * 保存模型
     */
    public function save(): bool
    {
        $db = Database::getInstance();

        if ($this->id) {
            // 更新
            $data = $this->getFillableData();
            return $db->update($this->table, $data, 'id = :id', ['id' => $this->id]) > 0;
        } else {
            // 插入
            $data = $this->getFillableData();
            $this->id = $db->insert($this->table, $data);
            return $this->id > 0;
        }
    }

    /**
     * 删除模型
     */
    public function delete(): bool
    {
        if (!$this->id) {
            return false;
        }

        $db = Database::getInstance();
        return $db->delete($this->table, 'id = :id', ['id' => $this->id]) > 0;
    }

    /**
     * 获取可填充数据
     */
    private function getFillableData(): array
    {
        $data = [];
        foreach ($this->getFillable() as $field) {
            if (isset($this->attributes[$field])) {
                $data[$field] = $this->attributes[$field];
            }
        }
        return $data;
    }

    /**
     * 查找所有记录
     */
    public static function all(): array
    {
        $db = Database::getInstance();
        $rows = $db->query("SELECT * FROM " . (new static())->table);
        return self::hydrate($rows);
    }

    /**
     * 查找单条记录
     */
    public static function find(int $id): ?static
    {
        $db = Database::getInstance();
        $row = $db->queryOne("SELECT * FROM " . (new static())->table . " WHERE id = :id", ['id' => $id]);
        return $row ? self::hydrateOne($row) : null;
    }

    /**
     * 根据条件查找
     */
    public static function where(array $conditions): array
    {
        $db = Database::getInstance();
        $model = new static();
        $table = $model->table;

        $where = [];
        $params = [];
        foreach ($conditions as $key => $value) {
            $where[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }

        $sql = "SELECT * FROM {$table} WHERE " . implode(' AND ', $where);
        $rows = $db->query($sql, $params);
        return self::hydrate($rows);
    }

    /**
     * 查找第一条
     */
    public static function first(array $conditions = []): ?static
    {
        $db = Database::getInstance();
        $model = new static();
        $table = $model->table;

        if (empty($conditions)) {
            $row = $db->queryOne("SELECT * FROM {$table} LIMIT 1");
        } else {
            $where = [];
            $params = [];
            foreach ($conditions as $key => $value) {
                $where[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
            $sql = "SELECT * FROM {$table} WHERE " . implode(' AND ', $where) . " LIMIT 1";
            $row = $db->queryOne($sql, $params);
        }

        return $row ? self::hydrateOne($row) : null;
    }

    /**
     * 创建记录
     */
    public static function create(array $data): static
    {
        $model = new static($data);
        $model->save();
        return $model;
    }

    /**
     * 水合多个模型
     */
    private static function hydrate(array $rows): array
    {
        return array_map(fn($row) => self::hydrateOne($row), $rows);
    }

    /**
     * 水合单个模型
     */
    private static function hydrateOne(array $row): static
    {
        $model = new static();
        $model->id = $row['id'] ?? null;
        foreach ($row as $key => $value) {
            $model->attributes[$key] = $value;
        }
        return $model;
    }
}
