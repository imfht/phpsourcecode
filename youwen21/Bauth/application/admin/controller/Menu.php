<?php
namespace app\admin\controller;
use app\admin\model\Menu as menuModel;
use app\lib\TreeModel;

class Menu extends Base
{
    /**
     * 菜单管理
     * @author baiyouwen 
     * @DateTime 2016-07-06T14:20:11+0800
     */
    public function index()
    {
        $current_module = $this->request->param('current_module');
        $pid  = $this->request->param('pid',0);
        if($pid){
            $data = db('Menu')->where("id={$pid}")->field(true)->find();
            $current_module = $data['module'];
            $this->assign('data',$data);
        }
        if(empty($current_module))
            $current_module = 'admin';
        $list = db('Menu')->where(['pid'=>$pid, 'module'=>$current_module])->select();
        int_to_string($list, array('hide'=>array(1=>'是',0=>'否'),'is_dev'=>array(1=>'是',0=>'否')));
        // $modules = db('menu_module')->select();
        $modules = config('menu_module');
        
        $this->assign('modules', $modules);
        $this->assign('current_module', $current_module);
        $this->assign('pid', $pid);
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 添加菜单
     * @author baiyouwen 
     * @DateTime 2016-07-06T14:19:18+0800
     */
    public function add()
    {
        $menu_module = $this->inputOrError('menu_module');
        $list = db('menu')->where(['module'=>$menu_module])->select();
        $tree = new TreeModel();
        $mtree = $tree->toFormatTree($list);
        $menuTree = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $mtree);
        // $modules = db('menu_module')->select();
        $modules = config('menu_module');
        $this->assign('modules', $modules);
        $this->assign('menuTree', $menuTree);
        return $this->fetch();
    }

    public function edit()
    {
        $id = $this->inputOrError('id');
        $info = menuModel::get($id)->toArray();
        $list = db('menu')->where(['module'=>$info['module']])->select();
        $tree = new TreeModel();
        $mtree = $tree->toFormatTree($list);
        $menuTree = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $mtree);
        // $modules = db('menu_module')->select();
        $modules = config('menu_module');
        $this->assign('modules', $modules);
        $this->assign('menuTree', $menuTree);
        $this->assign('info', $info);
        // $this->assign('pid', $id);
        return $this->fetch();
    }

    public function del()
    {
        $id = $this->inputOrError('id');
        $ret = db('menu')->delete($id);
        if($ret){
            return $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    /**
     * 更新菜单｜保存菜单
     * @return   [type]                   [description]
     * @author baiyouwen 
     * @DateTime 2016-07-06T14:19:33+0800
     */
    public function updateMenu()
    {
        $id = $this->request->param('id');
        if(empty($id)){
            $model = new menuModel();
            $ret = $model->save($this->request->param(''));
        }else{
            $model = menuModel::get($id);
            $ret = $model->save($this->request->param(''),['id'=>$id]);
        }
        if(false !== $ret){
            return $this->success('操作成功', url('Menu/index', array('pid'=>$this->request->param('pid'), 'menu_module'=>$this->request->param('module'))));
        }else{
            $this->error('操作失败');
        }
    }

    // 菜单隐藏设置
    public function toogleHide($id, $value)
    {
        return $this->editRows('Menu', ['id'=>array('in', $id)], ['hide'=>$value], ['url'=> url('index')]);
    }

    // 开发者菜单设置
    public function toogledev($id, $value)
    {
        return $this->editRows('Menu', ['id'=>array('in', $id)], ['is_dev'=>$value], ['url'=> url('index')]);
    }

    /**
     * 以树式显示某分组所有菜单 包括隐藏菜单和开发者菜单
     * @author EchoEasy
     * @DateTime 2017-01-15T13:03:21+0800
     */
    public function tree_show()
    {
        // $startId = $this->request->param('startId', 0);
        $module = $this->request->param('module', 'admin');
        $list = db('menu')->where(['module'=>$module])->select();
        $tree = new TreeModel();
        $menuTree = $tree->toTree($list);
        // if($startId){
        //     $menuTree = array($this->_substr_tree($menuTree, $startId));
        // }
        // $menuTree = array_merge(array(0=>array('id'=>0,'title'=>'顶级菜单')), $mtree);
        $this->assign("_menuTree", $menuTree);
        return $this->fetch();
    }

    /**
     * 生成某一ID为PID的菜单树
     * @author EchoEasy
     * @DateTime 2017-01-15T13:06:34+0800
     */
    private function _substr_tree($tree=[], $startId=0, $maxLevel=100)
    {
        if($maxLevel == 0){
            return ['00'];
        }
        if($startId == 0){
            return $tree;
        }
        foreach ($tree as $key => $value) {
            if($value['id'] == $startId){
                return $value;
            }
        }
        $lev2tree = array_column($tree, '_child');
        return $this->_substr_tree($lev2tree, $startId, $maxLevel--);
    }
}
