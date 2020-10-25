<?php
namespace app\admin\controller;
/**
 * 后台会员
 */
class User extends Admin {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '会员管理',
                'description' => '管理网站后台管理员',
                ),
            'menu' => array(
                    array(
                        'name' => '会员列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                ),
            '_info' => array(
                    array(
                        'name' => '添加会员',
                        'url' => url('info'),
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
        $groupId = input('type_id');
        if(!empty($groupId)){
            $where['A.type_id'] = $groupId;
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['type_id'] = $groupId;
        //查询数据
        $limit=0;
        $list = model('User')->loadList($where,$limit);
        //位置导航
        $breadCrumb = array('会员列表'=>url());
        //模板传值
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('_page',$list->render());
        $this->assign('typeList',model('UserType')->loadList());
        $this->assign('keyword',$keyword);
        $this->assign('groupId',$groupId);
        return $this->fetch();
    }
    /**
     * 详情
     */
    public function info(){
        $userId = input('user_id');
        $model = model('User');
        if (input('post.')){
            if($userId == 1){
                return ajaxReturn(0,'保留会员无法编辑');
            }
            if (input('post.password')){
                if (empty(input('post.password2'))){
                    return ajaxReturn(0,'确认密码不能为空');
                }
                if (input('post.password2')!=input('post.password')){
                    return ajaxReturn(0,'两次密码不一致');
                }
            }
            if ($userId){
                $status=$model->edit();
            }else{
                if (empty(input('post.password'))){
                    return ajaxReturn(0,'请填写密码');
                }
                $status=$model->add();
            }
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $this->assign('info', $model->getInfo($userId));
            $this->assign('typeList',model('UserType')->loadList());
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $userId = input('id');
        if(empty($userId)){
            return ajaxReturn(0,'参数不能为空');
        }
        if($userId == 1){
            return ajaxReturn(0,'保留会员无法删除');
        }
        if(model('User')->del($userId)){
            return ajaxReturn(200,'会员删除成功！');
        }else{
            return ajaxReturn(0,'会员删除失败');
        }
    }
}

