<?php

namespace app\admin\controller;

use think\Loader;
use think\Config;
use think\Url;
use think\Cache;
use think\Db;

/**
 * Description of Config
 * 系统配置
 * @author static7
 */
class Deploy extends Admin {

    /**
     * 网站系统配置
     * @param int $group 配置分组
     * @author staitc7 <static7@qq.com>
     */
    public function index($group = 0) {
        $map = [
            'group' => (int) $group > 0 ? $group : ['egt', 0]
        ];
        $field = 'id,name,group,type,sort,title';
        $data = Loader::model('Deploy')->configList($map, $field, 'sort ASC');
        $value = [
            'list' => $data['data'] ?? null,
            'group_id' => (int) $group,
            'page' => $data['page'],
            'groupList' => Config::get('config_group_list') ?? null
        ];
        $this->view->metaTitle = '菜单列表';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 菜单排序更新
     * @param int $id 菜单ID
     * @param int $sort 排序
     * @author staitc7 <static7@qq.com>
     */
    public function configSort($id = 0, $sort = null) {
        (int) $id || $this->error('参数错误');
        !is_numeric((int) $sort) && $this->error('排序非数字');
        $info = Loader::model('Deploy')->setStatus(['id' => $id], ['sort' => (int) $sort]);
        return $info !== FALSE ? $this->success('排序更新成功') : $this->error('排序更新失败');
    }

    /**
     * 配置详情
     * @param int $id 菜单ID
     * @author staitc7 <static7@qq.com>
     */
    public function edit($id = 0) {
        $Config = Loader::model('Deploy');
        if ((int) $id > 0) {
            $info = $Config->edit((int) $id);
            $value['info'] = $info;
        }
        $this->view->metaTitle = '配置详情';
        return $this->view->assign($value ?? null)->fetch();
    }

    /**
     * 用户更新或者添加菜单
     * @author staitc7 <static7@qq.com>
     */
    public function renew() {
        $info = Loader::model('Deploy')->renew(); 
        if (is_array($info)) {
            Cache::rm('db_config_data');
            return $this->success('操作成功', Url::build('index'));
        } else {
            return $this->error($info);
        }
    }

    /**
     * 网站设置
     * @param int $id 分组配置ID
     * @author staitc7 <static7@qq.com>
     */
    public function group($id = 1) {
        (int) $id || $this->error('参数错误');
        $map = ['group' => (int) $id, 'status' => 1];
        $field = 'id,name,title,extra,value,remark,type';
        $data = Db::name('Deploy')->where($map)->field($field)->order('sort ASC')->select();
        $type = Config::get('config_group_list') ?? null;
        $value = [
            'list' => $data,
            'group_id' => $id,
            'type' => $type
        ];
        $this->view->metaTitle = "{$type[$id]}设置";
        return $this->view->assign($value ?? null)->fetch();
    }

    /**
     * 网站设置保存
     * @author staitc7 <static7@qq.com>
     */
    public function setUp($config = null) {
        if (!$config && !is_array($config)) {
            return $this->error('数据有误，请检查后在保存');
        }
        $info = Loader::model('Deploy')->batchSave($config);
        if ($info !== FALSE) {
            Cache::rm('db_config_data');
            return $this->success('更新成功');
        } else {
            return $this->error('更新失败');
        }
    }

}
