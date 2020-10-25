<?php declare (strict_types = 1);
namespace msqphp\main\database;

trait DatabaseHandlerTrait
{
    private static $handlers = [];

    // 获取连接信息
    private static function getConnectInfo(array $config): array
    {
        switch ($config['type']) {
            case 'mysql':
                $dsn = $config['type'] . ':host=' . $config['host'] . ';port=' . $config['port'] . ';dbname=' . $config['name'] . ';charset=' . $config['charset'] . ';';
                return ['dsn' => $dsn, 'username' => $config['username'], 'password' => $config['password']];
            case 'pgsql':
                $dsn = $config['type'] . ':host=' . $config['host'] . ' port=' . $config['port'] . ' dbname=' . $config['name'] . ' user=' . $config['username'] . ' password=' . $config['password'];
                return ['dsn' => $dsn, 'username' => '', $password => ''];
            case 'sqllite':
                $dsn = 'sqlite:' . $config['name'];
                return ['dsn' => $dsn, 'username' => '', $password => ''];
            case 'oci':
            case 'oracle':
                $dsn = 'oci:dbname=' . $config['database'] . ';charset=' . $config['charset'];
                return ['dsn' => $dsn, 'username' => $config['username'], 'password' => $config['password']];
            default:
                static::exception('未知的数据库类型');
        }
    }
    // 获取
    public static function getHandler(string $name): \PDO
    {
        return static::$handlers[$name] = static::$handlers[$name] ?? static::initHandler($name);
    }
    // 设置
    public static function setHandler(string $name, \PDO $handler): void
    {
        static::$handlers[$name] = $handler;
    }
    // 关闭
    public static function closeHandler(string $name): void
    {
        if (isset(static::$handlers[$name])) {
            static::$handlers[$name] = null;
            unset(static::$handlers[$name]);
        }
    }
    // 关闭所有
    public static function closeAllHandler(): void
    {
        foreach (static::$dba_handlers as $name) {
            static::$handlers[$name] = null;
            unset(static::$handlers[$name]);
        }
    }
    // 初始化
    private static function initHandler(string $name): \PDO
    {
        try {
            // 获取配置
            $config = static::getConfig($name);
            // 获得连接信息
            $connect_info = static::getConnectInfo($config);
            // 初始化并设置对应属性
            $pdo = new \PDO($connect_info['dsn'], $connect_info['username'], $connect_info['password'], $config['params']);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            // 设置默认编码
            $pdo->exec('SET NAMES ' . $config['charset']);
            return $pdo;
        } catch (\PDOException $e) {
            // 异常捕捉
            static::exception($name . '数据库初始化失败,原因:' . $e->getMessage());
        }
    }
}
