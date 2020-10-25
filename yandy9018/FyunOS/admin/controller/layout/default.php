<?php
class ControllerLayoutDefault extends Controller {
	// prepare for layout and other element 
	protected function init() {
		$this->load_language('help/guide');
		if (!$this->user->isLogged() || !isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
			$this->data['logged'] = '';
		}else{
			$this->data['logged'] = 1;
		}
		if(isset($this->request->get['route']))
			$this->document->setGuide($this->language->get('guide_'.$this->request->get['route'])!='guide_'.$this->request->get['route'] ? $this->language->get('guide_'.$this->request->get['route']) :'');
		$this->children = array(
			'common/header',
			'common/footer',
		);
	}
	
	protected function before($data=array()) {
		if(isset($data['breadcrumbs']))
			$this->document->setBreadcrumbs($data['breadcrumbs']);
	}
	
	// excute the core function 
	protected function excute() {
		$this->load_language('help/guide');
		
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

		$this->data['dateto'] = $this->config->get('config_dateinfo');
		
		if(isset($this->request->get['token'])){
			$this->data['token'] = $this->request->get['token'];
			}
		$this->data['breadcrumbs'] = $this->document->getBreadcrumbs();
		$this->template = 'layout/default.tpl';
		
		$this->render();
	}
	
	// do sth like clare or other things
	protected function after($data=array()) {
		
	}
}
?>