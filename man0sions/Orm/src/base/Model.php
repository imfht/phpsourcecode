<?php
/**
 * Created by PhpStorm.
 * User: man0sions
 * Date: 16/8/27
 * Time: 下午3:19
 */

namespace LuciferP\Orm\base;


/**
 * 简单的orm --activerecord
 * Class Model
 * @package LuciferP\Orm\base\api
 * @author Luficer.p <81434146@qq.com>
 */
class Model
{
    /**
     * @var
     */
    protected $table;
    /**
     * @var null
     */
    private $db;

    /**
     * @var array
     */
    private $attributes = [];
    /**
     * @var bool
     */
    private $new_record;
    /**
     * @var array
     */
    private $sql = [
        'where' => '',
        'order' => '',
        'limit' => '',
        'params' => [],
        'fields' => '*'
    ];


    /**
     * Model constructor.
     * @param bool $new_record
     */
    public function __construct($new_record = true)
    {
        $this->new_record = $new_record;
        $this->db = Factory::getDb();
    }

    /**
     * @return static
     */
    public static function model()
    {
        return new static($new_record = false);
    }

    /**
     * findall 方法返回的是一个数组对象,数组中的每一个对象都可以进行update,delete,操作
     * @return array
     */
    public function findAll()
    {

        $prepare = $this->queryStmt()['prepare'];

        $data = $prepare->fetchAll();
        $ret = [];
        foreach ($data ? $data : [] as $value) {
            $obj = clone $this;
            foreach ($value as $key => $item) {
                $obj->$key = $item;
            }
            $ret[] = $obj;

        }

        return $ret;

    }

    /**
     * @return mixed
     */
    private function queryStmt()
    {

        $sql = "SELECT {$this->sql['fields'] } FROM {$this->table} {$this->sql['where']} {$this->sql['order']} {$this->sql['limit']}";
//        echo $sql."\n";
        $query = $this->db->query($sql, $this->sql['params']);
        return $query;
    }

    /**
     * @return int
     */
    public function count()
    {
        $ret = $this->fields(["count(*) as ct"])->find();
        if ($ret->getAttributes()) {
            return intval($ret->getAttributes()['ct']);
        }
        return 0;
    }

    /**
     * @return $this
     */
    public function find()
    {

        $prepare = $this->queryStmt()['prepare'];

        $data = $prepare->fetch();
        foreach ($data ? $data : [] as $key => $value) {
            $this->$key = $value;

        }

        return $this;

    }

    /**
     * @param array $fields
     * @return $this
     */
    public function fields($fields = ['*'])
    {
        $fields = join(",", $fields);
        $this->sql['fields'] = $fields;
        return $this;

    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return mixed
     */
    public function save()
    {

        if ($this->new_record) {
            return $this->create();
        } else {
            return $this->update();
        }

    }

    /**
     * @return mixed
     */
    public function create()
    {
        $sql = "INSERT INTO {$this->table} ";
        $fields = [];
        $values = [];

        foreach ($this->attributes as $name => $value) {
            $fields["`$name`"] = ":$name";
            $values[$name] = $value;
        }
        $keys = join(',', array_keys($fields));
        $vals = join(',', array_values($fields));
        $sql .= "(" . $keys . ") value(" . $vals . ")";
        $ret = $this->db->query($sql, $values);
        $this->id = $this->db->getDb()->lastInsertId();
        if ($ret['ret']) {
            return $this;
        } else {
            return false;
        }

    }

    /**
     * @return mixed
     */
    public function update()
    {

        $sql = "UPDATE {$this->table} ";
        $fields = [];
        $values = [];
        $condition = "WHERE id={$this->id}";
        foreach ($this->attributes as $name => $value) {
            if ($name == 'id') {
                continue;
            }
            $fields[$name] = "`$name`=:$name";
            $values[$name] = $value;
        }
        $keys = join(',', ($fields));
        $sql .= "SET $keys $condition";

        $ret = $this->db->query($sql, $values);
        if ($ret['ret']) {
            return $this;
        } else {
            return false;
        }

    }

    /**
     * @return mixed
     * @throws AppException
     */
    public function delete()
    {
        if (!$this->id) {
            throw new AppException("id not exists");
        }

        $sql = "DELETE FROM {$this->table} WHERE id=:id";
        $ret = $this->db->query($sql, ['id' => $this->id]);
        if ($ret['ret']) {
            return $this;
        } else {
            return false;
        }
    }

    /**
     * @param string $where
     * @return $this
     */
    public function where($where = '1')
    {

        if (is_array($where)) {
            $this->sql['params'] = $where;
            $condition = [];
            foreach ($where as $key => $value) {
                $condition[] = "$key=:$key";
            }

            $condition = join(",", $condition);

        } elseif (is_string($where)) {
            $condition = $where;
        }

        $this->sql['where'] = " WHERE $condition";

        return $this;
    }

    /**
     * @param array $order
     * @return $this
     */
    public function order($order = [])
    {
        if ($order) {
            $key = key($order);
            $this->sql['order'] = " ORDER BY " . $key . " " . $order[$key];
        }
        return $this;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return $this
     */
    public function limit($offset = 0, $limit = 10)
    {
        $this->sql['limit'] = " limit {$offset}, {$limit}";
        return $this;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->db->getErrors();
    }

    /**
     * @return null
     */
    public function getId()
    {
        if (isset($this->attributes['id'])) {
            return $this->attributes['id'];
        }
        return null;
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }


}
