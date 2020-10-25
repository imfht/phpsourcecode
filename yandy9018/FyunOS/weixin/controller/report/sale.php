<?php
class ControllerReportSale extends Controller { 
	public function index() {  
		$this->load_language('report/sale_order');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '';
		}
		
		if (isset($this->request->get['filter_group'])) {
			$filter_group = $this->request->get['filter_group'];
		} else {
			$filter_group = 'week';
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = 0;
		}	
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';
						
		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}
		
		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
		
		if (isset($this->request->get['filter_group'])) {
			$url .= '&filter_group=' . $this->request->get['filter_group'];
		}		

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
								
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

   		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),       		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
   		    'text'      => $this->language->get('text_sale_trend'),
   			'href'      => $this->url->link('report/sale', 'token=' . $this->session->data['token'] . $url, 'SSL'),
   		    'separator' => $this->language->get('text_breadcrumb_separator')
   		);
   		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('report/sale_order', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		$this->load->model('report/sale');
		
		$this->data['orders'] = array();
		
		$data = array(
			'filter_date_start'	     => $filter_date_start, 
			'filter_date_end'	     => $filter_date_end, 
			'filter_group'           => $filter_group,
			'filter_order_status_id' => $filter_order_status_id,
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit')
		);
		
		$order_total = $this->model_report_sale->getTotalOrders($data);
		
		$results = $this->model_report_sale->getOrders($data);
		
		foreach ($results as $result) {
			$this->data['orders'][] = array(
				'date_start' => date($this->language->get('date_format_short'), strtotime($result['date_start'])),
				'date_end'   => date($this->language->get('date_format_short'), strtotime($result['date_end'])),
				'orders'     => $result['orders'],
				'products'   => $result['products'],
				'tax'        => $this->currency->format($result['tax'], $this->config->get('config_currency')),
				'total'      => $this->currency->format($result['total'], $this->config->get('config_currency'))
			);
		}

		$this->data['token'] = $this->session->data['token'];
		
		$this->data['week_report'] =$this->url->link('report/sale', '&range=week&token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['year_report'] =$this->url->link('report/sale', '&range=year&token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['month_report'] =$this->url->link('report/sale', '&range=month&token=' . $this->session->data['token'] . $url, 'SSL');
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->data['groups'] = array();

		$this->data['groups'][] = array(
			'text'  => $this->language->get('text_year'),
			'value' => 'year',
		);

		$this->data['groups'][] = array(
			'text'  => $this->language->get('text_month'),
			'value' => 'month',
		);

		$this->data['groups'][] = array(
			'text'  => $this->language->get('text_week'),
			'value' => 'week',
		);

		$this->data['groups'][] = array(
			'text'  => $this->language->get('text_day'),
			'value' => 'day',
		);

		$url = '';
						
		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}
		
		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
		
		if (isset($this->request->get['filter_group'])) {
			$url .= '&filter_group=' . $this->request->get['filter_group'];
		}		

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
				
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('report/sale_order', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();		

		$this->data['filter_date_start'] = $filter_date_start;
		$this->data['filter_date_end'] = $filter_date_end;		
		$this->data['filter_group'] = $filter_group;
		$this->data['filter_order_status_id'] = $filter_order_status_id;
		
		$this->trend_chart();
		
		$this->template = 'report/sale.tpl';
		$this->id = 'content';
		$this->layout = 'layout/report';
		$this->render();
	}
	
	public function trend_chart() {
		$this->load->language('common/home');
	
		$data = array();
	
		$data['order'] = array();
		$data['customer'] = array();
		$data['xaxis'] = array();
	
		$data['order']['label'] = $this->language->get('text_order');
		$data['customer']['label'] = $this->language->get('text_customer');
	
		if (isset($this->request->get['range'])) {
			$range = $this->request->get['range'];
		} else {
			$range = 'week';
		}
	
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