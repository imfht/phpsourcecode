<?php
/**
 * 用户管理
 */
namespace Control\Controller;
use Core\Model\Acl;
use Think\Controller;

class AclController extends Controller {
    /**
     * @var Acl
     */
    private $acl;

    public function _initialize() {
        C('FRAME_ACTIVE', 'access');
        C('FRAME_CURRENT', U('control/acl/roles'));

        $this->acl = new Acl();
    }

    public function accessAction() {
        $this->error('暂未实现: 这里要实现当前用户组拥有的操作权限');
    }

    public function rolesAction() {
        $roles = $this->acl->getRoles(true);
        $roles = coll_key($roles, 'id');

        $id = I('get.id');
        if(!empty($id)) {
            $id = intval($id);
            if($id > 0) {
                $role = $roles[$id];
                $this->assign('entity', $role);
                if(!empty($role)) {
                    if(I('get.do') == 'delete') {
                        if($this->acl->removeRole($id)) {
                            $this->success('成功删除用户组', U('control/acl/roles'));
                            exit;
                        } else {
                            $this->error('操作失败, 请稍后重试');
                        }
                    }
                }
            }
            if(IS_POST) {
                $input = coll_elements(array('title', 'status', 'remark'), I('post.'));
                $input['title'] = trim($input['title']);
                if(empty($input['title'])) {
                    $this->error('请输入用户组名称');
                }
                $input['status'] = $input['status'] == '-1' ? '-1' : '0';
                $input['parent'] = '0';

                if(!empty($role)) {
                    //编辑组
                    $ret = $this->acl->table('__USR_ROLES__')->data($input)->where("`id`={$id}")->save();
                    if(empty($ret)) {
                        $this->error('保存用户组失败, 请稍后重试');
                    } else {
                        $this->success('成功保存用户组', U('control/acl/roles'));
                        exit;
                    }
                } else {
                    //新增组
                    $ret = $this->acl->table('__USR_ROLES__')->data($input)->add();
                    if(empty($ret)) {
                        $this->error('保存新增用户组失败, 请稍后重试');
                    } else {
                        $this->success('成功新增用户组', U('control/acl/roles'));
                        exit;
                    }
                }
            }
        }

        $this->assign('roles', $roles);
        $this->display();
    }
}