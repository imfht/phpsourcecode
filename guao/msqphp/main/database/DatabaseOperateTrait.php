<?php declare(strict_types = 1);
namespace msqphp\main\database;

trait DatabaseOperateTrait
{
    public static function get(string $handler, string $sql, array $prepare = [])
    {
        try {
            $result = empty($prepare)
            ? static::sqlQuery($handler, $sql)->fetchAll(\PDO::FETCH_ASSOC)
            : static::sqlPrepare($handler, $sql, $prepare)->fetchAll(\PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    public static function getOne(string $handler, string $sql, array $prepare = [])
    {

        try {
            $result = empty($prepare)
            ? static::sqlQuery($handler, $sql)->fetch(\PDO::FETCH_ASSOC)
            : static::sqlPrepare($handler, $sql, $prepare)->fetch(\PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    public static function getColumn(string $handler, string $sql, array $prepare = [])
    {
        try {
            $result = empty($prepare)
            ? static::sqlQuery($handler, $sql)->fetchColumn()
            : static::sqlPrepare($handler, $sql, $prepare)->fetchColumn();
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    public static function query(string $handler, string $sql, array $prepare = [])
    {

        try {
            $result = empty($prepare)
            ? static::sqlQuery($handler, $sql)->fetchAll(\PDO::FETCH_ASSOC)
            : static::sqlPrepare($handler, $sql, $prepare)->fetchAll(\PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    public static function exec(string $handler, string $sql, array $prepare = []) : ?int
    {
        try {
            $result = empty($prepare)
            ? static::sqlExec($handler, $sql)
            : static::sqlPrepare($handler, $sql, $prepare)->rowCount();
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    // 获取最后插入id
    public static function lastInsertId(string $handler) : int
    {
        try {
            return (int) static::getHandler($handler)->lastInsertId();
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }




    // 执行sql语句
    private static function sqlQuery(string $handler, string $sql) : \PDOStatement
    {
        return static::getHandler($handler)->query($sql);
    }
    // 执行exec语句
    private static function sqlExec(string $handler, string $sql) : ?int
    {
        return static::getHandler($handler)->exec($sql);
    }
    // 执行预处理语句
    private static function sqlPrepare(string $handler, string $sql, array $prepare = []) : \PDOStatement
    {
        $stat = static::getHandler($handler)->prepare($sql);
        foreach ($prepare as $key => $value) {
            // 引用传递,避免出错
            $stat->bindParam($key, $prepare[$key][0], $prepare[$key][1]);
        }
        $stat->execute();
        return $stat;
    }
}