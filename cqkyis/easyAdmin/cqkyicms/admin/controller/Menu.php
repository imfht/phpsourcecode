<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/9 0:52
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\controller;


use app\admin\model\MenuModel;

class Menu extends Base
{

    protected $title="系统设置";

    public function index(){
        $name = "系统菜单";
        $this->assign('name',$name);
        $this->assign('title',$this->title);


        return $this->fetch();
    }

    public function menulist(){
        $menuModel = new MenuModel();
        $list = $menuModel->listMenuAll();
        return json($list);
    }


    public function add($id){

        $name = "添加系统菜单";
        $menuModel = new MenuModel();
        if(request()->isPost()){
            $data = input('post.');
            $result = $this->validate([
                'menu_name'  => $data['menu_name'],
                'menu_role' => $data['menu_role'],
                'type'=>$data['type']
            ], 'MenuValidate');
            if(true !== $result){
                $this->ky_error($result);
            }
            $sure = $menuModel->add($data);
            if($sure['code']=1){
                $this->ky_success($sure['msg'],$sure['data']);
            }else{
                $this->ky_error($sure['msg']);
            }
        }else{
            if($id==0){
                $list=array('menu_id'=>'0','menu_name'=>'根目录');
            }else{
                $list = $menuModel->findByMenuId($id);
            }

            $this->assign('pmenu',$list);
            $this->assign('name',$name);
            return $this->fetch();
        }

    }



    public function edit($id){

        $name = "修改系统菜单";
        $menuModel = new MenuModel();
        if(request()->isPost()) {
            $data = input('post.');
            $data['menu_id'] = $id;
            $reuslt = $menuModel->edit($data);
            if ($reuslt['code'] == 1) {
                $this->ky_success($reuslt['msg'], $reuslt['data']);
            } else {
                $this->ky_error($reuslt['msg']);
            }
        }else{
            $list = $menuModel->findByMenuAll($id);
            $lists = $menuModel->findByMenuId($list['parent_id']);
            $this->assign('pmenu',$lists);
            $this->assign('vo',$list);
            $this->assign('name',$name);
            return $this->fetch();
        }
    }



    public function del($id){
        $menuModel = new MenuModel();
        $sure = $menuModel->del($id);
        if ($sure['code'] == 1) {
            $this->ky_success($sure['msg'], $sure['data']);
        } else {
            $this->ky_error($sure['msg']);
        }
    }

    public function icon(){
      return $this->fetch();
    }


    /*
     * 菜单树
     */
    public function tree(){
        $menu = new MenuModel();
        $tree = $menu->bulidTree();
        return $tree;
    }

    /**
     * 修改角色的菜单树
     */
    public function edittree($id){
        $menu = new MenuModel();
        $tree = $menu->editbulidTree($id);
        return $tree;
    }

}