<?php 
class ControllerAccountAccount extends Controller { 
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
		//$this->document->addStyle('catalog/view/theme/diancan/stylesheet/common.css');
	    $this->session->data['menu']="account";
		$this->load_language('account/account');

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
		$this->data['telephone'] = $this->config->get('config_telephone');
		if (isset($this->session->data['success'])) {
    		$this->data['success'] = $this->session->data['success'];
			
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		$this->data['logout'] = $this->url->link('account/logout');
		$this->data['order'] = $this->url->link('account/order');
			$this->data['edit'] = $this->url->link('account/edit');
				$this->data['reward'] = $this->url->link('account/reward');
					$this->data['transaction'] = $this->url->link('account/transaction');
		
		$this->load->model('account/order');
		
		$this->data['bind_status'] = $this->config->get('config_bind_status');
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$this->data['orders'] = array();
		
		$order_total = $this->model_account_order->getTotalOrders();
		
		$results = $this->model_account_order->getOrders(($page - 1) * $this->config->get('config_catalog_limit'), $this->config->get('config_catalog_limit'));
		$common = new Common($this->registry);
		
		foreach ($results as $result) {
			$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
		
			$this->data['orders'][] = array(
				'order_id'   => $result['order_id'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'products'   => $product_total,
				'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'href'       => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL')
			);
		}
		
		
		$this->data['name'] = $this->customer->getName();
		$this->data['id'] = $this->customer->getId();
		$this->data['total'] = $this->currency->format($this->customer->getBalance());
		$this->data['points'] = (int)$this->customer->getRewardPoints();
		$this->data['tel'] =$this->customer->getTelephone();
		$this->load->model('account/order');
		$this->data['orders'] = $this->model_account_order->getTotalOrders();
		$this->load->model('account/customer');
		$this->data['group'] = $this->model_account_customer->getCustomerGroup($this->customer->getCustomerGroupId());

		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/account.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/account.tpl';
		} else {
			$this->template = 'default/template/account/account.tpl';
		}
		
		$this->children = array(
			'common/nav',
			'common/header',	
		);
				
		$this->response->setOutput($this->render());
  	}
}
?>