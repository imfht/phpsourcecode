<?php
namespace app\admin\controller;
/**
 * 后台会员类型
 */
class UserType extends Admin {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '会员类型管理',
                'description' => '管理网站后台会员类型',
            ),
            'menu' => array(
                array(
                    'name' => '会员类型列表',
                    'url' => url('index'),
                    'icon' => 'list',
                ),
            ),
            '_info' => array(
                array(
                    'name' => '添加会员类型',
                    'url' => url('info'),
                ),
            ),
        );
    }
    
	/**
     * 列表
     */
    public function index(){
        $breadCrumb = array('会员类型列表'=>url());
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',model('UserType')->loadList());
        return $this->fetch();
    }
    /**
     * 详情
     */
    public function info(){
        $typeId = input('type_id');
        $model = model('UserType');
        if (input('post.')){
            if ($typeId){
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
            $this->assign('info', $model->getInfo($typeId));
            $this->assign('groupList',model('UserType')->loadList());
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $typeId = input('id');
        if(empty($typeId)){
            return ajaxReturn(0,'参数不能为空！');
        }
        //获取用户数量
        $map = array();
        $map['A.type_id'] = $typeId;
        $countUser = model('User')->countList($map);
        if($countUser>0){
            return ajaxReturn(0,'请先删除该类型下的用户！谢谢');
        }
        if(model('UserType')->del($typeId)){
            return ajaxReturn(200,'会员类型删除成功！');
        }else{
            return ajaxReturn(0,'会员类型删除失败！');
        }
    }
}

