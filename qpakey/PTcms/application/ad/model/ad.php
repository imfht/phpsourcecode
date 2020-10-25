<?php

class AdModel extends PT_Model {

    /**
     * 插入数据
     *
     * @param $param
     * @return mixed
     */
    public function add($param) {
        $res = $this->db('ad')->insert($param);
        $this->createJs($res);
        return $res;
    }

    /**
     * 修改
     *
     * @param $param
     * @return mixed
     */
    public function edit($param) {
        //更新缓存
        $res = $this->db('ad')->update($param);
        $this->model->rm('ad', $param['id']);
        $this->createJs($param['id']);
        return $res;
    }

    /**
     * 删除数据
     *
     * @param $where
     */
    public function del($where) {
        $list = $this->db('ad')->where($where)->field('id,key')->select();
        foreach ($list as $v) {
            // 删除缓存
            $this->model->rm('ad', $v['id']);
            // 删除js
            F(PT_ROOT . '/public/' . $this->config->get('addir') . '/' . $v['key'] . '.js', null);
        }
        $this->db('ad')->where($where)->delete();
    }

    //获取列表
    public function getlist() {
        $list = (array)$this->db('ad')->select();
        foreach ($list as &$v) {
            if (isset($v['create_user_id'])) {
                //后台
                $v['create_username'] = $this->model->rm('user', $v['create_user_id'], 'name');
                $v['update_username'] = $this->model->rm('user', $v['update_user_id'], 'name');
                $v['url_edit'] = U('ad.manage.edit', array('id' => $v['id']));
                $v['url_show'] = U('ad.manage.show', array('id' => $v['id']));
                $v['create_time'] = $v['create_time'] ? date('Y-m-d H:i', $v['create_time']) : '';
                $v['update_time'] = $v['update_time'] ? date('Y-m-d H:i', $v['update_time']) : '';
            }
        }
        return $list;
    }

    //根据id生成js
    public function createJs($id) {
        $info = $this->model->rm('ad', $id);
        $file = PT_ROOT . '/public/' . $this->config->get('addir') . '/' . $info['key'] . '.js';
        //判断广告是否状态正常
        if ($info['status'] == 1) {
            if ($info['type'] == 1) {
                // html需要进行转换
                $content = $this->html2js($info['code']);
            } else {
                // js直接写入
                $content = $info['code'];
            }
            F($file, $content);
        } else {
            F($file, null);
        }
    }

    //html转换成js
    public function html2js($html) {
        $return = '';
        $str = str_replace("\r\n", "\n", $html);
        $str = explode("\n", addcslashes($str, '\'"\\'));
        for ($i = 0; $i < count($str); $i++) {
            $return .= "document.writeln(\"" . $str[$i] . "\");\r\n";
        }
        return $return;
    }
}