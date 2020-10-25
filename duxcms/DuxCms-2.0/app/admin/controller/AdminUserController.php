<?php
namespace app\admin\controller;
use app\admin\controller\AdminController;
/**
 * 后台用户
 */
class AdminUserController extends AdminController {

    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '用户管理',
                'description' => '管理网站后台管理员',
                ),
            'menu' => array(
                    array(
                        'name' => '用户列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                ),
            'add' => array(
                    array(
                        'name' => '添加用户',
                        'url' => url('add'),
                    ),
                ),
            );
    }
	/**
     * 列表
     */
    public function index(){
        //筛选条件
        $where = array();
        $keyword = request('request.keyword','');
        if(!empty($keyword)){
            $where[] = ' (A.username like "%'.$keyword.'%")  OR ( A.nicename like "%'.$keyword.'%") ';
        }
        $groupId = request('request.group_id','');
        if(!empty($groupId)){
            $where['A.group_id'] = $groupId;
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['group_id'] = $groupId;
        //查询数据
        $list = target('AdminUser')->page(20)->loadList($where,$limit);
        $this->pager = target('AdminUser')->pager;
        //位置导航
        $breadCrumb = array('用户列表'=>url());
        //模板传值
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('groupList',target('AdminGroup')->loadList());
        $this->assign('page',$this->getPageShow($pageMaps));
        $this->assign('keyword',$keyword);
        $this->assign('groupId',$groupId);
        $this->adminDisplay();
    }

    /**
     * 增加
     */
    public function add(){
        if(!IS_POST){
            $breadCrumb = array('用户列表'=>url('index'),'添加'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->assign('groupList',target('AdminGroup')->loadList());
            $this->adminDisplay('info');
        }else{
            if(target('AdminUser')->saveData('add')){
                $this->success('用户添加成功！',url('index'));
            }else{
                $msg = target('AdminUser')->getError();
                if(empty($msg)){
                    $this->error('用户添加失败');
                }else{
                    $this->error($msg);
                }
                
            }
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(!IS_POST){
            $userId = request('get.user_id','','intval');
            if(empty($userId)){
                $this->error('参数不能为空！');
            }
            //获取记录
            $model = target('AdminUser');
            $info = $model->getInfo($userId);
            if(!$info){
                $this->error($model->getError());
            }
            $breadCrumb = array('用户列表'=>url('index'),'修改'=>url('',array('user_id'=>$userId)));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('groupList',target('AdminGroup')->loadList());
            $this->assign('info',$info);
            $this->adminDisplay('info');
        }else{
            if(target('AdminUser')->saveData('edit')){
                $this->success('用户修改成功！',url('index'));
            }else{
                $msg = target('AdminUser')->getError();
                if(empty($msg)){
                    $this->error('用户修改失败');
                }else{
                    $this->error($msg);
                }
            }
        }
    }

    /**
     * 删除
     */
    public function del(){
        $userId = request('post.data');
        if(empty($userId)){
            $this->error('参数不能为空！');
        }
        if($userId == 1){
            $this->error('保留用户无法删除！');
        }
        //获取用户数量
        if(target('AdminUser')->delData($userId)){
            $this->success('用户删除成功！');
        }else{
            $msg = target('AdminUser')->getError();
            if(empty($msg)){
                $this->error('用户删除失败！');
            }else{
                $this->error($msg);
            }
        }
    }


}

