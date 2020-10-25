<?php
class zjAction extends backendAction {

     public function _initialize() {
        parent::_initialize();
        $this->_mod = D('zj');
        
    }

    // 重写列表页 - wfgo
    public function index() {

        $count = $this->_mod->count();
        $pager = new Page($count, 15);
        $list = $this->_mod->order('id DESC')->limit($pager->firstRow.','.$pager->listRows)->select();
        foreach ($list as $k => $v) {
           $con =  M('doc_con')->where(array('zhuanji'=>$v['id']))->Count();
           $list[$k]['wds'] =  $con;
        }

        $this->assign('list',$list);
        $this->assign('page',$pager->show());
        $this->assign('keyword', array('keyword' => $keyword,));
        $this->display();
    }

    public function _search() {
        $map = array();
        ($start_time_min = $this->_request('start_time_min', 'trim')) && $map['start_time'][] = array('egt', strtotime($start_time_min));
        ($start_time_max = $this->_request('start_time_max', 'trim')) && $map['start_time'][] = array('elt', strtotime($start_time_max)+(24*60*60-1));
        ($end_time_min = $this->_request('end_time_min', 'trim')) && $map['end_time'][] = array('egt', strtotime($end_time_min));
        ($end_time_max = $this->_request('end_time_max', 'trim')) && $map['end_time'][] = array('elt', strtotime($end_time_max)+(24*60*60-1));
        $board_id = $this->_get('board_id', 'intval');
        $board_id && $map['board_id'] = $board_id;
        $style = $this->_request('style', 'trim');
        $style && $map['type'] = array('eq',$style);
        ($keyword = $this->_request('keyword', 'trim')) && $map['name'] = array('like', '%'.$keyword.'%');
        $this->assign('search', array(
            'start_time_min' => $start_time_min,
            'start_time_max' => $start_time_max,
            'end_time_min' => $end_time_min,
            'end_time_max' => $end_time_max,
            'board_id' => $board_id,
            'style'   => $style,
            'keyword' => $keyword,
        ));
        return $map;
    }
 
 
    public function _before_edit() {
        $id = $this->_get('id', 'intval');
     
    }

    

    //上传图片
    public function ajax_upload_img() {
        $type = $this->_get('type', 'trim', 'img');
        if (!empty($_FILES[$type]['name'])) {
            $dir = date('ym/d/');
            $result = $this->_upload($_FILES[$type], 'zj/'. $dir );
            if ($result['error']) {
                
                $data['status']=0;
                $data['info']=$result['info'];
               //$this->ajaxReturn(0, $result['info']);
            } else {
                $savename = $dir . $result['info'][0]['savename'];
                $data['status']=1;
                $data['info']=$savename;
               // $this->ajaxReturn(1, L('operation_success'), $savename);
            }
        } else {
            $data['status']=0;
                $data['info']=L('illegal_parameters');
            //$this->ajaxReturn(0, L('illegal_parameters'));
        }
        
        echo json_encode($data);
    }
}