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
        $this->assign('list',model('AdminGroup')->loadList());
        return $this->fetch();
    }

    /**
     * 增加
     */
    public function add(){
        if(input('post.')){
            if(model('AdminGroup')->add('add')){
                $this->success('用户组添加成功！',url('index'));
            }else{
                $this->error('用户组添加失败');
            }
        }else{
            $breadCrumb = array('用户组列表'=>url('index'),'添加'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            return $this->fetch();
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if (input('post.')){
            if(model('AdminGroup')->edit()){
                return $this->success('用户组修改成功！');
            }else{
                $this->error('用户组修改失败');
            }
        }else{
            $groupId = input('group_id');
            if ($groupId==1){
                return $this->error('该用户组不能删除');
            }
            if(empty($groupId)){
                $this->error('参数不能为空！');
            }
            //获取记录
            $model = model('AdminGroup');
            $info = $model->getInfo($groupId);
            if(!$info){
                $this->error('错误');
            }
            $breadCrumb = array('用户组列表'=>url('index'),'修改'=>url('',array('group_id'=>$groupId)));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('info',$info);
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
                $this->success('用户组修改成功！');
            }else{
                $this->error('用户组修改失败');
            }
        }else{
            $groupId = input('group_id');
            //var_dump($groupId);
            if(empty($groupId)){
                $this->error('参数不能为空！');
            }
            //获取记录
            $info = model('AdminGroup')->getInfo($groupId);
            if(!$info){
                $this->error('该组信息不存在');
            }
            $AdminPurvewArray = unserialize($info['base_purview'])?unserialize($info['base_purview']):array();
            $AdminMenuArray = unserialize($info['menu_purview'])?unserialize($info['menu_purview']):array();
            $breadCrumb = array('用户组列表'=>url('index'),'权限设置('.$info['name'].')'=>url('',array('group_id'=>$groupId)));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('AdminPurvew', model('Menu')->getPurview());
            $this->assign('AdminMenu', model('Menu')->getMenu());
            $this->assign('AdminPurvewArray',$AdminPurvewArray);
            $this->assign('AdminMenuArray', $AdminMenuArray);
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $groupId = input('post.id');
        if(empty($groupId)){
            $this->error('参数不能为空！');
        }
        //获取用户数量
        $map = array();
        $map['A.group_id'] = $groupId;
        $countUser = model('AdminUser')->countList($map);
        if($countUser>0){
            return $this->error('请先删除改组下的用户！');
        }
        if(model('AdminGroup')->del($groupId)){
            return $this->success('用户组删除成功！');
        }else{
            return $this->error('用户组删除失败！');
        }
    }


}

