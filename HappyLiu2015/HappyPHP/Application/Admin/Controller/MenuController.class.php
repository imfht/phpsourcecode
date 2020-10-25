<?php
namespace Admin\Controller;

use Think\Controller;

class MenuController extends AdminController {
    /**
     * 菜单首页
     */
    public function index() {
        $where['pid'] = 0;
        $pid = I('get.pid', 0, 'intval');
        $id = I('post.id', 0, 'intval');
        if($pid) {
            $where['pid'] = $pid;
            $level = M('Menu')->where(array('id'=>$pid))->getField('level');
            $level++;
        } else {
            $level = 1; // 一级菜单页面
        }

        if(IS_POST) {
            $Menu = M('Menu');
            $type = I('get.type', '', 'trim');
            $rules = array(
                array('tag','','标识符已存在！',0,'unique',3), // 存在即验证tag字段是否唯一
            );
            /* 添加菜单 */
            if($type == 'add' && I('post.isNew' == 'true')) {
                if($data = $Menu->validate($rules)->create()) {
                    if($pid) {
                        $data['pid'] = $pid;
                        $data['level'] = $level;
                    }
                    $data['add_time'] = time();
                    $id = $Menu->add($data);
                    if($id) {
                        $this->ajaxReturn('SUC', $id);
                    } else {
                        $this->ajaxReturn('插入数据失败');
                    }
                } else {
                    $this->ajaxReturn($Menu->getError());
                }
            } elseif ($type == 'edit') { /* 编辑菜单 */
                if($id) {
                    if($data = $Menu->validate($rules)->create()) {
                        if($Menu->save($data)) {
                            $this->ajaxReturn('SUC', $id);
                        } else {
                            $this->ajaxReturn('更新数据失败或者数据未变动过！');
                        }
                    } else {
                        $this->ajaxReturn($Menu->getError());
                    }
                } else {
                    $this->ajaxReturn('更新数据失败');
                }
            } elseif($type == 'remove') { /* 删除菜单 */
                $where = array();
                $ids = I('post.ids', array());
                $ids = is_array($ids) ? $ids : array($ids);
                $where['id'] = array('in', $ids);
                if($Menu->where($where)->delete()) {
                    $this->ajaxReturn();
                } else {
                    $this->ajaxReturn('删除数据失败');
                }
            } else {
                $this->ajaxReturn('非法操作');
            }
        }

        $this->pid = $pid;
        $this->level = $level;
        $this->display();
    }

    /**
     * 根据PID获取菜单列表
     *
     * @param int $pid
     * @return mixed
     */
    public function menuList($pid=0) {
        $ret = array();
        $Menu = M('Menu');
        $where['pid'] = $pid;
        $ret['results'] = $Menu->where($where)->count();
        $ret['rows'] = $Menu->where($where)->limit(I('post.start'),I('post.limit'))->select();
        //$ret['sql'] = $Menu->getLastSql();

        exit(json_encode($ret));
    }
}
