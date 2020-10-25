<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use app\admin\model\Admin as AdminModel;

/**
 * 基类模型
 * @author 牧羊人
 * @date 2018/12/8
 * Class BaseModel
 * @package app\common\model
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))    高山仰止,景行行止.虽不能至,心向往之.
 *
 */
class BaseModel extends CacheModel
{
    // 自动写入时间戳字段,true开启false关闭
    protected $autoWriteTimestamp = true;
    // 创建时间字段自定义,默认create_time
    protected $createTime = 'create_time';
    // 更新时间字段自定义,默认update_time
    protected $updateTime = 'update_time';

    /**
     * 初始化模型
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function initialize()
    {
        parent::initialize();
        //TODO...
    }

    /**
     * 添加或编辑
     * @param array $data 数据源
     * @param string $error 错误提示
     * @param bool $is_sql 是否打印SQL
     * @return int|string 返回记录ID
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author zongjl
     * @date 2018/12/8
     */
    public function edit($data = [], &$error = '', $is_sql = false)
    {
        // 基础参数设置
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        if ($id) {
            // 更新时间
            if (empty($data['update_time'])) {
                $data['update_time'] = time();
            }
            // 更新人
            if (empty($data['update_user'])) {
                $data['update_user'] = session('admin_id');
            }
        } else {
            // 添加时间
            if (empty($data['create_time'])) {
                $data['create_time'] = time();
            }
            // 添加人
            if (empty($data['create_user'])) {
                $data['create_user'] = session('admin_id');
            }
        }

        // 格式化表数据
        $this->formatData($data, $id);

        // 创建数据,并验证
        if (!$this->create()) {
            // 验证失败
            $error = $this->getError();
        }

        // 入库处理
        if ($id) {
            //修改数据
            $result = $this->update($data, ['id' => $id]);
            // 更新ID
            $rowId = $id;
        } else {
            // 新增数据
            $result = $this->insertGetId($data);
            // 新增ID
            $rowId = $result;
        }

        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }

        if ($result !== false) {
            // 重置缓存
            $data['id'] = $rowId;
            $this->cacheReset($rowId, $data, $id);
        }
        return $rowId;
    }

    /**
     * 格式化表数据
     * @param array $data 数据源
     * @param int $id 记录ID
     * @param string $table 表字段数据
     * @return array 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author zongjl
     * @date 2018/12/8
     */
    private function formatData(&$data = [], $id = 0, $table = '')
    {
        $data_list = [];
        $tables = $table ? explode(",", $table) : array($this->getTable());
        $item_data = [];
        foreach ($tables as $table) {
            $temp_data = [];
            $table_fields_list = $this->getTableFieldsList($table);
            foreach ($table_fields_list as $field => $field_info) {
                if ($field == "id") {
                    continue;
                }
                // 强制类型转换
                if (isset($data[$field])) {
                    if ($field_info['Type'] == "int") {
                        $item_data[$field] = (int)$data[$field];
                    } else {
                        $item_data[$field] = (string)$data[$field];
                    }
                }
                if (!isset($data[$field]) && in_array($field, array('update_time', 'create_time'))) {
                    continue;
                }
                // 插入数据-设置默认值
                if (!$id && !isset($data[$field])) {
                    $item_data[$field] = $field_info['Default'];
                }
                if (isset($item_data[$field])) {
                    $temp_data[$field] = $item_data[$field];
                }
            }
            $data_list[] = $temp_data;
        }
        $data = $item_data;
        return $data_list;
    }

    /**
     * 获取表字段
     * @param string $table 数据表名称
     * @return array 表字段
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2018/12/8
     */
    private function getTableFieldsList($table = '')
    {
        $table = $table ? $table : $this->getTable();
        $field_list = $this->query("SHOW FIELDS FROM {$table}");
        $info_list = [];
        foreach ($field_list as $row) {
            if ((strpos($row['Type'], "int") === false) ||
                (strpos($row['Type'], "bigint") !== false)) {
                $type = "string";
                $default = $row['Default'] ? $row['Default'] : "";
            } else {
                $type = "int";
                $default = $row['Default'] ? $row['Default'] : 0;
            }
            $info_list[$row['Field']] = array(
                'Type' => $type,
                'Default' => $default
            );
        }
        return $info_list;
    }

    /**
     * 获取缓存信息
     * @param int $id 记录ID
     * @return mixed 返回结果
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getInfo($id)
    {
        // 获取参数(用户提取操作人信息)
        $arg_list = func_get_args();
        $flag = isset($arg_list[0]) ? $arg_list[0] : 0;

        $info = $this->getCacheFunc("info", $id);
        if ($info) {
            // 添加时间
            if (isset($info['add_time'])) {
                $info['format_add_time'] = date('Y-m-d H:i:s', $info['add_time']);
            }

            //更新时间
            if (isset($info['upd_time'])) {
                $info['format_upd_time'] = date('Y-m-d H:i:s', $info['upd_time']);
            }

            // 获取操作人信息
            if ($flag) {
                // 获取全部人员信息
                $admin_model = new AdminModel();
                $admin_all = $admin_model->getAll([], false, true);

                // 添加人
                if (isset($info['create_user']) && $info['create_user']) {
                    $info['format_create_user'] = $admin_all[$info['create_user']]['realname'];
                }

                // 更新人
                if (isset($info['update_user']) && $info['update_user']) {
                    $info['format_update_user'] = $admin_all[$info['update_user']]['realname'];
                }
            }

            // 格式化信息
            if (method_exists($this, 'formatInfo')) {
                $info = $this->formatInfo($info);
            }
        }
        return $info;
    }

    /**
     * 格式化信息
     * @param $info 数据信息
     * @return mixed 返回结果
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function formatInfo($info)
    {
        // 基类方法可不做任何操作，在子类重写即可
        // TODO...
        return $info;
    }

    /**
     * 删除记录
     * @param int $id 记录ID
     * @param bool $is_sql 是否打印SQL
     * @return int 返回结果
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function drop($id, $is_sql = false)
    {
        // 设置mark值为0
        $result = $this->where(['id' => $id])->setField('mark', '0');
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        if ($result !== false) {
            //删除成功
            $this->cacheDelete($id);
        }
        return $result;
    }

    /**
     * 查询记录总数
     * @param array $map 查询条件
     * @param string $fields 字段名称
     * @param bool $is_sql 是否打印SQL
     * @return int 返回结果
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getCount($map = [], $fields = '', $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        if ($fields) {
            $count = $query->count($fields);
        } else {
            $count = $query->count();
        }
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return (int)$count;
    }

    /**
     * 获取某个字段的求和值
     * @param array $map 查询条件
     * @param string $field 字段名称
     * @param bool $is_sql 是否打印SQL
     * @return float 返回结果
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getSum($map = [], $field = 'id', $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        $result = $query->sum($field);
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return $result;
    }

    /**
     * 获取某个字段的最大值
     * @param array $map 查询条件
     * @param string $field 字段名称
     * @param bool $force 是否强制true或false
     * @param bool $is_sql 是否打印SQL
     * @return mixed 返回结果
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getMax($map = [], $field = 'id', $force = true, $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        $result = $query->max($field, $force);
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return $result;
    }

    /**
     * 获取某个字段的最小值
     * @param array $map 查询条件
     * @param string $field 字段名称
     * @param bool $force 是否强制true或false
     * @param bool $is_sql 是否打印SQL
     * @return mixed 返回结果
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getMin($map = [], $field = 'id', $force = true, $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        $result = $query->min($field, $force);
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return $result;
    }

    /**
     * 获取某个字段的平均值
     * @param array $map 查询条件
     * @param string $field 字段名称
     * @param bool $is_sql 是否打印SQL
     * @return float 返回结果
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getAvg($map = [], $field = 'id', $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        $result = $query->avg($field);
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return $result;
    }

    /**
     * 获取某个字段的值
     * @param array $map 查询条件
     * @param string $field 字段名称
     * @param bool $is_sql 是否打印SQL
     * @return mixed 返回结果
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getValue($map = [], $field = 'id', $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        $result = $query->value($field);
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return $result;
    }

    /**
     * 查询单条记录
     * @param array $map 查询条件
     * @param bool $field 字段名称
     * @param bool $is_sql 是否打印SQL
     * @return array 返回结果
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getOne($map = [], $field = true, $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        $result = $query->field($field)->find();
        // 对象转数组
        $result = $result ? $result->toArray() : [];
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return result;
    }

    /**
     * 根据ID获取某一行的值
     * @param int $id 记录ID
     * @param bool $field 字段名称
     * @param bool $is_sql 是否打印SQL
     * @return array 返回结果
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getRow($id, $field = true, $is_sql = false)
    {
        // 链式操作
        $result = $this->where('id', $id)->field($field)->find();
        $result = $result ? $result->toArray() : [];
        // 打印SQL
        if ($is_sql) {
            $this->getLastSql();
        }
        return $result;
    }

    /**
     * 获取某一列的值
     * @param array $map 查询条件
     * @param string $field 字段名称
     * @param string $key 数组键名
     * @param bool $is_sql 是否打印SQL
     * @return array 返回结果
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getColumn($map = [], $field = 'id', $key = '', $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);
        // 链式操作
        if ($key) {
            $result = $query->column($field, $key);
        } else {
            $result = $query->column($field);
        }
        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }
        return $result;
    }

    /**
     * 根据条件查询单条缓存记录
     * @param array $map 查询条件
     * @param array $fields 字段名
     * @param array $sort 排序方式
     * @param int $id 记录ID
     * @return array|mixed 返回结果
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getInfoByAttr($map = [], $fields = [], $sort = "id desc", $id = 0)
    {
        // 排除主键
        if ($id) {
            if (is_array($map)) {
                $map[] = ['id', '!=', $id];
            } elseif ($map) {
                $map .= " AND id != {$id}";
            } else {
                $map .= "id != {$id}";
            }
        }

        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);

        // 排序
        if (is_array($sort)) {
            // 闭包解析排序
            $query = $query->when($sort, function ($query, $sort) {
                foreach ($sort as $v) {
                    $query->order($v[0], $v[1]);
                }
            });
        } else {
            // 普通排序
            $query->order($sort);
        }

        // 链式操作
        $result = $query->field('id')->find();

        // 对象转数组
        $result = $result ? $result->toArray() : [];

        // 查询缓存
        $data = [];
        if ($result) {
            $info = $this->getInfo($result['id']);
            if ($info && !empty($fields) && $fields != "*") {
                // 逗号','分隔字段转数组
                if (!is_array($fields)) {
                    $fields = explode(',', $fields);
                }
                foreach ($fields as $val) {
                    $data[trim($val)] = $info[trim($val)];
                }
                unset($info);
            } else {
                $data = $info;
            }
        }
        return $data;
    }

    /**
     * 获取全部数据表
     * @return array 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getTablesList()
    {
        $tables = [];
        $database = strtolower(config('database.database'));
        $sql = 'SHOW TABLES';
        $data = $this->query($sql);
        foreach ($data as $v) {
            $tables[] = $v["Tables_in_{$database}"];
        }
        return $tables;
    }

    /**
     * 检查表是否存在
     * @param string $table 数据表名称
     * @return bool 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function tableExists($table)
    {
        $tables = $this->getTablesList();
        return in_array(DB_PREFIX . $table, $tables) ? true : false;
    }

    /**
     * 删除数据表
     * @param string $table 数据表名称
     * @return mixed 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function dropTable($table)
    {
        if (strpos($table, DB_PREFIX) === false) {
            $table = DB_PREFIX . $table;
        }
        return $this->query("DROP TABLE {$table}");
    }

    /**
     * 获取数据表字段
     * @param string $table 数据表名称
     * @return array 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getFieldsList($table)
    {
        if (strpos($table, DB_PREFIX) === false) {
            $table = DB_PREFIX . $table;
        }
        $fields = [];
        $data = $this->query("SHOW COLUMNS FROM {$table}");
        foreach ($data as $v) {
            $fields[$v['Field']] = $v['Type'];
        }
        return $fields;
    }

    /**
     * 检查字段是否存在
     * @param string $table 数据表名称
     * @param string $field 字段名称
     * @return bool 返回结果
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function fieldExists($table, $field)
    {
        $fields = $this->getFieldsList($table);
        return array_key_exists($field, $fields);
    }

    /**
     * 插入数据
     * @param array $data 数据源
     * @param bool $get_id 是否返回记录ID,默认true
     * @param bool $is_sql 是否打印SQL
     * @return int|string 返回结果
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function doInsert($data, $get_id = true, $is_sql = false)
    {
        if ($get_id) {
            // 插入数据并返回主键
            $result = $this->insertGetId($data);
        } else {
            // 返回影响数据的条数，没修改任何数据返回 0
            $result = $this->insert($data);
        }
        // 打印SQL
        if ($is_sql) {
            $this->getLastSql();
        }
        return $result;
    }

    /**
     * 更新数据
     * @param array $data 数据源
     * @param $where 查询条件
     * @param bool $is_sql 是否打印SQL
     * @return int|string 返回结果
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function doUpdate($data, $where, $is_sql = false)
    {
        $result = $this->where($where)->update($data);
        // 打印SQL
        if ($is_sql) {
            $this->getLastSql();
        }
        return $result;
    }

    /**
     * 物理删除数据
     * @param $where 查询条件
     * @param bool $is_sql 是否打印SQL
     * @return int 返回结果
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function doDelete($where, $is_sql = false)
    {
        $result = $this->where($where)->delete();
        // 打印SQL
        if ($is_sql) {
            $this->getLastSql();
        }
        return $result;
    }

    /**
     * 批量插入数据
     * @param array $data 数据源
     * @param bool $is_cache 是否设置缓存true或false
     * @return bool|int|string 返回结果
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function insertDAll($data, $is_cache = true)
    {
        if (!is_array($data)) {
            return false;
        }
        if ($is_cache) {
            // 插入数据并设置缓存
            $num = 0;
            foreach ($data as $val) {
                $result = $this->edit($val);
                if ($result) {
                    $num++;
                }
            }
            return $num ? true : false;
        } else {
            // 插入数据不设置缓存
            return $this->insertAll($data);
        }
        return false;
    }

    /**
     * 批量更新数据
     * @param array $data 数据源
     * @param bool $is_cache 是否设置缓存true或false
     * @return bool 返回结果
     * @throws \Exception
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function saveDAll($data, $is_cache = true)
    {
        if (!is_array($data)) {
            return false;
        }

        // 受影响行数
        $num = 0;
        if (!$is_cache) {
            // 批量更新数据(不设置缓存)
            $result = $this->saveAll($data);
            $num = $result;
        } else {
            // 批量更新数据(同步更新缓存)
            foreach ($data as $val) {
                if (!isset($val['id']) || empty($val['id'])) {
                    continue;
                }
                // 更新数据并设置缓存
                $result = $this->edit($val);
                if ($result) {
                    $num++;
                }
            }
        }
        return $num ? true : false;
    }

    /**
     * 批量删除
     * @param array $data 记录ID(数组或逗号','分隔ID)
     * @param bool $is_force 是否物理删除true或false
     * @return bool 返回结果
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function deleteDAll($data, $is_force = false)
    {
        if (empty($data)) {
            return false;
        }
        if (!is_array($data)) {
            $data = explode(',', $data);
        }

        $num = 0;
        foreach ($data as $val) {
            if ($is_force) {
                // 物理删除
                $result = $this->where('id', $val)->delete();
                if ($result) {
                    $this->cacheDelete($val);
                }
            } else {
                // 软删除
                $result = $this->drop($val);
            }
            if ($result) {
                $num++;
            }
        }
        return $num ? true : false;
    }

    /**
     * 查询多条记录
     * @param array $map 查询条件
     * @param string $order 排序
     * @param string $limit 限制条数
     * @param bool $is_sql 是否打印SQL
     * @return array 返回结果
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function getList($map = [], $order = 'id desc', $limit = '', $is_sql = false)
    {
        // 闭包查询条件格式化
        $query = $this->formatQuery($this, $map);

        // 链式操作
        $query = $query->order($order);

        // 分页设置
        if ($limit) {
            list($offset, $page_size) = explode(',', $limit);
            $query = $query->limit($offset, $page_size);
        }
        // 查询结果
        $result = $query->column("id");

        // 打印SQL
        if ($is_sql) {
            echo $this->getLastSql();
        }

        $list = [];
        if ($result) {
            foreach ($result as $val) {
                $info = $this->getInfo($val);
                if (!$info) {
                    continue;
                }
                $list[] = $info;
            }
        }
        return $list;
    }

    /**
     * 格式化查询条件
     * @param $model 当前模型
     * @param array $map 查询条件
     * @return mixed 返回结果
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function formatQuery($model, $map = [])
    {
        if (is_array($map)) {
            $map[] = ['mark', '=', 1];
        } elseif ($map) {
            $map .= " AND mark=1 ";
        } else {
            $map .= " mark=1 ";
        }
        $query = $model->where($map);
        return $query;
    }

    /**
     * 启动事务
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function startTrans()
    {
        // 事务-缓存相关处理
        $GLOBALS['trans'] = true;
        $transId = uniqid("trans_");
        $GLOBALS['trans_id'] = $transId;
        $GLOBALS['trans_keys'] = [];
        $info = debug_backtrace();
        $this->setCache($transId, $info[0]);

        // 启动事务
        Db::startTrans();
    }

    /**
     * 回滚事务
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function rollback()
    {
        // 回滚事务
        Db::rollback();

        // 回滚缓存处理
        foreach ($GLOBALS['trans_keys'] as $key) {
            $this->deleteCache($key);
        }
        $this->deleteCache($GLOBALS['trans_id']);
        $GLOBALS['trans'] = false;
        $GLOBALS['trans_keys'] = [];
    }

    /**
     * 提交事务
     * @author 牧羊人
     * @date 2018/12/8
     */
    public function commit()
    {
        // 提交事务
        Db::commit();

        // 事务缓存同步删除
        $GLOBALS['trans'] = false;
        $GLOBALS['trans_keys'] = [];
        $this->deleteCache($GLOBALS['trans_id']);
    }
}
