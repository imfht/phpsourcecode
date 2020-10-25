<?php

class FriendlinkModel extends PT_Model {

    /**
     * 插入数据
     *
     * @param $param
     * @return mixed
     */
    public function add($param) {
        $param['create_user_id'] = $_SESSION['admin']['userid'];
        $param['create_time']    = NOW_TIME;
        return $this->insert($param);
    }

    /**
     * 修改
     *
     * @param $param
     * @return mixed
     */
    public function edit($param) {
        //更新缓存
        $param['update_user_id'] = $_SESSION['admin']['userid'];
        $param['update_time']    = NOW_TIME;
        return $this->update($param);
    }

    /**
     * 删除数据
     *
     * @param $where
     */
    public function del($where) {
        $this->where($where)->delete();
    }

    //获取列表
    public function getlist() {
        $list = (array)$this->select();
        foreach ($list as &$v) {
            $v['showname'] = $this->getshowname($v);
            if (isset($v['create_user_id'])) {
                //后台
                $v['url']             = truncate($v['url'], 30, '......');
                $v['create_username'] = $this->model->rm('user', $v['create_user_id'], 'name');
                $v['update_username'] = $this->model->rm('user', $v['update_user_id'], 'name');
                $v['url_edit']        = U('friendlink.manage.edit', array('id' => $v['id']));
                $v['create_time']     = $v['create_time'] ? date('Y-m-d H:i', $v['create_time']) : '';
                $v['update_time']     = $v['update_time'] ? date('Y-m-d H:i', $v['update_time']) : '';
            }
        }
        return $list;
    }

    //获取展示的链接名
    public function getshowname($v) {
        $v['showname'] = $v['name'];
        if ($v['isbold']) {
            $v['showname'] = '<b>' . $v['showname'] . '</b>';
        }
        if ($v['color'] !== '') {
            $v['showname'] = "<font color={$v['color']}>{$v['showname']}</font>";
        }
        return $v['showname'];
    }
}