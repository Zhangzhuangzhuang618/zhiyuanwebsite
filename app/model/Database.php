<?php
namespace app\model;

/**
 * 数据库连接管理类
 */
class Database
{
    protected static $instance = null;
    protected static $config = [];

    /**
     * 初始化数据库配置
     */
    public static function init(array $config): void
    {
        self::$config = $config;
    }

    /**
     * 获取PDO连接实例
     */
    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            $cfg = self::$config;
            if ($cfg['type'] === 'sqlite') {
                $dsn = 'sqlite:' . $cfg['database'];
                self::$instance = new \PDO($dsn, null, null, [
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]);
            } else {
                $dsn = sprintf(
                    '%s:host=%s;port=%s;dbname=%s;charset=%s',
                    $cfg['type'],
                    $cfg['hostname'],
                    $cfg['hostport'],
                    $cfg['database'],
                    $cfg['charset']
                );
                self::$instance = new \PDO($dsn, $cfg['username'], $cfg['password'], [
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]);
            }
        }
        return self::$instance;
    }

    /**
     * 获取表名（带前缀）
     */
    public static function table(string $name): string
    {
        return self::$config['prefix'] . $name;
    }

    /**
     * 关闭连接
     */
    public static function close(): void
    {
        self::$instance = null;
    }
}
