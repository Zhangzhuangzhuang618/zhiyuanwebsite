<?php
namespace app\model;

/**
 * 基础模型类 - 提供通用数据库操作
 */
abstract class BaseModel
{
    protected $table;
    protected $prefix;
    protected $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance();
    }

    /**
     * 获取完整表名
     */
    protected function table(): string
    {
        return Database::table($this->table);
    }

    /**
     * 查询单条记录
     */
    public function find($id, string $field = '*')
    {
        $sql = "SELECT {$field} FROM " . $this->table() . " WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * 按条件查询单条
     */
    public function findWhere(array $where, string $field = '*')
    {
        $conditions = [];
        $params = [];
        foreach ($where as $key => $value) {
            $conditions[] = "`{$key}` = ?";
            $params[] = $value;
        }
        $sql = "SELECT {$field} FROM " . $this->table() . " WHERE " . implode(' AND ', $conditions) . " LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * 查询列表
     */
    public function select(array $where = [], string $field = '*', string $order = 'id DESC', int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT {$field} FROM " . $this->table();
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $conditions[] = "`{$key}` IN (" . implode(',', array_fill(0, count($value), '?')) . ")";
                    $params = array_merge($params, $value);
                } else {
                    $conditions[] = "`{$key}` = ?";
                    $params[] = $value;
                }
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if ($order) {
            $sql .= " ORDER BY {$order}";
        }
        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * 分页查询
     */
    public function paginate(array $where = [], int $page = 1, int $pageSize = 12, string $field = '*', string $order = 'id DESC'): array
    {
        $offset = ($page - 1) * $pageSize;

        // 查询总数
        $sql = "SELECT COUNT(*) as total FROM " . $this->table();
        $params = [];
        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $key => $value) {
                $conditions[] = "`{$key}` = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        $total = (int)$stmt->fetch()['total'];

        // 查询数据
        $list = $this->select($where, $field, $order, $pageSize, $offset);

        return [
            'list'       => $list,
            'total'      => $total,
            'page'       => $page,
            'page_size'  => $pageSize,
            'total_page' => ceil($total / $pageSize),
        ];
    }

    /**
     * 插入记录
     */
    public function insert(array $data): int
    {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($values), '?');

        $sql = "INSERT INTO " . $this->table() . " (`" . implode('`,`', $fields) . "`) VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($values);
        return (int)$this->connection->lastInsertId();
    }

    /**
     * 更新记录
     */
    public function update(int $id, array $data): bool
    {
        $sets = [];
        $params = [];
        foreach ($data as $key => $value) {
            $sets[] = "`{$key}` = ?";
            $params[] = $value;
        }
        $params[] = $id;

        $sql = "UPDATE " . $this->table() . " SET " . implode(', ', $sets) . " WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * 删除记录（软删除）
     */
    public function delete(int $id): bool
    {
        return $this->update($id, ['delete_time' => date('Y-m-d H:i:s')]);
    }

    /**
     * 查询某字段值
     */
    public function value(string $field, array $where = [])
    {
        $row = $this->findWhere($where, $field);
        return $row[$field] ?? null;
    }

    /**
     * 获取表名（公开方法，供外部查询使用）
     */
    public function getTable(): string
    {
        return $this->table();
    }

    /**
     * 获取数据库连接（公开方法）
     */
    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    /**
     * 获取所有记录（无分页）
     */
    public function all(array $where = [], string $field = '*', string $order = 'sort ASC, id DESC'): array
    {
        return $this->select($where, $field, $order);
    }
}
