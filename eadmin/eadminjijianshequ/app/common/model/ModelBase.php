<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use esclass\database;

/**
 * 模型基类
 */
class ModelBase
{
    /**
     * @var object 对象实例
     */
    protected static $instance;

    protected $class;
    protected $name;

    protected $classname;

    // 查询对象
    private static $ob_query = null;


    /**
     * 构造方法
     */
    public function __construct($name = '')
    {


        self::$instance = database::getInstance();

        if ($name == '') {
            $this->class = get_called_class();
            $name        = str_replace('\\', '/', $this->class);

            $this->classname = basename($name);
        }

        $this->classname = basename($name);

        $this->name = camelcase2underline($this->classname);

        $this->_initialize();

    }

    // 初始化
    protected function _initialize()
    {

    }

    public function setname($name)
    {
        $this->classname = $name;

        $this->name = camelcase2underline($name);

        return $this;

    }

    public function query($sql, array $bindData = [], $fetch = false, $unbuffered = false)
    {

        return self::$instance->query($sql, $bindData, $fetch, $unbuffered);


    }

    public function setIncOrDec($where, $field, $value, $type = '+')
    {

        return self::$instance->table($this->name)->setIncOrDec($where, $field, $value, $type);

    }


    public function getDataValue($where = [], $field = '')
    {


        return self::$instance->table($this->name)->where($where)->value($field);
    }

    public function getDataColumn($where = [], $field = '')
    {


        return self::$instance->table($this->name)->where($where)->column($field);
    }

    /**
     * 统计数据
     */
    public function getStat($where = [], $stat_type = 'count', $field = 'id')
    {


        return self::$instance->table($this->name)->where($where)->$stat_type($field);

    }

    public function setAttr($results)
    {


        $model = model($this->classname);

        $ref = new \ReflectionClass($model);

        $funarr = $ref->getMethods();

        foreach ($funarr as $k => $v) {

            $name = $v->name;

            if ($v->class != 'app\\common\\model\\ModelBase' && preg_match('/^get(.*?)Attr/', $name, $result) > 0) {


                $attrname = camelcase2underline($result[1]);

                $param = object_to_array($v->getParameters());

                $param = $param[0]['name'];

                foreach ($results as $key => $vo) {

                    $results [$key] [$attrname] = $model->$name ($vo [$param]);
                }
            }
        }

        return $results;
    }

    /**
     * 获取信息
     */
    public function getDataInfo($where = [], $field = true, $join = [], $status = false, $alias = '')
    {

        if (!empty($join) && empty($alias)) {
            $alias = 'm';
        }
        !empty($alias) ? self::$ob_query = self::$instance->table($this->name, $alias) : self::$ob_query = self::$instance->table($this->name);

        self::$ob_query = self::$ob_query->where($where)->field($field);

        if (!empty($join)) {

            if (is_array(reset($join))) {
                foreach ($join as $k => $v) {

                    self::$ob_query = self::$ob_query->join($v);
                }
            } else {

                self::$ob_query = self::$ob_query->join($join);


            }


        }


        $result_data = self::$ob_query->getRow();

        if ($status && !empty($result_data)) {

            $result_data = $this->setAttr($result_data);


        }

        return $result_data;
    }

    /**
     * 获取列表
     */
    public function getDataList($where = [], $field = true, $order = 'id desc', $paginate = 0, $join = [], $group = '', $limit = '', $status = false, $alias = '')
    {
        if (!empty($join) && empty($alias)) {
            $alias = 'm';
        }

        !empty($alias) ? self::$ob_query = self::$instance->table($this->name, $alias) : self::$ob_query = self::$instance->table($this->name);

        self::$ob_query = self::$ob_query->where($where)->field($field)->order($order);

        if (!empty($join)) {

            if (is_array(reset($join))) {
                foreach ($join as $k => $v) {

                    self::$ob_query = self::$ob_query->join($v);
                }
            } else {

                self::$ob_query = self::$ob_query->join($join);


            }


        }
        !empty($group) && self::$ob_query = self::$ob_query->group($group);
        !empty($limit) && self::$ob_query = self::$ob_query->limit($limit);
        if (DATA_DISABLE === $paginate) {

            if (webconfig('list_rows') > 0) {

                $paginate = webconfig('list_rows');
            } else {
                $paginate = DB_LIST_ROWS;

            }
        }

        if (false !== $paginate) {

            list($results, $render, $total) = self::$ob_query->paginate($paginate);

            if (!empty($results)) {


                if ($status) {


                    $results = $this->setAttr($results);


                }
                $data['data'] = $results;

            } else {
                $data['data'] = [];

            }

            $data['page']  = $render;
            $data['total'] = $total;
            return $data;

        } else {

            $result_data = self::$ob_query->getList();
            if ($status) {

                $result_data = $this->setAttr($result_data);


            }

            return $result_data;
        }
    }

    /**
     * 设置数据信息
     */
    public function setDataValue($where = [], $field = '', $value = '', $url = '', $info = '状态更新成功', $obj = '', $callback = '')
    {


        $result = self::$instance->table($this->name)->where($where)->update([$field => $value]);

        if ($result !== false) {

            if ($obj) {
                call_user_func([$obj, $callback], $value, $where);
            } else {

                if (!empty($callback)) {
                    call_user_func($callback, $value, $data);
                }


            }
            return [RESULT_SUCCESS, $info, $url];

        } else {


            return [RESULT_ERROR, self::$instance->errorInfo()];
        }

    }

    public function getError()
    {

        return self::$instance->errorInfo();

    }

    /**
     * 数据添加，单次添加
     */
    public function dataAdd($data = [], $isvalidate = true, $url = '', $info = '添加成功', $obj = '', $callback = '')
    {


        if ($isvalidate) {

            if (is_array($isvalidate)) {
                $validate = validate($isvalidate[0]);
            } else {
                $validate = validate($this->classname);
            }


            $validate_result = $validate->scene('add')->check($data);

            if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;

        }

        if (!is_array(reset($data)) && strtolower($this->name) != 'user') {
            $data[TIME_CT_NAME] = time();
        }

        if ($result = self::$instance->table($this->name)->insert($data)) {

            if ($obj) {
                call_user_func([$obj, $callback], $result, $data);
            } else {

                if (!empty($callback)) {
                    call_user_func($callback, $result, $data);
                }


            }


            return [RESULT_SUCCESS, $info, $url, $result];

        } else {
            return [RESULT_ERROR, self::$instance->errorInfo()];
        }


    }

    /**
     * 数据编辑
     */
    public function dataEdit($data = [], $where = [], $isvalidate = true, $url = '', $info = '编辑成功', $obj = '', $callback = '')
    {
        if ($isvalidate) {


            if (is_array($isvalidate)) {
                $validate = validate($isvalidate[0]);
            } else {
                $validate = validate($this->classname);
            }

            $validate_result = $validate->scene('edit')->check($data);

            if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;

        }
        if (!is_array(reset($data)) && strtolower($this->name) != 'user') {
            $data[TIME_UT_NAME] = time();
        }


        if (empty($where)) {

            $pk = self::$instance->getPk($this->name);

            if (empty($data[$pk])) {

                return [RESULT_ERROR, '主键不存在'];
            }

            $where[$pk] = $data[$pk];

            unset($data[$pk]);
        }

        $result = self::$instance->table($this->name)->update($data, $where);

        if ($result !== false) {

            if ($obj) {
                call_user_func([$obj, $callback], $result, $data);
            } else {

                if (!empty($callback)) {
                    call_user_func($callback, $result, $data);
                }


            }


            return [RESULT_SUCCESS, $info, $url, $result];

        } else {
            return [RESULT_ERROR, self::$instance->errorInfo()];
        }


    }


    /**
     * 删除
     */
    public function dataDel($where = [], $info = '删除成功', $is_true = false, $callback = '')
    {
        if ($is_true) {
            if (self::$instance->table($this->name)->delete($where)) {

                if (!empty($callback)) {
                    call_user_func($callback, $where);
                }
                return [RESULT_SUCCESS, $info];
            } else {
                return [RESULT_ERROR, self::$instance->errorInfo()];
            }


        } else {

            $data['status'] = -1;

            $result = self::$instance->table($this->name)->update($data, $where);

            if ($result !== false) {

                if (!empty($callback)) {
                    call_user_func($callback, $where);
                }


                return [RESULT_SUCCESS, $info];

            } else {
                return [RESULT_ERROR, self::$instance->errorInfo()];
            }


        }
    }

}
