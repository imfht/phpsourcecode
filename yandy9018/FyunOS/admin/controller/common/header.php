<?php 
class ControllerCommonHeader extends Controller {
	protected function index() {
		$this->load_language('common/header');
		
   		if (!$this->user->isLogged() || !isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
			$this->data['logged'] = '';
			
			$this->data['home'] = $this->url->link('common/login', '', 'SSL');
		} else {	
		    $this->data['token'] = $this->session->data['token'];
			$this->data['logged'] = sprintf($this->language->get('text_logged'), $this->user->getUserName());
			$this->data['sitemap'] =$this->url->link('catalog/sitemap', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['home'] = $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['seo'] =$this->url->link('seo/url_alias', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['affiliate'] = $this->url->link('sale/affiliate', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['attribute'] = $this->url->link('catalog/attribute', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['nav'] = $this->url->link('catalog/nav', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['attribute_group'] = $this->url->link('catalog/attribute_group', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['backup'] = $this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['banner'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['category'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['store_status'] = $this->config->get('config_store_status');
			$this->data['store_url'] = $this->config->get('config_url');
			$this->data['push_key'] = $this->config->get('config_push_key');
			$this->data['config_name'] = $this->config->get('config_name');
			$this->data['coupon'] = $this->url->link('sale/coupon', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['customer'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['customer_group'] = $this->url->link('sale/customer_group', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['download'] = $this->url->link('catalog/download', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['error_log'] = $this->url->link('tool/error_log', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['feed'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');	
			$this->data['media'] = $this->url->link('common/filemanager/manager', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['tool'] = $this->url->link('extension/tool', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['stores'] = array();
			
			$this->load->model('setting/store');
			
			$results = $this->model_setting_store->getStores();
			
			foreach ($results as $result) {
				$this->data['stores'][] = array(
					'name' => $result['name'],
					'href' => $result['url']
				);
			}
			$this->load->model('sale/order');
			$data['filter_order_status_id']=1;
			$data['start'] = 0;
			$data['limit'] = 15;
			$this->data['newOrdersCount'] = $this->model_sale_order->getTotalOrders($data);
			$newOrders = $this->model_sale_order->getOrders($data);
			foreach ($newOrders as $newOrder) {
				$this->data['new_orders'][] = array(
					  'order_id'        => $newOrder['order_id'],
					  'addtime'	     => $this->timec(strtotime($newOrder['date_added'])),
					  'href'	     => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $newOrder['order_id']),
		            );
					
			}
			$this->data['total'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['information'] = $this->url->link('catalog/information', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['user'] = $this->url->link('user/user', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['user_group'] = $this->url->link('user/user_permission', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['layout'] = $this->url->link('design/layout', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['logout'] = $this->url->link('common/logout', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['contact'] = $this->url->link('sale/contact', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['manufacturer'] = $this->url->link('catalog/manufacturer', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['module'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['option'] = $this->url->link('catalog/option', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['order'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['payment'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['product'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['review'] = $this->url->link('catalog/review', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['return'] = $this->url->link('sale/return', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['shipping'] = $this->url->link('shipping/weight', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['setting'] = $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['parameter'] = $this->url->link('setting/parameter', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['voucher'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['voucher_theme'] = $this->url->link('sale/voucher_theme', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['message'] = $this->url->link('catalog/message', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['report'] = $this->url->link('report/sale', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['store'] = HTTP_CATALOG;
			$this->data['report_sale_order'] = $this->url->link('report/sale_order', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['report_sale_tax'] = $this->url->link('report/sale_tax', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['report_sale_shipping'] = $this->url->link('report/sale_shipping', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['report_sale_return'] = $this->url->link('report/sale_return', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['report_sale_coupon'] = $this->url->link('report/sale_coupon', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['report_product_viewed'] = $this->url->link('report/product_viewed', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['report_product_purchased'] = $this->url->link('report/product_purchased', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['report_customer_order'] = $this->url->link('report/customer_order', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['report_customer_reward'] = $this->url->link('report/customer_reward', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['report_customer_credit'] = $this->url->link('report/customer_credit', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['report_affiliate_commission'] = $this->url->link('report/affiliate_commission', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['logistics'] = $this->url->link('localisation/logistics', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['unit'] = $this->url->link('localisation/unit', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['custom'] = $this->url->link('setting/custom', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['server'] = $this->url->link('setting/server', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['mail'] = $this->url->link('setting/mail', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['wechat'] = $this->url->link('setting/wechat', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['sms'] = $this->url->link('setting/sms', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['currency'] = $this->url->link('localisation/currency', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['language'] = $this->url->link('localisation/language', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['geo_zone'] = $this->url->link('localisation/geo_zone', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['stock_status'] = $this->url->link('localisation/stock_status', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['tax_class'] = $this->url->link('localisation/tax_class', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['total'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['order_status'] = $this->url->link('localisation/order_status', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['voucher'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['voucher_theme'] = $this->url->link('sale/voucher_theme', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['country'] = $this->url->link('localisation/country', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['zone'] = $this->url->link('localisation/zone', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['city'] = $this->url->link('localisation/city', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['return_action'] = $this->url->link('localisation/return_action', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['return_reason'] = $this->url->link('localisation/return_reason', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['return_status'] = $this->url->link('localisation/return_status', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['all_seo'] = $this->url->link('sale/auto_seo', 'token=' . $this->session->data['token'], 'SSL');

			
		}
		$this->id = 'header';
		$this->template = 'common/header.tpl';
		
		$this->render();
	}
	
	public function timec($timeInt,$format='m月d日'){
	if(empty($timeInt)||!is_numeric($timeInt)||!$timeInt){
		return '';
	}
	$d=time()+28800-$timeInt;
	if($d<0){
		return '1';
	}else{
		if($d<60){
			return $d.'秒前';
		}else{
			if($d<3600){
				return floor($d/60).'分钟前';
			}else{
				if($d<86400){
					return floor($d/3600).'小时前';
				}else{
					if($d<259200){//3天内
						return floor($d/86400).'天前';
					}else{
						return date($format,$timeInt);
					}
				}
			}
		}
	}
}	
}
?>