<?php
// 本类由系统自动生成，仅供测试用途
class topicAction extends frontendAction {
        
	public function _initialize() {
        parent::_initialize();
        global $userinfo;
        $userinfo= $this->visitor->info;
       
        $seoconfig=$this->_config_seo(C('wkcms_seo_config.ucenter'));
        $this->assign('seoconfig',$seoconfig);
        
        
        $this->assign('action',$action);
    }
	public function index(){
		
		
		$this->display();

	}
   
	
	
	
	
	
}