<?php
namespace app\admin\controller;
use app\admin\controller\AdminController;
/**
 * 后台用户组
 */

class AdminUserGroupController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '用户组管理',
                'description' => '管理网站后台用户组',
                ),
            'menu' => array(
                    array(
                        'name' => '用户组列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                ),
            'add' => array(
                    array(
                        'name' => '添加用户组',
                        'url' => url('add'),
                    ),
                ),
            );
    }
    
	/**
     * 列表
     */
    public function index(){
        $breadCrumb = array('用户组列表'=>url());
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',target('AdminGroup')->loadList());
        $this->adminDisplay();
    }

    /**
     * 增加
     */
    public function add(){
        if(!IS_POST){
            $breadCrumb = array('用户组列表'=>url('index'),'添加'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->adminDisplay('info');
        }else{
            if(target('AdminGroup')->saveData('add')){
                $this->success('用户组添加成功！',url('index'));
            }else{
                $this->error('用户组添加失败');
            }
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(!IS_POST){
            $groupId = request('get.group_id','','intval');
            if(empty($groupId)){
                $this->error('参数不能为空！');
            }
            //获取记录
            $model = target('AdminGroup');
            $info = $model->getInfo($groupId);
            if(!$info){
                $this->error($model->getError());
            }
            $breadCrumb = array('用户组列表'=>url('index'),'修改'=>url('',array('group_id'=>$groupId)));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('info',$info);
            $this->adminDisplay('info');
        }else{
            if(target('AdminGroup')->saveData('edit')){
                $this->success('用户组修改成功！',url('index'));
            }else{
                $this->error('用户组修改失败');
            }
        }
    }

    /**
     * 权限
     */
    public function purview(){
        if(!IS_POST){
            $groupId = request('get.group_id','','intval');
            if(empty($groupId)){
                $this->error('参数不能为空！');
            }
            //获取记录
            $model = target('AdminGroup');
            $info = $model->getInfo($groupId);
            if(!$info){
                $this->error($model->getError());
            }
            $AdminPurvewArray = unserialize($info['base_purview']);
            $AdminMenuArray = unserialize($info['menu_purview']);
            $breadCrumb = array('用户组列表'=>url('index'),'权限设置('.$info['name'].')'=>url('',array('group_id'=>$groupId)));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('AdminPurvew', target('Menu')->getPurview());
            $this->assign('AdminMenu', target('Menu')->getMenu());
            $this->assign('AdminPurvewArray', $AdminPurvewArray);
            $this->assign('AdminMenuArray', $AdminMenuArray);
            $this->assign('info',$info);
            $this->adminDisplay('purview');
        }else{
            if(target('AdminGroup')->savePurviewData()){
                $this->success('用户组修改成功！');
            }else{
                $this->error('用户组修改失败');
            }
        }
    }

    /**
     * 删除
     */
    public function del(){
        $groupId = request('post.data');
        if(empty($groupId)){
            $this->error('参数不能为空！');
        }
        //获取用户数量
        $map = array();
        $map['A.group_id'] = $groupId;
        $countUser = target('AdminUser')->countList($map);
        if($countUser>0){
            $this->error('请先删除改组下的用户！');
        }
        if(target('AdminGroup')->delData($groupId)){
            $this->success('用户组删除成功！');
        }else{
            $this->error('用户组删除失败！');
        }
    }


}

