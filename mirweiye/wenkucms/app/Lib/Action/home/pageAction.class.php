<?php

class pageAction extends frontendAction {
      

   
    public function index() {

        // 取seo信息
        $seoconfig=$this->_config_seo(C('wkcms_seo_config.page'));
        $this->assign('seoconfig',$seoconfig);

        //获得列表
        $pagecate = D('page')->where(array('status' => 1))->order('ordid')->select();

        $id=$this->_request('id','trim');
        $page = D('page')->where(array('id' => $id,'status' => 1))->order('ordid')->find();

        $title = $page['title'];

        // print_r($page);  
        $this->assign('pagecate', $pagecate);
        $this->assign('title', $title);
        $this->assign('page', $page);
        $this->assign('id', $id);
        $this->display();
       
         
    }

     
}