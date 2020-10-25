<?php
namespace app\admin\controller;
/**
 * 后台用户组
 */
class AdminUserGroup extends Admin {
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
            '_info' => array(
                array(
                    'name' => '添加用户组',
                    'url' => url('info'),
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
        $this->assign('list',model('AdminGroup')->loadList());
        return $this->fetch();
    }
    /**
     * 详情
     */
    public function info(){
        $groupId = input('group_id');
        $model = model('AdminGroup');
        if (input('post.')){
            if($groupId == 1){
                return $this->error('保留用户组无法编辑');
            }
            if ($groupId){
                $status=$model->edit();
            }else{
                $status=$model->add();
            }
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $this->assign('info', $model->getInfo($groupId));
            $this->assign('groupList',model('AdminGroup')->loadList());
            return $this->fetch();
        }
    }
    /**
     * 权限
     */
    public function purview(){
        if(input('post.')){
            $groupId = input('post.group_id');
            if(empty($groupId)){
                $this->error('参数不能为空！');
            }
            if(model('AdminGroup')->savePurviewData()){
                return ajaxReturn('200','权限更新成功！');
            }else{
                return ajaxReturn('0','权限更新失败！');
            }
        }else{
            $groupId = input('group_id');
            if(empty($groupId)){
                $this->error('参数不能为空！');
            }
            //获取记录
            $info = model('AdminGroup')->getInfo($groupId);
            if(!$info){
                $this->error('该组信息不存在');
            }
            $AdminMenu = model('AdminMenu')->getPurMenu();//后台菜单
            $AdminPurvew = $AdminMenu;//权限菜单
            $this->assign('AdminPurvew',$AdminPurvew);
            $this->assign('AdminMenu', $AdminMenu);
            $this->assign('menu_purview', $info['menu_purview']);
            $this->assign('base_purview', $info['base_purview']);
            $this->assign('info',$info);
            return $this->fetch();
        }
    }


    /**
     * 删除
     */
    public function del(){
        $groupId = input('id');
        if(empty($groupId)){
            return ajaxReturn(0,'参数不能为空！');
        }
        //获取用户数量
        $map = array();
        $map['A.group_id'] = $groupId;
        $countUser = model('AdminUser')->countList($map);
        if($countUser>0){
            return ajaxReturn(0,'请先删除该组下的用户！谢谢');
        }
        if(model('AdminGroup')->del($groupId)){
            return ajaxReturn(200,'用户组删除成功！');
        }else{
            return ajaxReturn(0,'用户组删除失败！');
        }
    }


}

