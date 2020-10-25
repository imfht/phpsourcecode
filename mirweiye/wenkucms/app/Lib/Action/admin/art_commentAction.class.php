<?php
class art_commentAction extends backendAction
{
    public function _initialize() {
        parent::_initialize();
        $this->_mod = M('art_comment');
        
    }

    public function index() {
        $prefix = C(DB_PREFIX);

        if ($this->_request("sort", 'trim')) {
            $sort = $this->_request("sort", 'trim');
        } else {
            $sort = $prefix.'art_comment.id';
        }
        if ($this->_request("order", 'trim')) {
            $order = $this->_request("order", 'trim');
        } else {
            $order = 'DESC';
        }

       $p = $this->_get('p','intval',1);
        $this->assign('p',$p);
        
        $where = '1=1';
        $keyword = $this->_request('keyword','trim','');
        $keyword && $where .= " AND ((".$prefix."user.username LIKE '%".$keyword."%') OR (".$prefix."article.title LIKE '%".$keyword."%') OR (".$prefix."art_comment.info LIKE '%".$keyword."%') )";
        $search = array();
        $keyword && $search['keyword'] = $keyword;
        $this->assign('search',$search);

        $count = $this->_mod->join($prefix.'user ON '.$prefix.'user.id='.$prefix.'art_comment.uid')->join($prefix.'article ON '.$prefix.'article.id='.$prefix.'art_comment.art_id')->where($where)->count($prefix.'art_comment.id');
        $pager = new Page($count,15);
      $list  = $this->_mod->field($prefix.'art_comment.*,'.$prefix.'user.username,'.$prefix.'article.title as art_name,'.$prefix.'article.img')->join($prefix.'user ON '.$prefix.'user.id='.$prefix.'art_comment.uid')->join($prefix.'article ON '.$prefix.'article.id='.$prefix.'art_comment.art_id')->where($where)->order($sort . ' ' . $order)->limit($pager->firstRow.','.$pager->listRows)->select();
        
        $this->assign('list',$list);
        $this->assign('page',$pager->show());

        $this->assign('list_table', true);

        $this->display();
    }
    
    /**
     * 删除
     */
    public function delete()
    {
        $ids = trim($this->_request('id'), ',');
        if ($ids) {
            $item_ids = $this->_mod->where(array('id'=>array('in', $ids)))->getField('item_id', true);
            if (false !== $this->_mod->delete($ids)) {
                $item_mod = D('item');
                foreach ($item_ids as $item_id) {
                    $item_mod->update_comments($item_id);
                }
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'));
            } else {
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
            }
        } else {
            IS_AJAX && $this->ajaxReturn(0, L('illegal_parameters'));
        }
    }
}