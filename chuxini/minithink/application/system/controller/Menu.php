<?php
namespace app\system\controller;
/*
*
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/3/24
*/
use app\base\controller\System;
use app\system\model\Menu as MenuModel;
class Menu extends System
{

    public function index($pid = 0)
    {
        $module_info = ['module'=>'', 'name'=>''];
        $pid && $module_info = MenuModel::get(['id'=>$pid]);

        $this->view->assign('pid',$pid);
        $this->view->assign('module_info',$module_info);
        return $this->view->fetch();
    }


    /**
     * ajax 获取菜单列表
     * @param int $pid
     * @param int $p
     * @return mixed
     */
    public function getmenus($pid = 0, $p = 1) {
        $menu = new MenuModel();
        $p = ($p*10) - 10;
        $list = $menu->getMenu($pid,$p);
        $msg['status'] =200;
        $msg['data']['list'] = $list;
        $msg['pages'] = $menu->getPage($pid);
        return $msg;
    }

    /**
     * 新增，修改
     * @return array|string
     */
    public function save() {
        if($this->request->isAjax()){
            $post_data = $this->request->param();
            if(empty($post_data)){return getMsg("数据不能为空");}
            if(empty($post_data['link'])){$post_data['link']="无";}
            $menu = new MenuModel();
            $state = $menu->allowField(true)->save($post_data,$post_data['id']);
            if(false == $state){
                return getMsg("操作失败");
            }
            return getMsg("操作成功","reload");
        }
    }

    /**
     * 重写父类删除操作
     * @param $model
     * @param $id
     * @return array|string
     */
    public function forceDelete($model = '', $id) {
        if(empty($id)){return getMsg("删除失败");}
        $menu = new MenuModel();
        $result = $menu->getMenu($id);
        if(!empty($result)){
            return getMsg("删除失败,下级存在菜单");
        }
        $state = $menu->where(['id'=>$id])->delete();
        if(false == $state){
            return getMsg("删除失败");
        }
        return getMsg("删除成功","reload");
    }

    public function allDelete($model = '', $checkbox) {
        $ids = explode(',',$checkbox);
        if (empty($ids)){return getMsg("删除失败");}
        $menu = new MenuModel();
        for ($i = 0; $i< count($ids); $i++){
            $result = $menu->getMenu($ids[$i]);
            if(!empty($result)){
                return getMsg("删除中止, id：".$ids[$i]."下级存在菜单");
            }
            $menu->where(['id'=>$ids[$i]])->delete();
        }
        return getMsg("删除成功","reload");

    }

    /**
     * 修改开发者模式
     * @param int $id
     * @return array|string
     */
    public function changeDev($id = 0) {
        $menu = MenuModel::get($id);
        $menu->is_dev = !$menu->is_dev;
        $menu->save();
        return getMsg("修改成功");
    }


    public function demo_unicode() {
        return $this->view->fetch();
    }

}
