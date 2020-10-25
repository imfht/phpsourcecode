<?php
class ControllerReportProductViewed extends Controller {
	public function index() {     
		$this->load_language('report/product_viewed');

		$this->document->setTitle($this->language->get('heading_title'));
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
				
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
			'href'      => $this->url->link('report/product_viewed', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);		
		
		$this->load->model('report/product');
		
		$data = array(
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
				
		$product_viewed_total = $this->model_report_product->getTotalProductsViewed($data); 
		
		$product_views_total = $this->model_report_product->getTotalProductViews(); 
		
		$this->data['products'] = array();
		
		$results = $this->model_report_product->getProductsViewed($data);
		
		foreach ($results as $result) {
			if ($result['viewed']) {
				$percent = round($result['viewed'] / $product_views_total * 100, 2);
			} else {
				$percent = 0;
			}
					
			$this->data['products'][] = array(
				'name'    => $result['name'],
				'model'   => $result['model'],
				'viewed'  => $result['viewed'],
				'percent' => $percent . '%'			
			);
		}
 
		$url = '';		
				
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
				
		$this->data['reset'] = $this->url->link('report/product_viewed/reset', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
						
		$pagination = new Pagination();
		$pagination->total = $product_viewed_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('report/product_viewed', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();
				 
		$this->template = 'report/product_viewed.tpl';
		$this->id = 'content';
		$this->layout = 'layout/report';
		$this->render();
	}
	
	public function reset() {
		$this->load_language('report/product_viewed');
		
		$this->load->model('report/product');
		
		$this->model_report_product->reset();
		
		$this->session->data['success'] = $this->language->get('text_success');
		
		$this->redirect($this->url->link('report/product_viewed', 'token=' . $this->session->data['token'], 'SSL'));
	}
}
?>