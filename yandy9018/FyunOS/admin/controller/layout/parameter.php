<?php
class ControllerLayoutParameter extends Controller {
	// prepare for layout and other element
	protected function init() {
		$this->load_language('help/guide');

		if(isset($this->request->get['route']))
			$this->document->setGuide($this->language->get('guide_'.$this->request->get['route'])!='guide_'.$this->request->get['route'] ? $this->language->get('guide_'.$this->request->get['route']) :'');
		$this->children = array(
			'common/header',
			'common/footer',
		);
		$this->data['dateto'] = $this->config->get('config_dateinfo');
	}

	protected function before($data=array()) {
		$this->document->setBreadcrumbs($data['breadcrumbs']);

		$this->load_language('common/header');
		
		$this->data['logistics'] = $this->url->link('localisation/logistics', 'token=' . $this->session->data['token'], 'SSL');
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
		$this->template = 'layout/parameter.tpl';

		$this->render();
	}

	// do sth like clare or other things
	protected function after($data=array()) {

	}
}
?>