<?php
class ControllerCommonFooter extends Controller {   
	protected function index() {
		$this->load_language('common/footer');
		
		$this->data['text_footer'] = sprintf($this->language->get('text_footer'), VERSION);
		
		if(isset($this->request->get['token'])){
			$this->data['token']=$this->request->get['token'];
		}else{
			$this->data['token']='';
		}
		
		$this->id = 'footer';
		$this->template = 'common/footer.tpl';
	
    	$this->render();
  	}
}
?>