<?php
namespace app\admin\controller;
/**
 * 后台用户
 */
class AdminUser extends Admin {
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
        $keyword = input('keyword');
        if(!empty($keyword)){
            $where[] = ' (A.username like "%'.$keyword.'%")  OR ( A.nicename like "%'.$keyword.'%") ';
        }
        $groupId = input('group_id');
        if(!empty($groupId)){
            $where['A.group_id'] = $groupId;
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['group_id'] = $groupId;
        //查询数据
        $limit=0;
        $list = model('AdminUser')->loadList($where,$limit);
        //位置导航
        $breadCrumb = array('用户列表'=>url());
        //模板传值
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('_page',$list->render());
        $this->assign('groupList',model('AdminGroup')->loadList());
        $this->assign('keyword',$keyword);
        $this->assign('groupId',$groupId);
        return $this->fetch();
    }

    /**
     * 增加
     */
    public function add(){
        if (input('post.')){
            if(model('AdminUser')->add('add')){
                $this->success('用户添加成功！');
            }else{
                $this->error('用户添加失败');
            }
        }else{
            $breadCrumb = array('用户列表'=>url('index'),'添加'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->assign('groupList',model('AdminGroup')->loadList());
            return $this->fetch();
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if (input('post.')){
            if(model('AdminUser')->edit()){
                return $this->success('用户修改成功！');
            }else{
                return $this->error('用户修改失败');
            }
        }else{
            $userId = input('user_id');
            if($userId == 1){
                return $this->error('保留用户无法编辑！');
            }
            if(empty($userId)){
                return $this->error('参数不能为空！');
            }

            //获取记录
            $model = model('AdminUser');
            $info = $model->getInfo($userId);
            if(!$info){
                $this->error('错误');
            }
            $breadCrumb = array('用户列表'=>url('index'),'修改'=>url('',array('user_id'=>$userId)));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('groupList',model('AdminGroup')->loadList());
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $userId = input('post.id');
        if(empty($userId)){
            $this->error('参数不能为空！');
        }
        if($userId == 1){
            $this->error('保留用户无法删除！');
        }
        //获取用户数量
        if(model('AdminUser')->del($userId)){
            $this->success('用户删除成功！');
        }else{
            $this->error('用户删除失败！');
        }
    }


}

