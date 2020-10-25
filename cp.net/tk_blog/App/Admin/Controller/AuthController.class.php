<?php
namespace Admin\Controller;
class AuthController extends AdminBaseController {

    /**
     * 权限管理
     */
    public function rule(){
        $t = I('get.t',0,'int');
        $id = I('get.id',0,'int');
        $pid = I('get.pid',0,'int');
        if ($t == 1) {
            $this->assign('id',$id);
            $this->assign('pid',$pid);
            $this->assign('pidName',D('AuthRule')->where(array('id'=>$id))->getField('title'));
            $this->display('addRule');
        } else {
            $this->assign('data',D('AuthRule')->getTreeData());
            $this->display('listRule');
        }
    }

    public function editRule(){
        $id = I('get.id',0,'int');
        $data = D('AuthRule')->where(array('id'=>$id))->find();
        if ($data['pid']) {
            $data['pidName'] = D('AuthRule')->where(array('id'=>$data['pid']))->getField('title');
        }
        $this->assign('data',$data);
        $this->display();
    }

    public function ajaxdelRule(){
        if (IS_AJAX) {
            $model = new \Common\Model\AuthRuleModel();
            if ($model->sendDeleteRule()) {
                exit(json_encode(array('status'=>1,'msg'=>'操作成功.^_^',)));
            } else {
                exit(json_encode(array('status'=>0,'msg'=>$model->getError())));
            }
        }
    }

    public function ajaxSendRule(){
        if (IS_AJAX) {
            $model = new \Common\Model\AuthRuleModel();
            if ($model->sendAddRule()) {
                exit(json_encode(array('status'=>1,'msg'=>'操作成功.^_^','url'=>'/Admin/Auth/rule')));
            } else {
                exit(json_encode(array('status'=>0,'msg'=>$model->getError())));
            }
        }
    }


    /**
     * 用户组管理
     */
    public function group(){
        $t = I('get.t',0,'int');
        $id = I('get.id',0,'int');
        $model = new \Common\Model\AuthGroupModel();
        if ($t == 1) {
            if ($id) {
                $this->assign('result',$model->getFindData($id));
            }
            $this->display('addGroup');
        } else {
            $this->assign('data',$model->getListData(5));
            $this->display('listGroup');
        }
    }
    //添加用户组
    public function ajaxAddGroup(){
        if (IS_AJAX) {
            $title = trim(I('post.title'));
            $status = I('post.status') ? '1' : '0';
            if (!$title) exit(json_encode(array('status'=>0,'msg'=>'用户组名称必须填写')));
            $data = array(
                'title' => $title,
                'status' => $status
            );
            if (M('Auth_group')->add($data)) {
                exit(json_encode(array('status'=>1,'msg'=>'操作成功.^_^','url'=>'/Admin/Auth/group')));
            } else {
                exit(json_encode(array('status'=>0,'msg'=>'操作失败.^_^')));
            }
        }
    }
    //删除用户组
    public function ajaxDelGroup(){
        if (IS_AJAX) {
            $id = trim(I('post.id'));
            $model = new \Common\Model\AuthGroupModel();
            if ($model->execDelData($id)) {
                exit(json_encode(array('status'=>1,'msg'=>'')));
            } else {
                exit(json_encode(array('status'=>0,'msg'=>'删除失败,请重试...')));
            }
        }
    }

    //用户组 分配权限
    public function allocation(){
        $id = I('get.id',0,'int');
        $data['list'] = node_merge(D('AuthRule')->select());
        $data['id'] = $id;
        $oldData = D('AuthGroup')->where(array('id'=>$id))->getField('rules');
        $oldData = explode(",",$oldData);
        $oldData = empty($oldData) ? array() : $oldData;
        $this->oldData = $oldData;
        $this->data = $data;
        $this->display();
    }

    //分配权限 ajax执行添加
    public function ajaxSendAllocation(){
        if (IS_AJAX) {
            $rid = I('post.rid');
            $gid = I('post.gid');
            $rules = implode(',',$rid);
            if (D('AuthGroup')->where(array('id'=>$gid))->setField(array('rules'=>$rules))) {
                exit(json_encode(array('status'=>1,'msg'=>'操作成功.^_^')));
            } else {
                exit(json_encode(array('status'=>0,'msg'=>'操作失败,请重试...')));
            }
        }
    }



    /**
     * 管理员列表
     */
    public function admin_user(){
        $t = I('get.t',0,'int');
        $uid = I('get.uid',0,'int');
        $model = new \Common\Model\AuthGroupAccessModel();
        if ($t) {
            $this->assign('group',M('AuthGroup')->select());
            if ($uid) {
                $this->assign('oldGroup',$model->where(array('uid'=>$uid))->find());
                $this->assign('data',M('Users')->where(array('uid'=>$uid))->find());
                $this->display('editUser');
                exit;
            }
            $this->display('addUser');
        } else {
            $this->assign('data',$model->getListData());
            $this->display('listUser');
        }
    }

    public function ajaxAddUser(){
        if (IS_AJAX) {
            $model = new \Common\Model\AuthGroupAccessModel();
            if ($model->sendAdd()) {
                exit(json_encode(array('status'=>1,'msg'=>'操作成功.^_^','url'=>'/Admin/Auth/admin_user')));
            } else {
                exit(json_encode(array('status'=>0,'msg'=>$model->getError())));
            }
        }
    }
}