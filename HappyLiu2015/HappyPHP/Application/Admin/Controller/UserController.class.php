<?php
namespace Admin\Controller;

use Think\Controller;

class UserController extends AdminController {
    /**
     * 用户首页及相关操作
     */
    public function index() {
        $uid = I('post.uid', 0, 'intval');
        if(IS_POST) {
            $User = M('User');
            $type = I('get.type', '', 'trim');
            $rules = array(
                array('username','','用户名已存在！',0,'unique',3), // 存在即验证nickname字段是否唯一
                array('nickname','','用户昵称已存在！',0,'unique',3), // 存在即验证nickname字段是否唯一
            );
            /* 添加 */
            if($type == 'add' && I('post.isNew' == 'true')) {
                if($data = $User->validate($rules)->create()) {
                    $data['addtime'] = $data['updatetime'] = time();
                    $data['state'] = 0;
                    if(!$data['password']) {
                        $data['password'] = 123456;
                    }
                    $data['password'] = md5(C('ENCODE_KEY').$data['password']);
                    $uid = $User->add($data);
                    if($uid) {
                        $this->ajaxReturn('SUC', $uid);
                    } else {
                        $this->ajaxReturn('插入数据失败');
                    }
                } else {
                    $this->ajaxReturn($User->getError());
                }
            } elseif ($type == 'edit') { /* 编辑 */
                if($uid) {
                    if($data = $User->validate($rules)->create()) {
                        $data['updatetime'] = time();
                        if(!$data['password']) {
                            unset($data['password']);
                        } else {
							$data['password'] = md5(C('ENCODE_KEY').$data['password']);	
						}
                        if($User->save($data)) {
                            $this->ajaxReturn('SUC', $uid);
                        } else {
                            $this->ajaxReturn('更新数据失败或者数据未变动过！');
                        }
                    } else {
                        $this->ajaxReturn($User->getError());
                    }
                } else {
                    $this->ajaxReturn('更新数据失败');
                }
            } elseif($type == 'remove') { /* 删除 */
                $where = array();
                $ids = I('post.ids', array());
                $ids = is_array($ids) ? $ids : array($ids);
                if(in_array(1,$ids)) {
                    unset($ids[array_search(1, $ids)]);
                }
                if($ids) {
                    $where['uid'] = array('in', $ids);
                    if($User->where($where)->delete()) {
                        $this->ajaxReturn();
                    } else {
                        $this->ajaxReturn('删除数据失败');
                    }
                } else {
                    $this->ajaxReturn('请选择要删除的数据，但不能包括用户编号为1的用户。');
                }
            } else {
                $this->ajaxReturn('非法操作');
            }
        }
        $this->display();
    }

    /**
     * 获取用户列表
     *
     * @return mixed
     */
    public function userList() {
        $this->lists('User', array(), array('uid','username','sex','nickname','level','credit','status'));
    }

}
