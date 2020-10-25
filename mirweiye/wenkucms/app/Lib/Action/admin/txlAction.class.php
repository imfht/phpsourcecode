<?php
class txlAction extends backendAction {
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('txl');
        $this->_cate_mod = D('txl_cate');
        $this->assign('img_dir', attach('','txl'));
    }
    public function _before_index() {
         // 获取栏目分类
        $catelist = D('txl_cate')->where(array('pid' => 0 , 'status' => 1))->select();
        foreach ($catelist as $k => $v) {
            $catelist[$k]['erji'] = D('txl_cate')->where(array('pid' => $v['id'], 'status' => 1))->select();
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

        // 取地区名称
        $where['status']  = '1';
        $chengshi=D('txl_cate')->where($where)->select();
        foreach ($chengshi as $val) {
            $chengshi[$val['id']] = $val['name'];
        }
         $this->assign('chengshi',$chengshi);
    }
     
    protected function _before_insert($data) {
        return $data;
    }

    public function _before_add() {
        // 获取栏目分类
        $catelist = D('txl_cate')->where(array('pid' => 0, 'status' => 1))->select();
        foreach ($catelist as $k => $v) {
            $catelist[$k]['erji'] = D('txl_cate')->where(array('pid' => $v['id'], 'status' => 1))->select();
        }
        $this->assign('catelist', $catelist);
    }

    public function _before_edit() {
        // 获取栏目分类
        $catelist = D('txl_cate')->where(array('pid' => 0, 'status' => 1))->select();
        foreach ($catelist as $k => $v) {
            $catelist[$k]['erji'] = D('txl_cate')->where(array('pid' => $v['id'], 'status' => 1))->select();
        }
        $this->assign('catelist', $catelist);
    }

    //上传图片
    public function ajax_upload_img() {
        $type = $this->_get('type', 'trim', 'img');
        if (!empty($_FILES[$type]['name'])) {
            $dir = date('ym/d/');
            $result = $this->_upload($_FILES[$type], 'txl/'. $dir );
            if ($result['error']) {
                
                $data['status']=0;
                $data['info']=$result['info'];
               
            } else {
                $savename = $dir . $result['info'][0]['savename'];
                $data['status']=1;
                $data['info']=$savename;
                
            }
        } else {
            $data['status']=0;
                $data['info']=L('illegal_parameters');
            
        }
        
        echo json_encode($data);
    }

   
}
