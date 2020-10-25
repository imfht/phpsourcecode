<?php
class ControllerAccountTransaction extends Controller {
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

		$this->load_language('account/transaction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/transaction');

		$this->data['column_amount'] = sprintf($this->language->get('column_amount'), $this->config->get('config_currency'));
				
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}		
		
		$this->data['transactions'] = array();
		
		$data = array(				  
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 40
		);
		
		$transaction_total = $this->model_account_transaction->getTotalTransactions($data);
	
		$results = $this->model_account_transaction->getTransactions($data);
 		
    	foreach ($results as $result) {
			$this->data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}	

		$pagination = new MobilePagination();
		$pagination->total = $transaction_total;
		$pagination->page = $page;
			$pagination->limit = $this->config->get('config_catalog_limit'); 
		$pagination->style_links = 'pageNumber';
		$pagination->url = $this->url->link('account/transaction', 'page={page}', 'SSL');
		$this->data['pagination'] = $pagination->render();
		
		$this->data['total'] = $this->currency->format($this->customer->getBalance());
		
		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/transaction.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/transaction.tpl';
		} else {
			$this->template = 'default/template/account/transaction.tpl';
		}
		
		$this->children = array(
			'common/header',
			'common/nav',
		
		);
						
		$this->response->setOutput($this->render());		
	} 		
}
?>