<?php declare(strict_types = 1);
namespace msqphp\main\model;

use \msqphp\core;

trait ModelStaticTrait
{
    use ModelSqlBulidTrait;

    protected static $config = [];

    // 静态类初始化
    protected static function initStatic() : void
    {
        // 初始化过直接返回
        static $inited = false;

        if (!$inited) {
            $inited = true;
            static::$config = core\config\Config::get('model');
        }
    }
}
trait ModelSqlBulidTrait
{
    private static function buildExistsSql(array $params) : string
    {
        // 开始
        $sql = 'SELECT ';
        isset($params['field']) || static::exception('错误的sql语句,未指定查询值');
        // 以逗号分割字段,并移除最后的逗号,加一个空格.
        $sql .= rtrim(implode(',', $params['field']), ',') . ' ';
        isset($params['table']) || static::exception('错误的sql语句,未指定表名');
        // 以逗号分割表名,并移除最后的逗号,加一个空格.
        $sql .= 'FROM '.rtrim(implode(',', $params['table']), ',') . ' ';
        isset($params['value']) || static::exception('错误的sql语句,未指定条件');
        // WHERE
        $sql .= 'WHERE ';
        // 如果字段数小于值,无法判断
        (count($params['field']) >= $l = count($params['value'])) || static::exception('错误的sql语句,指定whrer条件错误');
        // where值
        for ($i = 0; $i < $l; ++$i) {
            $sql .= $params['field'][$i] . '=' . $params['value'][$i] . ' AND ';
        }
        // 移除最后的 and四字符
        $sql = substr($sql, 0, -4);
        return $sql;
    }

    private static function buildInsertSql(array $params) : string
    {
        (isset($params['field']) && isset($params['value']) && count($params['field']) === count($params['value'])) || static::exception('错误的sql插入语句,键值不存在或数目不匹配');
        (isset($params['table']) && 1 === count($params['table'])) || static::exception('错误的sql插入语句,未指定表名,或者指定表过多');
        return 'INSERT INTO '.$params['table'][0] . ' ('.rtrim(implode(',', $params['field']), ',') . ') ' . 'VALUES ('. rtrim(implode(',', $params['value']), ',').') ';
    }
    private static function buildSelectSql(array $params) : string
    {
        // 开始
        $sql = 'SELECT ';
        isset($params['field']) || static::exception('错误的sql语句,未指定查询值');
        // 以逗号分割字段,并移除最后的逗号,加一个空格.
        $sql .= rtrim(implode(',', $params['field']), ',') . ' ';
        isset($params['table']) || static::exception('错误的sql语句,未指定表名');
        // 以逗号分割表名,并移除最后的逗号,加一个空格.
        $sql .= 'FROM '.rtrim(implode(',', $params['table']), ',') . ' ';

        if (isset($params['join'])) {
            switch ($params['join']['type']) {
                case 'inner_join':
                    $sql .= 'INNER';
                    break;
                case 'left_join':
                    $sql .= 'LEFT';
                    break;
                case 'right_join':
                    $sql .= 'RIGHT';
                    break;
                case 'full_join':
                    $sql .= 'FULL';
                    break;
                case 'cross_join':
                    $sql .= 'CROSS';
                    break;
                default:
                    static::exception('未知的join语句类型');
            }
            $table = $params['join']['table'];
            $sql .=  ' JOIN '.$table.' ON ';
            foreach ($params['join']['on'] as $v) {
                $sql .= $params['table'][0].'.'.$v[0].$v[1].$table.'.'.$v[2].' AND ';
            }
            $sql = substr($sql, 0, -4);
        }
        if (isset($params['where'])) {
            $sql .= 'WHERE ';
            // 添加where值
            foreach ($params['where'] as [$having, $condition, $value]) {
                $sql .= $having . ' ' . $condition . ' ' . $value . ' AND ';
            }
            $sql = substr($sql, 0, -4);
        } else {
            $sql .= 'WHERE 1 ';
        }
        if (isset($params['having'])) {
            $sql .= 'HAVING ';
            // 添加where值
            foreach ($params['having'] as [$having, $condition, $value]) {
                $sql .= $having . $condition . $value . ' AND ';
            }
            $sql = substr($sql, 0, -4);
        }
        if (isset($params['order'])) {
            $sql .= 'ORDER BY ';
            foreach ($params['order'] as ['field'=>$filed, 'type' => $type]) {
                $sql .= $filed.' '.$type.',';
            }
            $sql = substr($sql, 0, -1) . ' ';
        }
        if (isset($params['limit'])) {
            $sql .= 'LIMIT '.$params['limit'][0].','.$params['limit'][1].' ';
        }

        return $sql;
    }
    private static function buildUpdateSql(array $params) : string
    {
        (isset($params['table']) && 1 === count($params['table'])) || static::exception('错误的sql更新语句,未指定表名,或者指定表过多');
        // UPDATE `表名` SET
        $sql = 'UPDATE ' . $params['table'][0] . 'SET ';
        // 字段=值........
        (isset($params['field']) && isset($params['value']) && count($params['field']) === $count = count($params['value'])) || static::exception('错误的sql更新语句,键值不匹配');
        for ($i = 0; $i < $count; ++$i) {
            $sql .= $params['field'][$i] . '=' . $params['value'][$i] . ', ';
        }
        // 移除末尾, 然后添加where判断
        $sql = substr($sql, 0, -2) . ' WHERE ';
        isset($params['where']) || static::exception('错误的sql更新语句,where键不存在');
        // 添加where值
        foreach ($params['where'] as $value) {
            $sql .= $value[0] . $value[1] . $value[2] . ' AND ';
        }
        // 去除最后的 AND
        return substr($sql, 0, -4);
    }
    private static function buildDeleteSql(array $params) : string
    {
    }
}