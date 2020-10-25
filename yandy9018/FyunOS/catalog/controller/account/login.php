<?php 
class ControllerAccountLogin extends Controller {
	private $error = array();
	
	public function index() {
		if ($this->customer->isLogged()) {  
      		$this->redirect($this->url->link('account/account', '', 'SSL'));
    	}
	
    	$this->load_language('account/login');

    	$this->document->setTitle($this->language->get('heading_title'));
    	
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
		    if (isset($this->request->post['telephone'])) {
				$this->customer->mobileLogin($this->request->post['telephone']);
			}
    	}  
		
		if(isset($this->session->data['track_url'])){
			$this->data['track_url'] =$this->session->data['track_url'];
			}else{
				$this->data['track_url'] = $this->url->link('account/account');
				}
      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),       	
        	'separator' => false
      	);


    	
		if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], HTTP_SERVER) !== false || strpos($this->request->post['redirect'], HTTPS_SERVER) !== false)) {
			$this->data['redirect'] = $this->request->post['redirect'];
		} elseif (isset($this->session->data['redirect'])) {
      		$this->data['redirect'] = $this->session->data['redirect'];
	  		
			unset($this->session->data['redirect']);		  	
    	} else {
			$this->data['redirect'] = $this->url->link('account/account', '', 'SSL');
		}
        
		if (isset($this->session->data['success'])) {
    		$this->data['success'] = $this->session->data['success'];
    
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
       if(isset($this->request->get['phone'])){
		   $this->data['phone'] = $this->request->get['phone'];
		   }else{
			    $this->data['phone']="";
			   }
			   
	   if(isset($this->request->get['ubind'])){
		   $this->data['ubind'] = $this->request->get['ubind'];
		   }else{
			    $this->data['ubind']="";
			   }

		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/login.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/login.tpl';
		} else {
			$this->template = 'default/template/account/login.tpl';
		}
		
		$this->children = array(
			'common/header'	,
			'common/nav'	
		);
						
		$this->response->setOutput($this->render());
  	}
  public function auto() {
	  $this->customer->weixinLogin();
	  }
 
}
?>