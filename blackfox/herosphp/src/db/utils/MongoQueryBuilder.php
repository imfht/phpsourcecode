<?php
/**
 * mongodb查询语句处理工具，用来将通用api传入的查询条件转换成mongodb的查询条件
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */
namespace herosphp\db\mongo;

class MongoQueryBuilder {

    /**
     * mongodb条件操作符
     * @var array
     */
    private static  $operator = array(
        '>' => '$gt',
        '<' => '$lt',
        '>=' => '$gte',
        '<=' => '$lte',
        '!=' => '$ne'
    );

    private function __construct() {}

    /**
     * 组合查询字段
     * @param array $fields 推荐格式：array('id','name','pass')
     * @return $this
     */
    public static function fields($fields) {

        $arr = array();
        if ( is_string($fields) ) {
            $fields = explode(',', $fields);
        }
        foreach ( $fields as $key => $value ) {
            $arr[trim($value)] = 1;
        }
        return $arr;
    }

    /**
     * 处理排序
     * @param $sort 推荐格式 array('id' => -1, 'name' => 1)
     * @return array
     */
    public static function sort($sort) {

        if ( is_array($sort) ) return $sort;

        $__sort = array();
        if ( is_string($sort) && $sort != '' ) {
            $oarr = explode(',', $sort);
            foreach ($oarr as $value) {
                $value = preg_replace('/\s+/', ' ', $value);    //去除多余的空格
                $value = explode(' ', $value);
                if ( strtoupper($value[1]) == "DESC" ) {
                    $__sort[$value[0]] = -1;
                } else {
                    $__sort[$value[0]] = 1;
                }
            }
        }
        return $__sort;
    }

    /**
     * 设置查询偏移
     * @param array $limit 标准格式:array($skip, $size)
     * @return $this
     */
    public static function limit($limit) {
        //1. limit(10);
        if ( is_numeric($limit) ) {
            return array(0, $limit);

            //2. limit("10, 50")
        } else if ( is_string( $limit ) ) {
            return explode(',', $limit);
        }
        return $limit;
    }

    /**
     * 组合查询条件
     * @param
     * @return string
     */
    public static function where($where=null) {

        if ( !$where || empty($where) ) return array();

        /**
         * 基于 key => value 数组语法的查询条件解析,这里借鉴的是mongodb的查询语法，以便兼容mongodb
         * array('name' => 'zhangsan', '|age' => '>12')
         * 转换后：array('name' => 'zhangsan', '$or' => array('age' => array('$lt' => 24, '$gt' => 30)))
         */
        $condi = array();
        foreach ( $where as $key => $value ) {

            if ( $key == '$or' ) { //组合or查询
                $condi['$or'] = array($value);
                continue;
            }
            if ( $key == '$and' ) {
                $condi['$and'] = array($value);
                continue;
            }
            //这里判断是AND,OR
            if ( $key[0] == '|' ) {
                $key = substr($key, 1);
                $condi['$or'][] = array($key => $value);
            } else {
                $condi[$key] = self::getFormatValue($value);
            }

            if ( is_array($value) ) {

                foreach ( $value as $key1 => $value1 ) {
                    /**
                     * 3. IN 查询,支持2种形式
                     * array('id' => array('$in' => array(1,2,3)))
                     * array('id' => array('$in' => '1,2,3'))
                     */
                    if ( $key1 == '$in' || $key1 == '$nin' ) {
                        if ( is_string($value1) ) {
                            $value[$key1] = explode(',', $value1);
                        }
                        $condi[$key] = $value;
                        continue;
                    }

                    //4. like查询 array('title' => array('$like' => 'xabc'))
                    if ( $key1 == '$like' ) {
                        $condi[$key] = array('$regex' => $value[$key1]);
                        continue;
                    }

                } //end foreach

            } //end fi
        } //end foreach

        self::replaceConditionOperation($condi, $arr);

        return $arr;
    }

    /**
     * 获取正确格式的字段值
     * @param $value
     * @return string
     */
    public static function getFormatValue($value) {

        if ( $value instanceof \MongoId ) return $value;

        $opt = substr($value, 0, 2);
        //1.包含双字符操作符的
        if ( isset(self::$operator[$opt]) ) {
            //获取真正的value
            $_value = substr($value, 2);
            return array(self::$operator[$opt] => $_value);
        }

        //2.包含单字符操作符的
        if ( isset(self::$operator[$value[0]]) ) {
            //获取真正的value
            $_value = substr($value, 1);
            return array(self::$operator[$value[0]] => $_value);
        }

        return $value;
    }

    /**
     * 遍历条件数组，替换操作符 '>' => '$gt'...
     * @param $value
     * @return string
     */
    public static function replaceConditionOperation($arr, &$result) {

        foreach ( $arr as $key => $value ) {

            if ( is_numeric($value) ) {  //这里时mongodb的坑，严格区分整数和字符串
                $value = intval($value);
            }

            if ( isset(self::$operator[$key]) ) {
                $result[self::$operator[$key]] = $value;
                continue;
            }
            if ( is_array($value) ) {
                self::replaceConditionOperation($value, $result[$key]);
            } else {
                $result[$key] = $value;
            }
        }
    }

}

