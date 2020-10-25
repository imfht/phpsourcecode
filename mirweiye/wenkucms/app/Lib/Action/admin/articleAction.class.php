<?php
class articleAction extends backendAction {
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('article');
        $this->_cate_mod = D('article_cate');
    }
    public function _before_index() {
         // 获取栏目分类
        $catelist = D('article_cate')->where(array('pid' => 0))->select();
        foreach ($catelist as $k => $v) {
            $catelist[$k]['erji'] = D('article_cate')->where(array('pid' => $v['id']))->select();
        }
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
        ($keyword = $this->_request('keyword', 'trim')) && $map['title'] = array('like', '%' . $keyword . '%');
        $cate_id = $this->_request('cate_id', 'intval');
        if ($cate_id) {
            $id_arr = $this->_cate_mod->get_child_ids($cate_id, true);
            $map['cateid'] = array('IN', $id_arr);
            $spid = $this->_cate_mod->where(array('id' => $cate_id))->getField('spid');
        }
        $this->assign('search', array('time_start' => $time_start, 'time_end' => $time_end, 'cate_id' => $cate_id, 'selected_ids' => $selected_ids, 'status' => $status, 'keyword' => $keyword,));
        return $map;
    }
    protected function _before_insert($data) {
        return $data;
    }

    public function _before_add() {
        // 获取栏目分类
        $catelist = D('article_cate')->where(array('pid' => 0))->select();
        foreach ($catelist as $k => $v) {
            $catelist[$k]['erji'] = D('article_cate')->where(array('pid' => $v['id']))->select();
        }
        $this->assign('catelist', $catelist);
    }

    public function _before_edit() {
        $this->_before_add();
        $id = $this->_get('id', 'intval');
        $article = $this->_mod->field('id,cateid')->where(array('id' => $id))->find();
        $spid = $this->_cate_mod->where(array('id' => $article['cateid']))->getField('spid');
        if ($spid == 0) {
            $spid = $article['cateid'];
        } else {
            $spid.= $article['cateid'];
        }
        $this->assign('selected_ids', $spid);
    }
    protected function _before_update($data) {
        if (!empty($_FILES['img']['name'])) {
            $art_add_time = date('ym/d/');
            //删除原图
            $old_img = $this->_mod->where(array('id' => $data['id']))->getField('img');
            $old_img = $this->_get_imgdir() . $old_img;
            is_file($old_img) && @unlink($old_img);
            //上传新图
            $result = $this->_upload($_FILES['img'], 'article/' . $art_add_time, array('width' => '130', 'height' => '100', 'remove_origin' => true));
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
