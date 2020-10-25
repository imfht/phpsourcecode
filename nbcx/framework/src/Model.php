<?php
/**
 *
 * User: Collin
 * QQ: 1169986
 * Date: 2017/12/30 下午9:13
 */
namespace nb;

abstract class Model extends Collection {

    //php5.6 貌似不能在抽象类里定义抽象静态方法
    //可以在接口类里定义抽象静态方法，后续有需要可以完善
    //abstract protected static function __config();

    /**
     * 默认以子类的类名作为表名
     * @return array
     */
    protected static function __config() {
        $class = explode('\\',get_called_class());
        return [$class[count($class)-1]];
    }

    /**
     * 根据条件删除对应的记录
     * @param null $condition
     * @param null $params
     * @return int|\PDOStatement
     */
    public static function delete($condition=null, $params=null) {
        return static::dao()->delete($condition,$params);
    }

    /**
     * 通过表主键删除对应的记录
     * @param $id
     * @return int|\PDOStatement
     */
    public static function deleteId($id) {
        return static::dao()->deleteId($id);
    }

    /**
     * 根据表主键修改对应的记录
     * @param $id
     * @param $data
     * @return int
     */
    public static function updateId($id, $data, $params=[], $filter=false) {
        return static::dao()->updateId($id, $data, $params, $filter);
    }

    /**
     * 根据条件修改对应的表记录
     * @param $arr
     * @param null $condition
     * @param array $params
     * @return int
     */
    public static function update($arr, $condition=null, $params=[]) {
        return static::dao()->update(
            $arr,
            $condition,
            $params
        );
    }

    /**
     * 插入一条记录到数据库
     * @param $arr
     * @return int
     */
    public static function insert($arr,$filter=false) {
        return static::dao()->insert($arr,$filter);
    }

    /**
     * 批量插入记录到数据库
     * @param $arr
     * @return int
     */
    public static function inserts($arr, $fieldNames=[]) {
        return static::dao()->inserts($arr,$fieldNames);
    }

    /**
     * 根据指定条件获取符合的记录总数
     * @param null $condition
     * @param null $params
     * @param bool $distinct
     * @return int
     */
    public static function counts($condition = null, $params = null, $distinct=false) {
        return static::dao()->count(
            $condition,
            $params,
            $distinct
        );
    }

    /**
     * 根据表主键查找对应的数据
     * @param $id
     * @param string $fields
     * @return $this
     */
    public static function findId($id, $object = true) {
        return static::dao($object)->findId($id);
    }

    /**
     * 根据条件查找对应的一条记录
     * @param $condition
     * @param null $params
     * @param string $fields
     * @return $this
     */
    public static function find($condition, $params = NULL, $object = true) {
        return static::dao($object)->find($condition, $params);
    }

    /**
     * @param $field
     * @param null $condition
     * @param null $params
     * @param bool $object
     * @return array
     */
    public static function findkv($field,$condition = NULL, $params = NULL, $object = true) {
        return static::dao($object)->kv($field,$condition, $params);
    }

    /**
     * 根据条件查找对应的所有记录，可指定分页
     * @param null $condition
     * @param int $rows
     * @param int $start
     * @param string $order
     * @param string $fields
     * @return mixed
     * @throws \Exception
     */
    public static function finds($condition = NULL, $rows = 0, $start = 0, $order='', $fields = '*', $object = true) {
        return static::dao($object)->finds($condition,$rows,$start,$order,$fields);
    }

    /**
     * 根据条件查找对应的所有记录
     * @param null $condition
     * @param null $params
     * @param null $fields
     * @param string $order
     * @return $this
     */
    public static function fetchs($condition = NULL, $params = NULL, $fields=null, $order='', $object = true) {
        return static::dao($object)->fetchs($condition,$params,$fields,$order);
    }

    /**
     * @param string $condition
     * @param int $rows
     * @param int $start
     * @param string $order
     * @param string $fields
     * @return array
     */
    public static function paginate($rows = 0, $start = 0, $condition = '', $order='', $fields = '*', $object = true) {
        return static::dao($object)->paginate($rows, $start, $condition, $order, $fields);
    }

    /**
     * @return Dao
     */
    public static function dao($object = true,$conf=null)  {
        $class = get_called_class();
        $key = md5($class.$object.json_encode($conf));
        return Pool::value($key,function () use ($object,$conf,$class) {
            $conf = $conf?:static::__config();
            switch (count($conf)) {
                case 1:
                    $table = $conf[0];
                    $pk = 'id';
                    $server = 'dao';
                    break;
                case 2:
                    $table = $conf[0];
                    $pk = $conf[1];
                    $server = 'dao';
                    break;
                case 3:
                    $table = $conf[0];
                    $pk = $conf[1];
                    $server = $conf[2];
                    break;
                default:
                    throw new \Exception('Model dao config error');
                    break;
            }
            $dao = new Dao($table,$pk,$server);
            $object and $dao->object($class);
            return $dao;
        });
    }

}