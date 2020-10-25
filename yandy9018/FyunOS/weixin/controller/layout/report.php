<?php
class ControllerLayoutReport extends Controller {
	// prepare for layout and other element
	protected function init() {
		$this->load_language('help/guide');
		if(isset($this->request->get['route']))
			$this->document->setGuide($this->language->get('guide_'.$this->request->get['route'])!='guide_'.$this->request->get['route'] ? $this->language->get('guide_'.$this->request->get['route']) :'');

		$this->children = array(
			'common/header',
			'common/footer',
		);
	}

	protected function before($data=array()) {
		$this->document->setBreadcrumbs($data['breadcrumbs']);

		$this->load_language('common/header');
		
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
	}

	// excute the core function
	protected function excute() {

		$this->data['title'] = $this->document->getTitle();
		$this->data['description'] = $this->document->getDescription();
		$this->data['guide'] = $this->document->getGuide();

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['base'] = HTTPS_SERVER;
		} else {
			$this->data['base'] = HTTP_SERVER;
		}

		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		$this->data['links'] = $this->document->getLinks();
		$this->data['styles'] = $this->document->getStyles();
		$this->data['scripts'] = $this->document->getScripts();
		$this->data['lang'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');

		$this->data['breadcrumbs'] = $this->document->getBreadcrumbs();
		$this->template = 'layout/report.tpl';

		$this->render();
	}

	// do sth like clare or other things
	protected function after($data=array()) {

	}
}
?>