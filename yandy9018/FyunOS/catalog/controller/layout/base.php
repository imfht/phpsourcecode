<?php
class ControllerLayoutBase extends Controller {
	// prepare for layout and other element 
	protected function init() {
		$this->children = array(
					'common/column_left',
					'common/column_right',
					'common/content_top',
					'common/content_bottom',
					'common/footer',
					'common/header'
		);
	}
	
	// excute the core function 
	protected function excute() {
		$this->data['title'] = $this->document->getTitle();
		$this->data['description'] = $this->document->getDescription();

		if ((!isset($this->request->server['HTTPS'])) || ($this->request->server['HTTPS'] != 'on')) {
			$this->data['base'] = HTTP_SERVER;
		} else {
			$this->data['base'] = HTTPS_SERVER;
		}

		$this->data['charset'] = $this->language->get('charset');
		$this->data['language'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		$this->data['links'] = $this->document->getLinks();
		$this->data['styles'] = $this->document->getStyles();
		$this->data['scripts'] = $this->document->getScripts();

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/layout/index.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/layout/index.tpl';
		} else {
			$this->template = 'default/template/layout/index.tpl';
		}


		$this->render();
	}
	
	// do sth like clare or other things
	protected function after() {
		
	}
}
?>