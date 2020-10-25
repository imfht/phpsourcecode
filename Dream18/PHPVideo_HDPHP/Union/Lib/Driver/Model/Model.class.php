<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------

/**
 * 基本模型处理类
 *
 * @package     Model
 * @author      后盾向军 <2300071698@qq.com>
 */
class Model
{
    //数据表
    public $table = null;
    //数据库连接驱动对象
    public $db = null;
    //触发器状态
    public $trigger = true;
    //模型错误信息
    public $error;
    //模型操作数据
    public $data = array();
    //验证规则
    public $validate = array();
    //自动完成规则
    public $auto = array();
    //字段映射规则
    public $map = array();
    //别名方法
    public $alias
        = array('add' => 'insert', 'save' => 'update', 'all' => 'select',
                'del' => 'delete');

    /**
     * 构造函数
     *
     * @param null  $table  表名
     * @param bool  $full   全表名
     * @param null  $driver 驱动
     * @param array $param  参数
     */
    public function __construct(
        $table = null, $full = null, $param = array(), $driver = null
    ) {
        /**
         * 初始化默认表
         * 如果设置了table属性时,使用table属性值为表名
         */
        if ( ! $this->table) {
            $this->table = $table;
        }
        /**
         * 获得数据库引擎
         */
        $this->db = DbFactory::factory($driver, $this->table, $full);
        /**
         * 执行子类构造函数__init
         */
        if (method_exists($this, "__init")) {
            $this->__init($param);
        }
    }

    /**
     * 魔术方法  设置模型属性如表名字段名
     *
     * @param string $var   属性名
     * @param mixed  $value 值
     *
     * @return object
     */
    public function __set($var, $value)
    {
        /**
         * 设置$data属性值用于插入修改等操作
         */
        $this->data[$var] = $value;
    }

    /**
     * 魔术方法
     *
     * @param $name 变量
     *
     * @return mixed
     */
    public function __get($name)
    {
        /**
         * 返回$this->data属性
         * $this->data属性指添加与编辑的数据
         */
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
    }

    /**
     * 魔术方法用于动态执行Db类中的方法
     *
     * @param $method
     * @param $param
     *
     * @return mixed
     */
    public function __call($method, $param)
    {
        /**
         * 执行别名函数
         * 如add 是insert别名,执行add时执行insert方法
         */
        if (isset($this->alias[$method])) {
            return call_user_func_array(
                array($this, $this->alias[$method]), $param
            );
        } else if (method_exists($this->db, $method)) {
            $RETURN = call_user_func_array(array($this->db, $method), $param);

            return $RETURN === null ? $this : $RETURN;
        }
    }

    /**
     * 重置模型
     */
    protected function __reset()
    {
        /**
         * 重置更新、插件数据
         */
        $this->data = array();
        /**
         * 开启触发器
         */
        $this->trigger = true;
    }

    /**
     * 获得添加、插入数据
     *
     * @param array $data void
     *
     * @return array|null
     */
    public function data($data = array())
    {
        if (empty($data)) {
            /**
             * 数据为空时使用$_POST值
             */
            if (empty($this->data)) {
                $this->data = $_POST;
            }
        } else {
            $this->data = $data;
        }
        /**
         * 系统开启转义时,去除转义操作
         */
        foreach ($this->data as $key => $val) {
            if (MAGIC_QUOTES_GPC && is_string($val)) {
                $this->data[$key] = stripslashes($val);
            }
        }

        return $this;
    }

    /**
     * 执行自动映射、自动验证、自动完成
     *
     * @param array $data 如果为空使用$_POST
     *
     * @return bool
     */
    public function create($data = array())
    {
        /**
         * 初始数据
         */
        $this->data($data);
        /**
         * 批量执行方法
         * validate 自动验证
         * auto 自动完成
         * map 自动映射
         */
        $action = array('validate', 'auto', 'map');
        foreach ($action as $a) {
            if ( ! $this->$a()) {
                return false;
            }
        }

        return true;
    }

    /**
     * 字段映射
     * 将添加或更新的数据键名改为表字段名
     */
    public function map($data = array())
    {
        $this->data($data);
        if ( ! empty($this->map)) {
            foreach ($this->map as $k => $v) {
                //处理POST
                if (isset($this->data[$k])) {
                    $this->data[$v] = $this->data[$k];
                    unset($this->data[$k]);
                }
            }
        }

        return true;
    }

    /**
     * 当前操作的方法
     * 主要是判断数据中是否存在主键,有主键为更新操作,否则为添加操作
     * 1为插入操作 2为更新操作
     *
     * @return int
     */
    private function getCurrentMethod()
    {
        //1 插入  2 更新
        return isset($this->data[$this->db->pri]) ? 2 : 1;
    }


    /**
     * 字段验证
     * 验证字段合法性,支持自定义函数,模型方法与Validate验证类方法的操作
     *
     * @return bool
     */
    public function validate($data = array())
    {
        /**
         * 验证规则为空时不验证
         */
        if (empty($this->validate)) {
            return true;
        }
        /**
         * 操作数据
         */
        $this->data($data);
        $data = &$this->data;
        /**
         * 当前方法
         */
        $motion = $this->getCurrentMethod();
        /**
         * 验证处理
         */
        foreach ($this->validate as $v) {
            //表单名称
            $name = $v[0];
            /**
             * 验证条件
             * 1 有表单时
             * 2 必须验证
             * 3 不为空时
             */
            $condition = isset($v[3]) ? $v[3] : 2;
            /**
             * 验证时机
             * 1 插入时
             * 2 更新时
             * 3 插入与更新
             */
            $action = isset($v[4]) ? $v[4] : 3;
            /**
             * 验证时间判断
             */
            if ( ! in_array($action, array($motion, 3))) {
                continue;
            }
            /**
             * 错误信息
             */
            $msg = $v[2];
            switch ($condition) {
                case 1 :
                    //有表单时
                    if ( ! isset($data[$name])) {
                        continue 2;
                    }
                    break;
                case 2 :
                    //必须验证
                    if ( ! isset($data[$name])) {
                        $this->error = $msg;

                        return false;
                    }
                    break;
                case 3 :
                    //不为空时
                    if (empty($data[$name])) {
                        continue 2;
                    }
                    break;
            }
            if ($pos = strpos($v[1], ':')) {
                $func = substr($v[1], 0, $pos);
                $args = substr($v[1], $pos + 1);
            } else {
                $func = $v[1];
                $args = '';
            }
            /**
             * 执行模型方法
             */
            if (method_exists($this, $func)) {
                $res = call_user_func_array(
                    array($this, $func),
                    array($name, $data[$name], $msg, $args)
                );
                /**
                 * 验证失败
                 */
                if ($res !== true) {
                    $this->error = $res;

                    return false;
                }
            } else if (function_exists($func)) {
                /**
                 * 函数验证
                 */
                $res = $func($name, $data[$name], $msg, $args);
                /**
                 * 验证失败
                 */
                if ($res !== true) {
                    $this->error = $res;

                    return false;
                }
            } else {
                /**
                 * Validate验证类处理
                 */
                $validate = new Validate();
                $func     = '_' . $func;
                if (method_exists($validate, $func)) {
                    $res = call_user_func_array(
                        array($validate, $func),
                        array($name, $data[$name], $msg, $args)
                    );
                    /**
                     * 验证失败
                     */
                    if ($res !== true) {
                        $this->error = $res;

                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * 自动完成
     *
     * @param array $data
     *
     * @return bool
     */
    public function auto($data = array())
    {
        /**
         * 获取数据
         */
        $this->data($data);
        $data = &$this->data;
        /**
         * 处理时机
         * 1 插入
         * 2 更新
         */
        $motion = $this->getCurrentMethod();
        foreach ($this->auto as $v) {
            /**
             * 表单名
             */
            $name = $v[0];
            /**
             * 方法名
             */
            $method = $v[1];
            /**
             * 执行方式
             * function 函数
             * method   模型方法
             * string   字符串
             */
            $handle = isset($v[2]) ? $v[2] : "string";
            /**
             * 处理条件
             * 1 有表单时
             * 2 必须处理
             * 3 值不为空时
             * 4 值为空时
             */
            $condition = isset($v[3]) ? $v[3] : 1;
            /**
             * 处理时机
             * 1 插入时处理
             * 2 更新时处理
             * 3 插入与更新都处理
             */
            $action = isset($v[4]) ? $v[4] : 3;
            /**
             * 验证处理时机
             */
            if ( ! in_array($action, array($motion, 3))) {
                continue;
            }
            switch ($condition) {
                case 1 :
                    //不存在字段时
                    if ( ! isset($data[$name])) {
                        continue 2;
                    }
                    break;
                case 2 :
                    // 必须处理
                    if ( ! isset($data[$name])) {
                        $data[$name] = '';
                    }
                    break;
                case 3 :
                    //值不为空时
                    if (empty($data[$name])) {
                        continue 2;
                    }
                    break;
                case 4:
                    //值为空时
                    if (empty($data[$name])) {
                        $data[$name] = '';
                    } else {
                        continue 2;
                    }
                    break;
            }
            $data[$name] = isset($data[$name]) ? $data[$name] : '';
            switch ($handle) {
                case "function" :
                    //函数
                    if (function_exists($method)) {
                        $data[$name] = $method($data[$name]);
                    }
                    break;
                case "method" :
                    //模型方法
                    if (method_exists($this, $method)) {
                        $data[$name] = $this->$method($data[$name]);
                    }
                    break;
                case "string" :
                    //字符串
                    $data[$name] = $method;
                    break;
            }
        }

        return true;
    }

    /**
     * 设置触发器状态
     *
     * @param $status
     *
     * @return bool
     */
    public function trigger($status)
    {
        $this->trigger = $status;

        return $this;
    }

    /**
     * 删除数据
     *
     * @param string $where 条件
     *
     * @return mixed
     */
    public function delete($where = '')
    {
        $this->trigger && method_exists($this, '__before_delete')
        && $this->__before_delete();
        $return = $this->db->delete($where);
        $this->trigger && method_exists($this, '__after_delete')
        && $this->__after_delete($return);
        /**
         * 重置模型
         */
        $this->__reset();

        return $return;
    }

    /**
     * 查找满足条件的一条记录
     *
     * @param string $where 条件,如果为数字查询主键值
     *
     * @return mixed
     */
    public function find($where = '')
    {
        $result = $this->select($where);

        return is_array($result) ? current($result) : $result;
    }

    /**
     * 查询结果
     *
     * @param string $where 条件
     *
     * @return mixed
     */
    public function select($where = '')
    {
        $this->trigger && method_exists($this, '__before_select')
        && $this->__before_select();
        $return = $this->db->select($where);
        $this->trigger && method_exists($this, '__after_select')
        && $this->__after_select($return);
        /**
         * 重置模型
         */
        $this->__reset();

        return $return;
    }

    /**
     * 更新数据
     *
     * @param array $data 更新的数据
     *
     * @return mixed
     */
    public function update($data = array())
    {
        $this->data($data);
        $this->trigger && method_exists($this, '__before_update')
        && $this->__before_update($this->data);
        $return = $this->db->update($this->data);
        $this->trigger && method_exists($this, '__after_update')
        && $this->__after_update($return);
        /**
         * 重置模型
         */
        $this->__reset();

        return $return;
    }

    /**
     * 插入数据
     *
     * @param array $data 新数据
     *
     * @return mixed
     */
    public function insert($data = array())
    {
        $this->data($data);
        $this->trigger && method_exists($this, '__before_insert')
        && $this->__before_insert($this->data);
        $return = $this->db->insert($this->data);
        $this->trigger && method_exists($this, '__after_insert')
        && $this->__after_insert($return);
        /**
         * 重置模型
         */
        $this->__reset();

        return $return;
    }

    /**
     * replace方式插入数据
     * 更新数据中存在主键或唯一索引数据为更新操作否则为添加操作
     *
     * @param array $data
     *
     * @return mixed
     */
    public function replace($data = array())
    {
        $this->data($data);
        $this->trigger && method_exists($this, '__before_insert')
        && $this->__before_insert($this->data);
        $return = $this->db->replace($this->data);
        $this->trigger && method_exists($this, '__after_insert')
        && $this->__after_insert($return);
        /**
         * 重置模型
         */
        $this->__reset();

        return $return;
    }
}