<?php
class ControllerAccountReward extends Controller {
	public function index() {
		//判断用户来自哪里
		if (!$this->customer->isLogged()) {
			if($this->config->get('config_bind_status')=='1'){
				if($this->config->get('config_phone_login')=='1'){
					$this->redirect($this->url->link('account/login&phone=1', '', 'SSL'));
				}else{
					$this->customer->weixinLogin();
					}
			}elseif($this->config->get('config_bind_status')=='2'){
				if(isset($this->request->get['openId'])){
					$this->session->data['open_id'] = $this->request->get['openId'];
					$this->redirect($this->url->link('account/account', '', 'SSL'));
					}else{
						$this->redirect($this->url->link('account/login&ubind=1', '', 'SSL'));
						}
				}else{
					$this->redirect($this->url->link('account/login', '', 'SSL'));
					}
		}
		
		
		$this->session->data['menu']="account";
		
		$this->load_language('account/reward');

		$this->document->setTitle($this->language->get('heading_title'));

      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(       	
        	'text'      => $this->language->get('text_account'),
			'href'      => $this->url->link('account/account', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);
		
      	$this->data['breadcrumbs'][] = array(       	
        	'text'      => $this->language->get('text_reward'),
			'href'      => $this->url->link('account/reward', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);
		
		$this->load->model('account/reward');

    	if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}		
		
		$this->data['rewards'] = array();
		
		$data = array(				  
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);
		
		$reward_total = $this->model_account_reward->getTotalRewards($data);
	
		$results = $this->model_account_reward->getRewards($data);
 		
    	foreach ($results as $result) {
			$this->data['rewards'][] = array(
				'order_id'    => $result['order_id'],
				'points'      => $result['points'],
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'href'        => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL')
			);
		}	

		$pagination = new MobilePagination();
		$pagination->total = $reward_total;
		$pagination->page = $page;
			$pagination->limit = $this->config->get('config_catalog_limit'); 
		$pagination->style_links = 'pageNumber';
		$pagination->url = $this->url->link('account/reward', 'page={page}', 'SSL');
		$this->data['pagination'] = $pagination->render();
			
		
		$this->data['total'] = (int)$this->customer->getRewardPoints();
		
		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/reward.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/reward.tpl';
		} else {
			$this->template = 'default/template/account/reward.tpl';
		}
		
		$this->children = array(
			'common/nav',
			'common/header',
		);
						
		$this->response->setOutput($this->render());		
	} 		
}
?>