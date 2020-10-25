<?php

class txlAction extends frontendAction {
      
    public function index() {
        // 取seo信息
        $seoconfig=$this->_config_seo(C('wkcms_seo_config.txl'));
        $this->assign('seoconfig',$seoconfig);

        //分类
        $catelist = D('txl_cate')->where(array('status'=>'1'))->select();

        $id=$this->_request('id','trim');
        //获得列表
        //
        $map = array();
        $map['status'] = 1;

        if ($id) {
            $map['cate_id'] = $id;
        }
        $count = D('txl')->where($map)->count();
        $page=new Page($count,20);
        $show=$page->show();
        if ($id) {
            $where['cate_id'] = $id;
        }
        $where['status'] = 1;
        $list = D('txl')->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
      
        $this->assign('list', $list);
        $this->assign('catelist', $catelist);
        $this->assign('id', $id);
        $this->assign('page', $show);
        $this->display();
    }

     public function info() {
        $id = $this->_request('id','trim');
        $info = D('txl')->where(array('id'=>$id))->find();

        if(substr(strtolower($info['url']), 0, 4) == 'www.') {
            $info['url'] = 'http://'.$info['url'];
        }

        $where['status']  = '1';
        $chengshi=D('txl_cate')->where($where)->select();
        foreach ($chengshi as $val) {
            $chengshi[$val['id']] = $val['name'];
        }
        $this->assign('chengshi',$chengshi);

        $this->assign('id', $id);
        $this->assign('info', $info);
        $this->ajaxReturn(1, '', $this->fetch());
     }

     public function jiucuo() {
        $id = $this->_request('id','trim');
        $data = $this->_request();
        $data['add_time'] = time();
        if (D('txl_jiucuo')->add($data)) {
            $this->ajaxReturn(1, '纠错成功,感谢您的支持！');
        }else{
            $this->ajaxReturn(0, '纠错失败，请再次尝试提交！');
        }
     }

     
}