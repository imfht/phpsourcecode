<?php
class slideAction extends backendAction {
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('slide');
        $this->assign('img_dir', attach('','slide'));
    }

    public function _search() {
        $map = array();
        ($keyword = $this->_request('keyword', 'trim')) && $map['name'] = array('like', '%'.$keyword.'%');
        $this->assign('search', array(
            'keyword' => $keyword,
        ));
        return $map;
    }

    public function _before_index() {
        $big_menu = array(
            'title' => L('slide_add'),
            'iframe' => U('slide/add'),
            'id' => 'add',
            'width' => '520',
            'height' => '410',
        );
        $this->assign('big_menu', $big_menu);

        //默认排序
        $this->sort = 'ordid';
        $this->order = 'ASC';
    }

    public function _before_add() {
        
    }

    protected function _before_insert($data) {

    }

    public function _before_edit() {
        
    }

    protected function _before_update($data) {

    }

    //上传图片
    public function ajax_upload_img() {
        $type = $this->_get('type', 'trim', 'img');
        if (!empty($_FILES[$type]['name'])) {
            $dir = date('ym/d/');
            $result = $this->_upload($_FILES[$type], 'slide/'. $dir );
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