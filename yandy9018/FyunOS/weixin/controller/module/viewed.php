<?php
class ControllerModuleViewed extends Controller {
	private $error = array(); 
	
    private $product_layout_id = 0;
        
	public function index() {   
		$this->load_language('module/viewed');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
        $this->load->model('design/layout');
				
        $layouts = $this->model_design_layout->getLayouts();
    
        $product_layout_name = "";
    
        foreach ($layouts as $layout) {
            $routes = $this->model_design_layout->getLayoutRoutes($layout['layout_id']);
            
            foreach ($routes as $route) {
                if ($route['route'] == 'product/product') {
                    $this->product_layout_id = $layout['layout_id'];
                    $product_layout_name = $layout['name'];
                    break;
                }
            }
            
            if ($this->product_layout_id) {
                break;
            }
        }
        
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {			
			$this->model_setting_setting->editSetting('viewed', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		
        $this->data['product_layout_name'] = $product_layout_name;
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}
        
        if (isset($this->error['layout'])) {
			$this->data['error_layout'] = $this->error['layout'];
		} else {
			$this->data['error_layout'] = array();
		}
				
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/viewed', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/viewed', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];
	
        if (isset($this->request->post['viewed_count'])) {
			$this->data['viewed_count'] = $this->request->post['viewed_count'];
		} else {
			$this->data['viewed_count'] = $this->config->get('viewed_count');
		}	
				    
        $this->data['viewed_count'] = isset($this->data['viewed_count']) && !is_null($this->data['viewed_count']) ? $this->data['viewed_count'] : 5;
        
        if (isset($this->request->post['show_on_product'])) {
			$this->data['show_on_product'] = $this->request->post['show_on_product'];
		} else {
			$this->data['show_on_product'] = $this->config->get('show_on_product');
		}	
		
        $this->data['show_on_product'] = isset($this->data['show_on_product']) && !is_null($this->data['show_on_product']) ? $this->data['show_on_product'] : 1;
        
		$this->data['modules'] = array();
		
		if (isset($this->request->post['viewed_module'])) {
			$this->data['modules'] = $this->request->post['viewed_module'];
		} elseif ($this->config->get('viewed_module')) { 
			$this->data['modules'] = $this->config->get('viewed_module');
		}		
		
        if ($this->product_layout_id && count($this->data['modules']) == 0) {
            $this->data['modules'][] = array(
                'image_width' => 80,
                'image_height' => 80,
                'layout_id' => $this->product_layout_id,
                'position' => 'content_bottom',
                'status' => 1,
                'sort_order' => ''   
            );
        }
        
		$this->data['layouts'] = $layouts;
        
		$this->template = 'module/viewed.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/viewed')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (isset($this->request->post['viewed_module'])) {
            
            $isset_product_layout = false;
            $isset_other_layout = false;
            
			foreach ($this->request->post['viewed_module'] as $key => $value) {
                if ($value['layout_id'] == $this->product_layout_id && $value['status'] == 1) {
                    $isset_product_layout = true;
                } else if ($value['layout_id'] != $this->product_layout_id && $value['status'] == 1) {
                    $isset_other_layout = true;
                }
				if (!$value['image_width'] || !$value['image_height']) {
					$this->error['image'][$key] = $this->language->get('error_image');
				}
			}
            
            if (!$isset_product_layout && $isset_other_layout) {
                $this->error['layout'] = $this->language->get('error_layout');
            }
		}
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>