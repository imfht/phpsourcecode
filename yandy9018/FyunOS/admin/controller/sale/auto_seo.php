<?php
class ControllerSaleAutoSEO extends Controller {
    private $error = array();
    public function index () {
       $this->load_language('sale/auto_seo');
      
       $this->document->setTitle($this->language->get('heading_title'));
	   $this->load->model('setting/setting');
       $this->load->model('sale/auto_seo');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            if (isset($this->request->post['all_seo'])) {
            	$this->model_sale_auto_seo->generateAll();
            }
          	$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('sale/auto_seo', 'token=' . $this->session->data['token'], 'SSL'));
        }
        
   		 if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
        
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
        
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);
   		
   		$this->data['breadcrumbs'][] = array(
   		   	'text'      => $this->language->get('heading_title_1'),
   		   	'href'      => $this->url->link('setting/parameter', 'token=' . $this->session->data['token'], 'SSL'),
   		   	'separator' => $this->language->get('text_breadcrumb_separator')
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('sale/auto_seo', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
        
       
        $this->data['action'] =  $this->url->link('sale/auto_seo', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel'] = $this->url->link('sale/auto_seo', 'token=' . $this->session->data['token'], 'SSL'); 
        
        $this->data['heading_title'] = $this->language->get('heading_title');
       
        $this->template = 'sale/auto_seo.tpl';
        $this->id = 'content';
		$this->layout = 'layout/parameter';
		$this->render();
    }
    private function validate () {
        if (! $this->user->hasPermission('modify', 'sale/auto_seo')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (! $this->error) {
            return true;
        } else {
            return false;
        }
    }
} 