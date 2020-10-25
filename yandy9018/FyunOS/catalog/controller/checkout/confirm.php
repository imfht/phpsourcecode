<?php 
class ControllerCheckoutConfirm extends Controller { 
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
		
	  if ((!$this->cart->hasProducts() && (!isset($this->session->data['vouchers']) || !$this->session->data['vouchers'])) || !$this->cart->hasStock()) {
	  $this->redirect($this->url->link('checkout/cart'));
	  }
	
	  $json['captcha']="A";
	  
	  if(isset($this->request->post['codeRand'])){
		  if($this->verification($this->request->post['codeRand'])){
			   $json['captcha']="A";
			  }else{
				  $json['captcha']="N";  
				  }
		  }
	 
				  
	  if ($json['captcha']=="A") {
		 $json =  array();
	      
			$this->load_language('checkout/success');
			$this->document->setTitle($this->language->get('heading_title'));
			$data = array();   
			$data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$data['store_id'] = $this->config->get('config_store_id');
			$data['store_name'] = $this->config->get('config_name');

			if ($data['store_id']) {
				$data['store_url'] = $this->config->get('config_url');
				$json['store_url'] = $data['store_url'];		
			} else {
				$data['store_url'] = HTTP_SERVER;	
			}
			
			
			$data['customer_id'] = $this->customer->getId();
			$data['customer_group_id'] = $this->customer->getCustomerGroupId();
			
			$data['firstname'] = $this->request->post['firstname'];
			$data['telephone'] = $this->request->post['telephone'];
			$data['type'] = $this->request->post['type'];
			
			$data['seat'] = 0;
			
		    if($this->request->post['type']==1){
			    $data['seat'] = $this->request->post['seat'];
				$data['address'] = 0;
				$data['zone_id'] = 0;
				$data['city_id'] = 0;
				$this->data['text_server'] = '服务员';
			}
			
		    if($this->request->post['type']==2){
				$data['address'] = $this->request->post['address'];
				$data['zone_id'] = $this->request->post['zone_id'];
				$data['city_id'] = $this->request->post['city_id'];
				$this->data['text_server'] = '配送员';
		    }
			
			$this->load->model('account/address');
			$this->load->model('shipping/weight');
			
			
			$this->tax->setZone($data['zone_id'], $data['city_id']);

				$quote_data = array();
	            $this->load->model('shipping/weight');
							
				$quote = $this->model_shipping_weight->getQuote($data['zone_id'], $data['city_id']);
				if ($quote) {
					$quote_data = array(
						  'title'          => $quote['title'],
						  'quote'          => $quote['quote'], 
						  'description'    => $quote['description'],
						  'sort_order'     => $quote['sort_order'],
						  'error'          => $quote['error']
					);
					 $geo_zone = $this->model_shipping_weight->getGeoZoneByCityId1($data['zone_id'],$data['city_id']);
					 $this->session->data['shipping_method'] = $quote_data['quote']['weight_'.$geo_zone['geo_zone_id']];
				}else{
					 unset($this->session->data['shipping_method']);
					}

             


			$total_data = array();
			$total = 0;
			$taxes = $this->cart->getTaxes();
			 
			$this->load->model('setting/extension');
			
			$sort_order = array(); 
			
			$results = $this->model_setting_extension->getExtensions('total');
			
			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}
			
			array_multisort($sort_order, SORT_ASC, $results);
			
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);
		
					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
			}
			
			$sort_order = array(); 
		  
			foreach ($total_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}
	
			array_multisort($sort_order, SORT_ASC, $total_data);

			$product_data = array();
		
			foreach ($this->cart->getProducts() as $product) {

				$product_data[] = array(
					'product_id' => $product['product_id'],
					'href' 		=> $this->url->link('product/product', '&product_id=' . $product['product_id']),
					'name'       => $product['name'],
					'model'      => $product['model'],
					'quantity'   => $product['quantity'],
					'quantity1'   => $product['quantity1'],
					'subtract'   => $product['subtract'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'tax'        => $this->tax->getRate($product['tax_class_id'])
				); 
			}
			
			// Gift Voucher
			if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$product_data[] = array(
						'product_id' => 0,
						'href' 		=>'#',
						'name'       => $voucher['description'],
						'model'      => '',
						'option'     => array(),
						'download'   => array(),
						'quantity'   => 1,
						'subtract'   => false,
						'price'      => $voucher['amount'],
						'total'      => $voucher['amount'],
						'tax'        => 0
					);
				}
			}
						
			$data['products'] = $product_data;
			$data['totals'] = $total_data;
			if(isset($this->session->data['comment'])){
				$data['comment'] = $this->session->data['comment'];
			}else{
				$data['comment'] = '';
			}
			$data['total'] = $total;
			$data['reward'] = $this->cart->getTotalRewardPoints();

			$data['language_id'] = $this->config->get('config_language_id');
			$data['currency_id'] = $this->currency->getId();
			$data['currency_code'] = $this->currency->getCode();
			$data['currency_value'] = $this->currency->getValue($this->currency->getCode());
			$data['ip'] = $this->request->server['REMOTE_ADDR'];
			
			$this->load->model('checkout/order');
			$this->load->model('account/customer');
			
          
  			$this->session->data['order_id'] = $this->model_checkout_order->create($data);
			 $this->model_account_customer->getCustomerByTelephone($data['telephone']);
			$this->data['order_id'] = $this->session->data['order_id'];
  		    $this->model_checkout_order->updateOrderStatus($this->session->data['order_id'],$this->config->get('config_order_status_id'));
			// Gift Voucher
			if (isset($this->session->data['vouchers']) && is_array($this->session->data['vouchers'])) {
				$this->load->model('checkout/voucher');

				foreach ($this->session->data['vouchers'] as $voucher) {
					$this->model_checkout_voucher->addVoucher($this->session->data['order_id'], $voucher);
				}
			}
			

	        	
			$this->data['products'] = $product_data;
	
	    		// Gift Voucher
			$this->data['vouchers'] = array();
			
			if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$this->data['vouchers'][] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'])
					);
				}
			}  

		    $this->children = array(
				'common/header',
				'common/nav'
			);
			
			$this->data['totals'] = $total_data;
			$this->data['total'] = $total;
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/confirm.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/checkout/confirm.tpl';
			} else {
				$this->template = 'default/template/checkout/confirm.tpl';
			}
			
		$this->model_checkout_order->confirm($this->session->data['order_id'],$this->config->get('config_order_status_id'));

        //云巴实时后台提醒====================================
        if ($this->config->get('config_push_key')) {
        	require_once(DIR_SYSTEM . 'library/yunba.php');
	        //构造实例
			$yunba = new Yunba(array(
				"appkey" => $this->config->get('config_push_key')
			));

			//初始化
			$yunba->init();

			//连接
			$yunba->connect();
			sleep(1);

			//发布消息到topic1
			$yunba->publish(array(
				"topic" => "order_id",
				"qos" => 2,
				"msg" => $this->session->data['order_id']	
			));


        }

				
		
		 
		//微信云提醒=================================================================
        if ($this->config->get('config_weixin_notice')==1) {
	  	    $openids = $this->model_checkout_order->getOpenid();
	  	    if($data['type']==1){
				$content = "店内下单";
			}
			if ($data['type']==2) {
				$content = "外卖配送";
			}
			$sendweixin = new Sendweixin();
			$sendweixin->openids = serialize($openids);
			$sendweixin->title = "订单：#".$this->session->data['order_id'];
			$sendweixin->site_url = $this->config->get('config_url')."weixin/index.php?route=sale/order/info&order_id=".$this->session->data['order_id'];
			$sendweixin->content = $content;
			$sendweixin->type = 2;
			$sendweixin->send_weixin();
		}

       	//微信云提醒=================================================================


		 if($this->config->get('config_sms_notice')==1){
			  $content = "新订单：".$this->session->data['order_id'];
			  $sms = new Sms();
			  $sms->url = $this->config->get('config_sms_url');
			  $sms->uid = $this->config->get('config_sms_ac');
			  $sms->mobile = $this->config->get('config_sms_notice_mobile');
			  $sms->pwd = $this->config->get('config_sms_authkey');
			  $sms->content = $content;
			  $sms->sendSMS();
		 }


        $json['order_id'] = $this->session->data['order_id'];
		$this->data['entry_home'] = $this->language->get('entry_home');
		$this->data['entry_category'] = $this->language->get('entry_category');
		
		$this->data['text_success'] = $this->language->get('text_success');
		$this->data['text_notice'] = $this->language->get('text_notice');
		
		$this->data['home_links'] = $this->url->link('common/home');
		$this->data['category_links'] = $this->url->link('product/category&path=0');
		$json['output'] = $this->render();
		
  	}


  	


	    $this->load->library('json');
		$this->response->setOutput(Json::encode($json));	
}
	public function verification($code) {
		//$code = $this->request->get['codeRand'];
		
			if(md5($code) == $this->session->data['captcha']){
			return true;
			}else{
				return false;
				}
	}

}
?>