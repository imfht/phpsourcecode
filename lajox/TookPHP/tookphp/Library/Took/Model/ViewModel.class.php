<?php
/**
 * 视图模型处理类
 * @package     Model
 * @subpackage  Driver
 * @author      lajox <lajox@19www.com>
 */

namespace Took\Model;
class ViewModel extends \Took\Model
{
    /**
     * 视图关联表关系
     * @var array
     */
    public $view = array();
    /**
     * 这些方法需要改变驱动Db的相应opt['table']与opt['field']等属性值
     * @var array
     */
    private $queryMethod = array(
        'select', 'find', 'count', 'max', 'min', 'avg', 'sum'
    );

    /**
     * 魔术方法用于动态执行Db类中的方法
     * @param $method
     * @param $param
     * @return mixed
     */
    public function __call($method, $param)
    {
        if (in_array($method, $this->queryMethod)) {
            $this->setDriverOption();
        }
        //调用父类方法完成查询操作
        return parent::__call($method, $param);
    }

    /**
     * 设置查询表名与字段
     * 就是设置驱动Db的opt属性
     */
    private function setDriverOption()
    {
        if (empty($this->view)) {
            //没有定义view属性时不进行处理
            return;
        } else {
            //获得本次查询的join与field值
            $this->setJoin();
            $this->setDbField();
        }
    }

    /**
     * 查找满足条件的一条记录
     * @param string $where 条件,如果为数字查询主键值
     * @return mixed
     */
    public function find($where = '')
    {
        $result = $this->select($where);
        return is_array($result) ? current($result) : $result;
    }

    /**
     * 查询结果
     * @param string $where 条件
     * @return mixed
     */
    public function select($where = '')
    {
        //设置查询表与字段
        $this->setDriverOption();
        $this->trigger && method_exists($this, '__before_select') && $this->__before_select();
        $return = $this->db->select($where);
        $this->trigger && method_exists($this, '__after_select') && $this->__after_select($return);
        //重置模型
        $this->__reset();
        return $return;
    }

    /**
     * 更改Db::opt['join']值
     * @return mixed
     */
    private function setJoin()
    {
        $table = preg_replace('@\s+@', ' ', $this->db->opt['table']);
        list($table, $alias) = array_pad(explode(' ',$table),2,'');
        if(empty($alias)) {
            if(isset($this->_alias) && !empty($this->_alias)) {
                $alias = $this->_alias;
            } else {
                $alias = $this->table ? $this->table : $table;
            }
            $this->db->opt['table'] = $table.' '.$alias;
        }
        foreach ($this->view as $table => $set) {
            //表别名设置
            $as = isset($set['_as']) ? $set['_as'] : $table;
            $table = C('DB_PREFIX') . $table . ' ' . $as;
            //_TYPE关联方式
            if (isset($set['_type'])) {
                $joinStr = ' ' . strtoupper($set['_type']) . ' JOIN '. $table;
            } else {
                $joinStr = ' INNER JOIN '. $table;
            }
            //关联条件
            if (isset($set['_on'])) {
                $onStr = " ON  " . $set['_on'];
            }
            $this->db->opt['join'][] = $joinStr.' '.$onStr;
        }
    }

    /**
     * 设置查询字段
     */
    private function setDbField()
    {
        //字段设置. 如果链式操作中调用了field()方法,则不执行以下操作
        if ($this->db->opt['field'] != '*') {
            return $this->db->opt['field'];
        } else {
            $field = '';
            foreach ($this->view as $table => $set) {
                if (!isset($set['_field'])) {
                    //没有定义_field属性时不处理
                    continue;
                } else {
                    $field .= $set['_field'] . ',';
                }
            }
            if (!empty($field)) {
                $this->db->opt['field'] = substr($field, 0, -1);
            }
        }
    }

}