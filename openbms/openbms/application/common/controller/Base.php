<?php

namespace app\common\controller;

use think\Controller;

class Base extends Controller
{
    protected $model;
    protected $errorMsg = '未知错误';
    protected $insertId = 0;

    protected function _initialize()
    {
        parent::_initialize();
        $system = cache('db_system_data');
        if (!$system) {
            $system = [];
            foreach (model('system')->select() as $v) {
                $system[$v['name']] = $v['value'];
            }
            cache('db_system_data', $system);
        }
        config($system);
    }

    /**
     * 插入记录
     * @param string $name 模型
     * @param array $data 数据
     * @param bool $rule 是否开启认证
     * @param string|array $field 允许写入的字段 如果为true只允许写入数据表字段
     * @param string $key 获取模型的主键，默认是id
     * @return bool|mixed
     */
    public function insert($name, $data, $rule = true, $field = true, $key = 'id')
    {
        try {
            $this->model = model($name);
            if ($this->model->allowField($field)->validate($rule)->save($data)) {
                $this->insertId = $this->model->$key;
                return true;
            } else {
                return $this->errorMsg = $this->model->getError();
            }
        } catch (\Exception $e) {
            return $this->errorMsg = config('app_debug') ? $e->getMessage() : '请求错误';
        }
    }

    /**
     * 更新记录
     * @param string $name 模型
     * @param array $data 数据
     * @param bool $rule 是否开启认证
     * @param string|array $field 允许写入的字段 如果为true只允许写入数据表字段
     * @param string $key 更新条件字段 多个用逗号隔开
     * @return bool|mixed
     */
    public function update($name, $data, $rule = true, $field = true, $key = 'id')
    {
        try {
            $where = [];
            foreach (explode(',', $key) as $v) {
                $where[$v] = $data[$key];
            }
            $this->model = model($name);
            if ($this->model->allowField($field)->validate($rule)->save($data, $where)) {
                return true;
            } else {
                return $this->errorMsg = $this->model->getError();
            }
        } catch (\Exception $e) {
            return $this->errorMsg = config('app_debug') ? $e->getMessage() : '请求错误';
        }
    }

    /**
     * 删除记录
     * @param string $name 模型
     * @param array $data 数据
     * @param string $key 主键
     * @return bool|mixed
     */
    public function delete($name, $data, $key = 'id')
    {
        try {
            $this->model = model($name);
            if ($this->model->where($key, 'in', $data[$key])->delete()) {
                return true;
            } else {
                return $this->errorMsg = '删除失败';
            }
        } catch (\Exception $e) {
            return $this->errorMsg = config('app_debug') ? $e->getMessage() : '请求错误';
        }
    }

    /**
     * 保存多个数据到当前数据对象
     * @param string $name 模型
     * @param array $data 数据
     * @param bool $rule 是否开启认证
     * @param string|array $field 允许写入的字段 如果为true只允许写入数据表字段
     * @return bool|mixed
     */
    protected function saveAll($name, $data, $rule = true, $field = true)
    {
        try {
            $this->model = model($name);
            if ($this->model->allowField($field)->validate($rule)->saveAll($data)) {
                return true;
            } else {
                return $this->errorMsg = $this->model->getError();
            }
        } catch (\Exception $e) {
            return $this->errorMsg = config('app_debug') ? $e->getMessage() : '请求错误';
        }
    }
}
