<?php
class forumAction extends backendAction {
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('forum');
        $this->_cate_mod = D('forum_cate');
    }
    public function _before_index() {
         // 获取栏目分类
        $catelist = $this->_cate_mod->where(array('status' => 1))->select();
        $this->assign('catelist', $catelist);
        
        $res = $this->_cate_mod->field('id,name')->select();
        $cate_list = array();
        foreach ($res as $val) {
            $cate_list[$val['id']] = $val['name'];
        }
        $this->assign('cate_list', $cate_list);
        $p = $this->_get('p', 'intval', 1);
        $this->assign('p', $p);
    }
    protected function _search() {
        $map = array();
        ($cate_id = $this->_request('cate_id', 'trim')) && $map['cateid'] = array('eq', $cate_id);
        ($keyword = $this->_request('keyword', 'trim')) && $map['title'] = array('like', '%'.$keyword.'%');
        $this->assign('search', array('keyword' => $keyword,'cate_id' => $cate_id,));
        return $map;
    }
    protected function _before_insert($data) {
        return $data;
    }

    public function _before_add() {
        // 获取栏目分类
        $catelist = $this->_cate_mod->where(array('status' => 1))->select();
        $this->assign('catelist', $catelist);
    }

    public function _before_edit() {
        $catelist = $this->_cate_mod->select();
        $this->assign('catelist', $catelist);
    }
    protected function _before_update($data) {
        if (!empty($_FILES['img']['name'])) {
            $art_add_time = date('ym/d/');
            //删除原图
            $old_img = $this->_mod->where(array('id' => $data['id']))->getField('img');
            $old_img = $this->_get_imgdir() . $old_img;
            is_file($old_img) && @unlink($old_img);
            //上传新图
            $result = $this->_upload($_FILES['img'], 'forum/' . $art_add_time, array('width' => '130', 'height' => '100', 'remove_origin' => true));
            if ($result['error']) {
                $this->error($result['info']);
            } else {
                $ext = array_pop(explode('.', $result['info'][0]['savename']));
                $data['img'] = $art_add_time . '/' . str_replace('.' . $ext, '_thumb.' . $ext, $result['info'][0]['savename']);
            }
        } else {
            unset($data['img']);
        }
        return $data;
    }
    /**
     * ajax获取标签
     */
    public function ajax_gettags() {
        $title = $this->_get('title', 'trim');
        if ($title) {
            $tags = D('tag')->get_tags_by_title($title);
            $tags = implode(',', $tags);
            $this->ajaxReturn(1, L('operation_success'), $tags);
        } else {
            $this->ajaxReturn(0, L('operation_failure'));
        }
    }
}
