<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/9/29
 * Time: 13:27
 * ----------------------------------------------------------------------
 */

namespace app\admin\model;

use think\model;
use think\facade\Request;

/**
 * Class Config
 * 配置模型
 * @package app\admin\model
 */
class Config extends model
{
    protected $table = COMMON . 'config';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * 查询模块控制器下配置
     * @param $model
     * @param $controller
     * @return array
     * @author:wdx(wdx@ourstu.com)
     */
    public function queryConfig($model = '', $controller = '')
    {
        $model = $model ? : Request::module();
        $controller = $controller ? : Request::controller();
        $configs = $this
            ->where('module',$model)
            ->where('controller', $controller)
            ->limit(999)
            ->column('value', 'name');
        return $configs;
    }

    /**
     * 保存模块控制器下配置
     * @param array $data
     * @param string $model
     * @param  string $controller
     * @return int
     * @author:wdx(wdx@ourstu.com)
     */
    public function saveConfig($data = [], $model = '', $controller = '')
    {
        $model = $model ? : Request::module();
        $controller = $controller ? : Request::controller();
        if (Request::isPost()) {
            $status = 0;
            foreach ($data as $k => $v) {
                $config['name'] = strtolower($k);
                $config['module'] = strtolower($model);
                $config['controller'] = strtolower($controller);
                $config['type'] = 0;
                $config['title'] = '';
                $config['group'] = 0;
                $config['extra'] = '';
                $config['remark'] = '';
                $config['status'] = 1;
                $config['value'] = is_array($v) ? implode(',', $v) : $v;
                $config['sort'] = 0;
                if ($this->insert($config, true)) {
                    $status = 1;
                }
                $tag = 'conf_' . strtolower(Request::module()) . '_' . strtolower(Request::controller()) . '_' . strtolower($k);
                cache($tag, null);
            }
            return $status;
        }
    }
}