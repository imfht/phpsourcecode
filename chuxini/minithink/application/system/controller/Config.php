<?php
namespace app\system\controller;
/*
*
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/4/27
*/
use app\base\controller\System;
use app\system\model\Config as ConfigModel;
use think\Cache;
use think\Config as ConfigSettings;
class Config extends System
{

    public function index($group = 0)
    {

        $this->view->assign('config_type_list',ConfigSettings::get('config_type_list'));
        $this->view->assign('config_group_list',ConfigSettings::get('config_group_list'));
        $this->view->assign('group', $group);
        return $this->view->fetch();
    }

    /**
     * 新增，修改
     * @return array|string
     */
    public function save() {
        if($this->request->isAjax()){
            $post_data = $this->request->param();
            if(empty($post_data)){return getMsg("数据不能为空");}
            $menu = new ConfigModel();
            $state = $menu->allowField(true)->save($post_data,$post_data['id']);
            if(false == $state){
                return getMsg("操作失败");
            }
            return getMsg("操作成功");
        }
    }

    /**
     * ajax 获取配置列表
     * @param int $pid
     * @param int $p
     * @return mixed
     */
    public function getConfig($group = 0, $p = 1, $keyword = '') {
        $config = new ConfigModel();
        $p = ($p*10) - 10;
        $list = $config->getConfig($keyword, $group, $p);
        $msg['status'] = 200;
        $msg['data']['list'] = $list;
        $msg['pages'] = $config->getPage($group);
        return $msg;
    }

    public function setconfig($id = 1) {
        $config = new ConfigModel();
        $list = $config->getConfig('',$id,0,999);

        $this->assign('list',$list);
        $this->assign('id',$id);
        return $this->view->fetch();
    }

    /**
     * 修改配置内容
     * @return array|string
     */
    public function updateconfig() {
        if($this->request->isAjax()){
            $config = $this->request->param();
            if($config && is_array($config)){
                $configModel = new ConfigModel();
                foreach ($config as $name => $value){
                    $map['name'] = $name;
                    $configModel->where($map)->setField('value', $value);
                }
            }
            Cache::rm('cache_config');
            return getMsg("操作成功");
        }
    }

}
