<?php  
class ControllerCommonLogin extends Controller { 
	private $error = array();
	          
	public function index() { 
    	$this->load_language('common/login');

		$this->document->setTitle($this->language->get('heading_title'));

		

		if ($this->request->server['REQUEST_METHOD'] == 'POST') { 
			if($this->user->login($this->request->post['username'], $this->request->post['password'])){
				 $this->redirect($this->url->link('common/home'));
			}
		}

		
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
    		$this->data['success'] = $this->session->data['success'];
    
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
				
    	$this->data['action'] = $this->url->link('common/login', '', 'SSL');
		$this->template = 'common/login.tpl';
		
		$this->response->setOutput($this->render());
				
		
  	}
		
	
}  
?>