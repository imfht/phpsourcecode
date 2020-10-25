<?php   
class ControllerCommonHome extends Controller {   
	public function index() {
    	$this->load_language('common/home');

		$this->document->setTitle($this->language->get('heading_title'));
		
		// Check install directory exists
 		if (is_dir(dirname(DIR_APPLICATION) . '/install')) {
			$this->data['error_install'] = $this->language->get('error_install');
		} else {
			$this->data['error_install'] = '';
		}

		
		// Check logs directory is writable
		$file = DIR_LOGS . 'test';
		
		$handle = fopen($file, 'a+'); 
		
		fwrite($handle, '');
			
		fclose($handle); 		
		
		if (!file_exists($file)) {
			$this->data['errorlogs'] = sprintf($this->language->get('error_logs'). DIR_LOGS);
		} else {
			$this->data['error_logs'] = '';
			
			unlink($file);
		}
										
		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

		$this->data['token'] = $this->session->data['token'];
		
		$this->load->model('sale/order');

		$this->data['total_sale'] = $this->currency->format($this->model_sale_order->getTotalSales(), $this->config->get('config_currency'));
		$this->data['total_shipping'] = $this->currency->format($this->model_sale_order->getTotalShipping(), $this->config->get('config_currency'));
		$this->data['total_sale_year'] = $this->currency->format($this->model_sale_order->getTotalSalesByYear(date('Y')), $this->config->get('config_currency'));
		$this->data['total_order'] = $this->model_sale_order->getTotalOrders();
		
		$this->load->model('sale/customer');
		
		$this->data['total_customer'] = $this->model_sale_customer->getTotalCustomers();
		$this->data['total_customer_approval'] = $this->model_sale_customer->getTotalCustomersAwaitingApproval();
		
		$this->load->model('catalog/review');
		
		$this->data['total_review'] = $this->model_catalog_review->getTotalReviews();
		$this->data['total_review_approval'] = $this->model_catalog_review->getTotalReviewsAwaitingApproval();
		
		$this->load->model('sale/affiliate');
		
		$this->data['total_affiliate'] = $this->model_sale_affiliate->getTotalAffiliates();
		$this->data['total_affiliate_approval'] = $this->model_sale_affiliate->getTotalAffiliatesAwaitingApproval();
				
		$this->data['orders'] = array(); 
		
		$data = array(
			'sort'  => 'o.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 10
		);
		
		$results = $this->model_sale_order->getOrders($data);
	

    	foreach ($results as $result) {
			$action = array();
			 
			$action[] = array(
				'text' => $this->language->get('text_view'),
				'href' => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'], 'SSL')
			);
					
			$this->data['orders1'][] = array(
				'order_id'   => $result['order_id'],
				'customer'   => $result['customer'],
				'telephone'   => $result['telephone'],
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'action'     => $action
			);
		}

		if ($this->config->get('config_currency_auto')) {
			$this->load->model('localisation/currency');
		
			$this->model_localisation_currency->updateCurrencies();
		}
		$this->data['product'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['add_product'] = $this->url->link('catalog/product/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['category'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['add_category'] = $this->url->link('catalog/category/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['manufacturer'] = $this->url->link('catalog/manufacturer', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['attribute'] = $this->url->link('catalog/attribute', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['attribute_group'] = $this->url->link('catalog/attribute_group', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['option'] = $this->url->link('catalog/option', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['review'] = $this->url->link('catalog/review', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['coupon'] = $this->url->link('sale/coupon', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['customer'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['customer_group'] = $this->url->link('sale/customer_group', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['download'] = $this->url->link('catalog/download', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['order'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['voucher'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['setting'] = $this->url->link('setting/store', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['custom'] = $this->url->link('setting/custom', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['server'] = $this->url->link('setting/server', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['banner'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['store_url'] = $this->config->get('config_url');
		$this->trend_chart();
		$this->id = 'content';
		$this->template = 'common/home.tpl';
		$this->layout = 'layout/default';
		$this->render();
  	}
	
	public function login() {
		$route = '';
		
		if (isset($this->request->get['route'])) {
			$part = explode('/', $this->request->get['route']);
			
			if (isset($part[0])) {
				$route .= $part[0];
			}
			
			if (isset($part[1])) {
				$route .= '/' . $part[1];
			}
		}
		
		$ignore = array(
			'common/login',
			'common/forgotten',
			'common/reset'
		);	
					
		if (!$this->user->isLogged() && !in_array($route, $ignore)) {
			return $this->forward('common/login');
		}
		
		if (isset($this->request->get['route'])) {
			$ignore = array(
				'common/login',
				'common/logout',
				'common/forgotten',
				'common/reset',
				'error/not_found',
				'error/permission'
			);
						
			$config_ignore = array();
			
			if ($this->config->get('config_token_ignore')) {
				$config_ignore = unserialize($this->config->get('config_token_ignore'));
			}
				
			$ignore = array_merge($ignore, $config_ignore);
						
			if (!in_array($route, $ignore) && (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token']))) {
				return $this->forward('common/login');
			}
		} else {
			if (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
				return $this->forward('common/login');
			}
		}
	}
	
	public function permission() {
		if (isset($this->request->get['route'])) {
			$route = '';
			
			$part = explode('/', $this->request->get['route']);
			
			if (isset($part[0])) {
				$route .= $part[0];
			}
			
			if (isset($part[1])) {
				$route .= '/' . $part[1];
			}
			
			$ignore = array(
				'common/home',
				'common/login',
				'common/logout',
				'common/forgotten',
				'common/reset',
				'error/not_found',
				'error/permission'		
			);			
						
			if (!in_array($route, $ignore) && !$this->user->hasPermission('access', $route)) {
				return $this->forward('error/permission');
			}
		}
	}	
	public function trend_chart() {
		$this->load->language('common/home');
	
		$data = array();
	
		$data['order'] = array();
		$data['customer'] = array();
		$data['xaxis'] = array();
	
		$data['order']['label'] = $this->language->get('text_order');
		$data['customer']['label'] = $this->language->get('text_customer');
	
	    $range = 'week';
	
	
		switch ($range) {
			case 'week':
				$date_start = strtotime('-' . date('w') . ' days');
	
				for ($i = 0; $i < 7; $i++) {
					$date = date('Y-m-d', $date_start + ($i * 86400));
	
					if($i==0)
						$this->data['range'] = "'".date('D', strtotime($date))."'";
					else
						$this->data['range'] .= ",'".date('D', strtotime($date))."'";
						
					$this->getData($i,$date,0);
				}
					
				break;
			default:
			case 'month':
				for ($i = 1; $i <= date('t'); $i++) {
				$date = date('Y') . '-' . date('m') . '-' . $i;
					
				if($i==1)
					$this->data['range'] = "'".date('j', strtotime($date))."'";
				else
					$this->data['range'] .= ",'".date('j', strtotime($date))."'";
				
				$this->getData($i,$date,1);
			}
			break;
			case 'year':
				for ($i = 1; $i <= 12; $i++) {
					if($i==1)
						$this->data['range'] = "'".date('M', mktime(0, 0, 0, $i, 1, date('Y')))."'";
					else
						$this->data['range'] .= ",'".date('M', mktime(0, 0, 0, $i, 1, date('Y')))."'";
					
					$this->getData($i,date('Y'),1);
				
				}
				break;
		}
	}
	
	private function getData($i,$date,$min){
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND DATE(date_added) = '" . $this->db->escape($date) . "' GROUP BY DATE(date_added)");
		
		if($i==$min){
			if ($query->num_rows) {
				$this->data['orders'] =(int)$query->row['total'];
			} else {
				$this->data['orders'] = 0;
			}
		}else{
			if ($query->num_rows) {
				$this->data['orders'] .=','.(int)$query->row['total'];
			} else {
				$this->data['orders'] .=','. 0;
			}
		}
			
		$query = $this->db->query("SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND DATE(date_added) = '" . $this->db->escape($date) . "' GROUP BY DATE(date_added)");
			
		if($i==$min){
			if ($query->num_rows) {
				$this->data['order_total'] =$query->row['total']/100;
			} else {
				$this->data['order_total'] = 0;
			}
		}else{
			if ($query->num_rows) {
				$this->data['order_total'] .=','.$query->row['total']/100;
			} else {
				$this->data['order_total'] .=','. 0;
			}
		}
			
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer` WHERE DATE(date_added) = '" . $this->db->escape($date) . "' AND status=1 GROUP BY DATE(date_added)");
		
		if($i==$min){
			if ($query->num_rows) {
				$this->data['customers'] =(int)$query->row['total'];
			} else {
				$this->data['customers'] = 0;
			}
		}else{
			if ($query->num_rows) {
				$this->data['customers'] .=','.(int)$query->row['total'];
			} else {
				$this->data['customers'] .=','. 0;
			}
		}
			
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "affiliate` WHERE DATE(date_added) = '" . $this->db->escape($date) . "' AND status=1 GROUP BY DATE(date_added)");
			
		if($i==$min){
			if ($query->num_rows) {
				$this->data['affiliate'] =(int)$query->row['total'];
			} else {
				$this->data['affiliate'] = 0;
			}
		}else{
			if ($query->num_rows) {
				$this->data['affiliate'] .=','.(int)$query->row['total'];
			} else {
				$this->data['affiliate'] .=','. 0;
			}
		}
			
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "review` WHERE DATE(date_added) = '" . $this->db->escape($date) . "' AND status=1 GROUP BY DATE(date_added)");
		
		if($i==$min){
			if ($query->num_rows) {
				$this->data['review'] =(int)$query->row['total'];
			} else {
				$this->data['review'] = 0;
			}
		}else{
			if ($query->num_rows) {
				$this->data['review'] .=','.(int)$query->row['total'];
			} else {
				$this->data['review'] .=','. 0;
			}
		}
	}
}
?>