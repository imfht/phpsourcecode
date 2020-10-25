<?php
class ControllerExtensionTool extends Controller {
	public function index() {
		$this->load_language('extension/tool');
		 
		$this->document->setTitle($this->language->get('heading_title')); 

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/tool', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->session->data['error'])) {
			$this->data['error'] = $this->session->data['error'];
		
			unset($this->session->data['error']);
		} else {
			$this->data['error'] = '';
		}

		$this->load->model('setting/extension');

		$this->data['extensions'] = array();
						
		$files = glob(DIR_APPLICATION . 'controller/toolset/*.php');
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				
				if($this->startsWith($extension,'_')==1)
					continue;
				
				$this->load_language('toolset/' . $extension);

				$action = array();
				
				$action[] = array(
					'text' => $this->language->get('text_edit'),
					'href' => $this->url->link('toolset/' . $extension . '', 'token=' . $this->session->data['token'], 'SSL')
				);
				
				$this->data['extensions'][] = array(
					'name'   => $this->language->get('heading_title'),
					'description'   => $this->language->get('text_description'),
					'action' => $action
				);
			}
		}

		$this->template = 'extension/tool.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}

	
}
?>