<?php
namespace Admin\Controller;

use Admin\Controller\AdminController;
class DistrictController extends AdminController
{
    public function index(){
        $pid  = I('get.pid',0);
        if($pid){
            $data = M('District')->where("id={$pid}")->field(true)->find();
            $this->assign('data',$data);
        }
        $name      =   trim(I('get.name'));
        $type       =   C('CONFIG_GROUP_LIST');
        $all_menu   =   M('District')->getField('id,name');
        $map['pid'] =   $pid;
        if($name)
            $map['name'] = array('like',"%{$name}%");
        $list       =   M("District")->where($map)->field(true)->order('id asc')->select();
        int_to_string($list,array('hide'=>array(1=>'是',0=>'否'),'is_dev'=>array(1=>'是',0=>'否')));
        if($list) {          
            $this->assign('list',$list);
        }
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
        $this->meta_title = '地区列表';
        $this->display();
    }
/**
     * 新增菜单
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function add(){
        if(IS_POST){
            $District = D('District');
            $data = $District->create();
            if($data){
                $id = $District->add();
                if($id){
                    session('ADMIN_MENU_LIST',null);
                    //记录行为
                    action_log('update_menu', 'District', $id, UID);
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($District->getError());
            }
        } else {
            $res = M('District')->field(true)->find(I('pid'));
            $level = $res['level']+1;
            $this->assign('info',array('pid'=>I('pid'),'level'=>$level));
            $district = M('District')->field(true)->select();
          
            $district = D('Common/Tree')->toFormatTree($menus);
            $district = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $district);
            $this->assign('District', $district);
            $this->meta_title = '新增地区';
            $this->display('edit');
        }
    }

    /**
     * 编辑配置
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            $District = D('District');
            $data = $District->create();
            if($data){
                if($District->save()!== false){
                    session('ADMIN_MENU_LIST',null);
                    //记录行为
                    action_log('update_menu', 'District', $data['id'], UID);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($District->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('District')->field(true)->find($id);
          
            if(false === $info){
                $this->error('获取地区信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑地区';
            $this->display();
        }
    }

    /**
     * 删除后台菜单
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('District')->where($map)->delete()){
            session('ADMIN_MENU_LIST',null);
            //记录行为
            action_log('update_menu', 'District', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}

?>