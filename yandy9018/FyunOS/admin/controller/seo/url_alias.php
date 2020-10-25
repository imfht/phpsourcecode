<?php    
class ControllerSeoUrlAlias extends Controller { 
	private $error = array();
	
	private function init(){
		$this->load->language('seo/url_alias');
		
	 	$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('tool/url_alias');
	}
  
  	public function index() {
		$this->init();
		
    	$this->getList();
  	}
  
  	public function insert() {
		$this->init();
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			$this->model_tool_url_alias->addUrlAlias($this->request->post);
			
			$this->operateSuccess();
		}
    
    	$this->getForm();
  	} 
   
  	public function update() {
		$this->init();
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
    		$this->model_tool_url_alias->editAlias($this->request->get['url_alias_id'], $this->request->post);
			
			$this->operateSuccess();
		}
    
    	$this->getForm();
  	}   

  	public function delete() {
		$this->init();
			
    	if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $url_alias_id) {
				$this->model_tool_url_alias->deleteAlias($url_alias_id);
			}
			
			$this->operateSuccess();
    	}
    	
    	if(isset($this->request->get['url_alias_id'])){
    		$this->model_tool_url_alias->deleteAlias($this->request->get['url_alias_id']);
    		
    		$this->operateSuccess();
    	}
	
    	$this->getList();
  	}  
    
  	private function getList() {
  		$this->initFilterData();
  		
		$url=$this->getUrlDefaultParameters();
		
		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('seo/url_alias', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		$this->data['insert'] =$this->url->link('seo/url_alias/insert'.$url);
		$this->data['delete'] =$this->url->link('seo/url_alias/delete'.$url);

		$this->data['alias_urls'] = array();

		$urls_total = $this->model_tool_url_alias->getTotalAliasUrls($this->filter_data);
		
		$results = $this->model_tool_url_alias->getAliasUrls($this->filter_data);
		
    	foreach ($results as $result) {
    		$action = array();
			
			$action['edit'] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('seo/url_alias/update'. '&url_alias_id=' . $result['url_alias_id'].$url)
			);
			
			$action['delete'] = array(
				'text' => $this->language->get('text_delete'),
				'href' => $this->url->link('seo/url_alias/delete'. '&url_alias_id=' . $result['url_alias_id'].$url)
			);
			
						
			$this->data['alias_urls'][] = array(
				'url_alias_id' => $result['url_alias_id'],
				'query'            => $result['query'],
				'keyword'      => $result['keyword'],
				'category'      => $result['category'],
				'selected'        => isset($this->request->post['selected']) && in_array($result['manufacturer_id'], $this->request->post['selected']),
				'action'          => $action
			);
		}
	
 
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		$this->data['success'] =$this->common-> getDisposableSession('success');
				
		$url_p=$this->getUrlPaginationPatameters();

		$pagination = new Pagination();
		$pagination->total = $urls_total;
		$pagination->page = $this->filter_data['page'];
		$pagination->limit = $this->filter_data['limit'];
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('catalog/manufacturer'. $url_p . '&page={page}');
			
		$this->data['pagination'] = $pagination->render();

		
		//$this->render_admin('seo/url_alias_list.tpl');
		$this->template = 'sale/url_alias_list.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}
	
	private function errorHanlder(){
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['query'])) {
			$this->data['error_query'] = $this->error['query'];
		} else {
			$this->data['error_query'] = '';
		}
		
  		if (isset($this->error['keyword'])) {
			$this->data['error_keyword'] = $this->error['keyword'];
		} else {
			$this->data['error_keyword'] = '';
		}
	}
  
  	private function getForm() {
 		$this->errorHanlder();
		    
		$url=$this->getUrlDefaultParameters();
		
		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('seo/url_alias', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
							
		if (!isset($this->request->get['url_alias_id'])) {
			$this->data['action'] = $this->url->link('seo/url_alias/insert'.$url);
		} else {
			$this->data['action'] = $this->url->link('seo/url_alias/update'. '&url_alias_id=' . $this->request->get['url_alias_id'].$url);
		}
		
		$this->data['cancel'] = $this->url->link('seo/url_alias'.$url);

		$this->data['token'] = $this->session->data['token'];
		
    	if (isset($this->request->get['url_alias_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$url_info = $this->model_tool_url_alias->getUrlAlias($this->request->get['url_alias_id']);
    	}
    	    	
  		if (isset($this->request->post['query'])) {
			$this->data['query'] = $this->request->post['query'];
		} elseif (isset($url_info)) {
			$this->data['query'] = $url_info['query'];
		} else {
			$this->data['query'] = '';
		}
		
		if (isset($this->request->post['keyword'])) {
			$this->data['keyword'] = $this->request->post['keyword'];
		} elseif (isset($url_info)) {
			$this->data['keyword'] = $url_info['keyword'];
		} else {
			$this->data['keyword'] = '';
		}
		
  		if (isset($this->request->post['category'])) {
			$this->data['category'] = $this->request->post['category'];
		} elseif (isset($url_info)) {
			$this->data['category'] = $url_info['category'];
		} else {
			$this->data['category'] = '';
		}
		
		$this->render_admin('seo/url_alias_form.tpl');
	} 

	private $filter_data;
	
	private function initFilterData(){
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		
		$this->filter_data = array(
			'page' => $page,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
	}
	
	private function operateSuccess(){
  		$this->session->data['success'] = $this->language->get('text_success');
			
		$url=$this->getUrlDefaultParameters();
		
		$this->redirect($this->url->link('seo/url_alias'.$url));
  	}
	 
  	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'seo/url_alias')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
    	
  		if ((strlen(utf8_decode($this->request->post['query'])) < 1)) {
			$this->error['query'] = $this->language->get('error_query');
		}
		
  		if ((strlen(utf8_decode($this->request->post['keyword'])) < 1)) {
			$this->error['keyword'] = $this->language->get('error_keyword');
		}
    	
		if (!$this->error) {
	  		return TRUE;
		} else {
	  		return FALSE;
		}
  	}    

  	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'seo/url_alias')) {
			$this->error['warning'] = $this->language->get('error_permission');
    	}	
		
		if (!$this->error) {
	  		return TRUE;
		} else {
	  		return FALSE;
		}  
  	}
  	
	private function getUrlDefaultParameters(){
  		$url = '';
			
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		return $url;
  	}
  	
  	private function getUrlPaginationPatameters(){
  		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		return $url;
  	}
  	
  	private function  getUrlSortParameters(){
  		$url = '';
  		
		if ($this->filter_data['order'] == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		return $url;
  	}
}
?>