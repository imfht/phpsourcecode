<?php 
class ControllerToolBackup extends Controller { 
	private $error = array();
	
	public function index() {		
		$this->load_language('tool/backup');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('tool/backup');
				
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			if (is_uploaded_file($this->request->files['import']['tmp_name'])) {
				$content = file_get_contents($this->request->files['import']['tmp_name']);
			} else {
				$content = false;
			}
			
			if ($content) {
				$this->model_tool_backup->restore($content);
				
				$this->session->data['success'] = $this->language->get('text_success');
				
				$this->redirect($this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'));
			} else {
				$this->error['warning'] = $this->language->get('error_empty');
			}
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),     		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
		
		$this->data['restore'] = $this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['backup'] = $this->url->link('tool/backup/backup', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cache'] = $this->url->link('tool/backup/clearcache', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['test'] = $this->url->link('tool/backup/cleartestdata', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('tool/backup');
			
		$this->data['tables'] = $this->model_tool_backup->getTables();

		$this->template = 'tool/backup.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}
	
	public function backup() {
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename=backup.sql');
			$this->response->addheader('Content-Transfer-Encoding: binary');
			
			$this->load->model('tool/backup');
			
			$this->response->setOutput($this->model_tool_backup->backup($this->request->post['backup']));
		} else {
			return $this->forward('error/permission');
		}
	}
	
	public function clearCache() {
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->load_language('tool/backup');
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->cache->delete('product');
			$this->cache->delete('category');
			$this->cache->delete('information');
			$this->cache->delete('city');
			$this->cache->delete('country');
			$this->cache->delete('currency');
			$this->cache->delete('geo_zone');
			$this->cache->delete('language');
			$this->cache->delete('length_class');
			$this->cache->delete('logistics');
			$this->cache->delete('order_status');
			$this->cache->delete('return_action');
			$this->cache->delete('return_reason');
			$this->cache->delete('return_status');
			$this->cache->delete('stock_status');
			$this->cache->delete('tax_class');
			$this->cache->delete('weight_class');
			$this->cache->delete('zone');
			$this->cache->delete('manufacturer');
			$this->cache->delete('store');
			
			$this->redirect($this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'));
		} else {
			return $this->forward('error/permission');
		}
	}
	
	public function clearTestData() {
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename=backup.sql');
			$this->response->addheader('Content-Transfer-Encoding: binary');
	
			$this->load->model('tool/backup');
	
			$this->response->setOutput($this->model_tool_backup->backup($this->request->post['backup']));
		} else {
			return $this->forward('error/permission');
		}
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'tool/backup')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}		
	}
}
?>