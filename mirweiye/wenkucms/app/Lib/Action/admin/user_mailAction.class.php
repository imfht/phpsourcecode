<?php

class user_mailAction extends backendAction{

    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('user_mail');
    }
public function _before_index() {

            $big_menu = array(
                'title' => L('发送通知'),
                'iframe' => U('user_mail/add'),
                'id' => 'add',
                'width' => '500',
                'height' => '320'
            );
            $this->assign('big_menu', $big_menu);
        $map['sys']=1;
        
    }
 public function add() {
        if (IS_POST) {
            
            //用户
            //$to_name = $this->_post('to_name', 'trim');
            //发送者
           // $from_user = session('admin');
            //$from_name = $from_user['username'];
            //接收者
            $to_user = array(array('uid'=>'0'));
            if ($to_name) {
                //指定用户
                $to_name = split(',', $to_name);
                
                //$to_name = array('0'=>'feefasf','1'=>'111111');
                
                 $to_user = M('user')->field('uid,username')->where(array('username'=>array('in', $to_name)))->select();
            }
            //内容
            //自定义
                $info = $this->_post('info', 'trim');
               $title = $this->_post('title', 'trim');
                
                !$info && $this->ajaxReturn(0, L('message_empty'));
           !$title && $this->ajaxReturn(0, '标题为空');
            //逐条发送
           
           
            foreach ($to_user as $val) {
               
                $this->_mod->create(array(
                    'from_name' => 0,
                    'to_id' => $val['uid'],
                    'info' => $info,
                    'title' => $title,
                    'is_sys' =>'1',
                ));
                $this->_mod->add();
                
            }
            $this->ajaxReturn(1, L('operation_success'), '', 'add');
        } else {
            
            $response = $this->fetch();
            $this->ajaxReturn(1, '', $response);
        }
    }

    protected function _search() {
        $map = array();
        ($time_start = $this->_request('time_start', 'trim')) && $map['add_time'][] = array('egt', strtotime($time_start));
        ($time_end = $this->_request('time_end', 'trim')) && $map['add_time'][] = array('elt', strtotime($time_end)+(24*60*60-1));
        ($keyword = $this->_request('keyword', 'trim')) && $map['title'] = array('like', '%'.$keyword.'%');
        
        
        if($from_name = $this->_request('from_name', 'trim'))
        {
        	$map1['username'] = array('like', '%'.$from_name.'%');
            $fromid=D('user')->where($map1)->getField('uid',true);
            $map['fromid']=array('in',$fromid);
        }
        
        if($to_name = $this->_request('to_name', 'trim'))
        {
        	$map1['username'] = array('like', '%'.$to_name.'%');
        	$toid=D('user')->where($map1)->getField('uid',true);
        	$map['toid']=array('in',$toid);
        }
        
     
 
         $map['re_id'] = array('eq',0);
         
        $this->assign('search', array(
            'time_start' => $time_start,
            'time_end' => $time_end,
            'from_name' => $from_name,
            'to_name'   => $to_name,
            'type'  => $type,
            'keyword' => $keyword,
        ));
        return $map;
    }


}