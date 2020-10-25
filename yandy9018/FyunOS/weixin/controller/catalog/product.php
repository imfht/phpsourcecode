<?php
class ControllerCatalogProduct extends Controller {
	private $error = array();

  	public function index() {
  		
		$this->load_language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');
		
		$this->getList();
  	}

  	public function insert() {
    	$this->load_language('catalog/product');

    	$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_product->addProduct($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = $this->filter();
			
			$this->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}

    	$this->getForm();
  	}

  	public function update() {
    	$this->load_language('catalog/product');

    	$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_product->editProduct($this->request->get['product_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = $this->filter();

			$this->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

    	$this->getForm();
  	}

  	public function delete() {
    	$this->load_language('catalog/product');

    	$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_product->deleteProduct($product_id);
	  		}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = $this->filter();
			
			$this->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

    	$this->getList();
  	}
  	
  	public function changeStatus() {
    	$this->load_language('catalog/product');

    	$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected']) ) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_product->updateProductStatus($product_id,$this->request->get['status']);
	  		}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = $this->filter();
			
			$this->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

    	$this->getList();
  	}


  	public function copy() {
    	$this->load_language('catalog/product');

    	$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_product->copyProduct($product_id);
	  		}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = $this->filter();

			$this->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

    	$this->getList();
  	}

  	private function filter($url='') {
  		$requestes=array(
  			'filter_name' => 'filter_name',
  			'filter_model' => 'filter_model',
  			'filter_sku' =>   'filter_sku',
  			'filter_price' => 'filter_price',
  			'filter_quantity' => 'filter_quantity',
  			'filter_status' => 'filter_status',
  			'sort' => 'sort',
  			'order' => 'order',
  			'page' => 'page',
  			'filter_category_id' => 'filter_category_id'
  		);
  			
  		foreach ($requestes as $key => $value) {
  			if (isset($this->request->get[$key])) {
  				$url .= '&'.$key.'=' . $this->request->get[$value];
  			}
  		}
  		
  		return $url;
  	}
  	
  	private function getList() {
  		$requestes=array(
  		  	'filter_name' => null,
  		  	'filter_model' => null,
  			'filter_sku' => null,
  		  	'filter_price' => null,
  		  	'filter_quantity' => null,
  			'filter_category_id' => null,
  		  	'filter_status' => null,
  		  	'sort' => 'pd.name',
  		  	'order' => 'ASC',
  		  	'page' => 1
  		);
  			
  		foreach ($requestes as $key => $value) {
  			if (isset($this->request->get[$key])) {
  				$$key = $this->request->get[$key];
  			} else {
  				$$key = $value;
  			}
  		}

		$url = $this->filter();

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);

		$this->data['insert'] = $this->url->link('catalog/product/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['copy'] = $this->url->link('catalog/product/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('catalog/product/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['enabled'] = $this->url->link('catalog/product/changeStatus', 'status=1&token=' . $this->session->data['token'], 'SSL');
		$this->data['disabled'] = $this->url->link('catalog/product/changeStatus', 'status=0&token=' . $this->session->data['token'], 'SSL');
		
		
		$this->data['products'] = array();

		$data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_sku'	  => $filter_sku,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
			'filter_category_id' =>$filter_category_id,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'           => $this->config->get('config_admin_limit')
		);

		$this->load->model('tool/image');

		$product_total = $this->model_catalog_product->getTotalProducts($data);

		$results = $this->model_catalog_product->getProducts($data);
		foreach ($results as $result) {
			$action = array();

			$action[] = array(
					'text' => $this->language->get('text_edit'),
					'href' => $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, 'SSL')
			);
			
			$preview=array(
				'text' => $this->language->get('text_preview'),
					'href' => HTTP_CATALOG.'index.php?route=product/product&product_id='. $result['product_id']
			);

			if ($result['image']) {
				$image = $result['image'].'!40list';
			} else {
				$image = "http://fyunimage.b0.upaiyun.com/no_image.jpg!40list";
			}

			$product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);

			if ($product_specials) {
				$special = reset($product_specials);
				if(($special['date_start'] != '0000-00-00' && $special['date_start'] > date('Y-m-d')) || ($special['date_end'] != '0000-00-00' && $special['date_end'] < date('Y-m-d'))) {
					$special = FALSE;
				}
			} else {
				$special = FALSE;
			}
			
			
	
			
		
			$this->load->model('localisation/unit');
			$units = $this->model_localisation_unit->getUnit($result['unit_id']);
			$unit_id = $units['name'];
				
			

			$this->data['products'][$result['product_id']] = array(
				'product_id' => $result['product_id'],
				'name'       => $result['name'],
				'price'      => round($result['price'],2)."/".$unit_id,
				'special'    => $special['price'],
				'image'      => $image,
				'quantity'   => $result['quantity'],
				'status'     => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'selected'   => isset($this->request->post['selected']) && in_array($result['product_id'], $this->request->post['selected']),
				'preview'     => $preview,
				'action'     => $action
				 
			);
			
		}

    	$this->data['token'] = $this->session->data['token'];

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

		$url = $this->filter();

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}
		
		$this->data['sort_name'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=pd.name' . $url, 'SSL');
		$this->data['sort_model'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.model' . $url, 'SSL');
		$this->data['sort_sku'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.sku' . $url, 'SSL');
		$this->data['sort_price'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.price' . $url, 'SSL');
		$this->data['sort_quantity'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.status' . $url, 'SSL');
		$this->data['sort_order'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, 'SSL');
		$this->data['sort_category_id'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&sort=p.category_id' . $url, 'SSL');
		$url = '';

		$url = $this->filter();

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();

		$this->data['filter_name'] = $filter_name;
		$this->data['filter_model'] = $filter_model;
		$this->data['filter_sku'] = $filter_sku;
		$this->data['filter_price'] = $filter_price;
		$this->data['filter_quantity'] = $filter_quantity;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_category_id'] = $filter_category_id;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'catalog/product_list.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
  	}

  	private function product_setter($product_info,$key,$default=''){
  		if (isset($this->request->post[$key])) {
  			$this->data[$key] = $this->request->post[$key];
  		} elseif (isset($product_info)) {
  			$this->data[$key] = $product_info[$key];
  		} else {
  			$this->data[$key] = $default;
  		}
  	}
  	
  	private function getForm() {
  		$errores=array(
  			'warning' => '',
  			'name' => array(),
  			'meta_description' =>array(),
	  		'description' =>array(), 
	  		'model' => '',
  			'date_available' => ''
  		);
  		
  		$err_flag='error_';
  		foreach ($errores as $key => $value) {
	  		if (isset($this->error[$key])) {
				$this->data[$err_flag.$key] = $this->error[$key];
			} else {
				$this->data[$err_flag.$key] = $value;
			}
  		}
  		
		$url = $this->filter();

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => false
   		);

		if (!isset($this->request->get['product_id'])) {
			$this->data['action'] = $this->url->link('catalog/product/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
			$this->data['breadcrumbs'][] = array(
			    'text'      => $this->language->get('text_add'),
				'href'      => $this->data['action'],
			    'separator' => $this->language->get('text_breadcrumb_separator')
			);
		} else {
			$this->data['action'] = $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $this->request->get['product_id'] . $url, 'SSL');
			$this->data['breadcrumbs'][] = array(
			    'text'      => $this->language->get('text_edit'),
				'href'      => $this->data['action'],
			    'separator' => $this->language->get('text_breadcrumb_separator')
			);
		}

		$this->data['cancel'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
    	}

		$this->load->model('localisation/language');

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['product_description'])) {
			$this->data['product_description'] = $this->request->post['product_description'];
		} elseif (isset($product_info)) {
			$this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
		} else {
			$this->data['product_description'] = array();
		}

		$this->product_setter(isset($product_info)?$product_info : NULL, 'model');
		$this->product_setter(isset($product_info)?$product_info : NULL, 'sku');
		$this->product_setter(isset($product_info)?$product_info : NULL, 'upc');
		$this->product_setter(isset($product_info)?$product_info : NULL, 'location');
		
		$this->load->model('setting/store');

		$this->data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['product_store'])) {
			$this->data['product_store'] = $this->request->post['product_store'];
		} elseif (isset($product_info)) {
			$this->data['product_store'] = $this->model_catalog_product->getProductStores($this->request->get['product_id']);
		} else {
			$this->data['product_store'] = array(0);
		}

		$this->product_setter(isset($product_info)?$product_info : NULL, 'keyword');
		
		if (isset($this->request->post['tag'])) {
      		$this->data['tag'] = $this->request->post['tag'];
    	} else if (isset($product_info)) {
      		$this->data['tag'] = $product_info['tag'];
    	} else {
			$this->data['tag'] = '';
		}
		
		if (isset($this->request->post['category_id'])) {
      		$this->data['category_id'] = $this->request->post['category_id'];
    	} else if (isset($product_info)) {
      		$this->data['category_id'] = $product_info['category_id'];
    	} else {
			$this->data['category_id'] = '';
		}

		$this->product_setter(isset($product_info)?$product_info : NULL, 'image');
		
		$this->load->model('tool/image');

		if (isset($product_info) && $product_info['image']) {
			$this->data['preview'] = $product_info['image'].'!list';
		} else {
			$this->data['preview'] = "http://fyunimage.b0.upaiyun.com/no_image.jpg!list";
		}

		$this->load->model('catalog/manufacturer');

    	$this->data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();

    	$this->product_setter(isset($product_info)?$product_info : NULL, 'manufacturer_id',0);
    	$this->product_setter(isset($product_info)?$product_info : NULL, 'shipping',1);
    	
		if (isset($this->request->post['date_available'])) {
       		$this->data['date_available'] = $this->request->post['date_available'];
		} elseif (isset($product_info)) {
			$this->data['date_available'] = date('Y-m-d', strtotime($product_info['date_available']));
		} else {
			$this->data['date_available'] = date('Y-m-d', time() - 86400);
		}
		
		$this->product_setter(isset($product_info)?$product_info : NULL, 'quantity',1);
		$this->product_setter(isset($product_info)?$product_info : NULL, 'minimum',1);
		$this->product_setter(isset($product_info)?$product_info : NULL, 'subtract',1);
		$this->product_setter(isset($product_info)?$product_info : NULL, 'sort_order',1);
		
		$this->load->model('localisation/stock_status');

		$this->data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['stock_status_id'])) {
      		$this->data['stock_status_id'] = $this->request->post['stock_status_id'];
    	} else if (isset($product_info)) {
      		$this->data['stock_status_id'] = $product_info['stock_status_id'];
    	} else {
			$this->data['stock_status_id'] = $this->config->get('config_stock_status_id');
		}
		
		
		
		$this->load->model('localisation/unit');

		$this->data['units'] = $this->model_localisation_unit->getUnits();

		if (isset($this->request->post['unit_id'])) {
      		$this->data['unit_id'] = $this->request->post['unit_id'];
    	} else if (isset($product_info)) {
      		$this->data['unit_id'] = $product_info['unit_id'];
    	} else {
			$this->data['unit_id'] = $this->config->get('config_unit_id');
		}

		$this->product_setter(isset($product_info)?$product_info : NULL, 'price');
    	$this->product_setter(isset($product_info)?$product_info : NULL, 'status',1);
    	
		$this->load->model('localisation/tax_class');

		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->product_setter(isset($product_info)?$product_info : NULL, 'tax_class_id',0);
	
		$this->product_setter(isset($product_info)?$product_info : NULL, 'weight');
    	
		$this->load->model('localisation/weight_class');

		$this->data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (isset($this->request->post['weight_class_id'])) {
      		$this->data['weight_class_id'] = $this->request->post['weight_class_id'];
    	} elseif (isset($product_info)) {
      		$this->data['weight_class_id'] = $product_info['weight_class_id'];
    	} elseif (isset($weight_info)) {
      		$this->data['weight_class_id'] = $this->config->get('config_weight_class_id');
		} else {
      		$this->data['weight_class_id'] = '';
    	}

    	$this->product_setter(isset($product_info)?$product_info : NULL, 'length');
    	$this->product_setter(isset($product_info)?$product_info : NULL, 'width');
    	$this->product_setter(isset($product_info)?$product_info : NULL, 'height');
    	
		$this->load->model('localisation/length_class');

		$this->data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		if (isset($this->request->post['length_class_id'])) {
      		$this->data['length_class_id'] = $this->request->post['length_class_id'];
    	} elseif (isset($product_info)) {
      		$this->data['length_class_id'] = $product_info['length_class_id'];
    	} elseif (isset($length_info)) {
      		$this->data['length_class_id'] = $this->config->get('config_length_class_id');
    	} else {
    		$this->data['length_class_id'] = '';
		}

		if (isset($this->request->post['product_attribute'])) {
			$this->data['product_attributes'] = $this->request->post['product_attribute'];
		} elseif (isset($product_info)) {
			$this->data['product_attributes'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);
		} else {
			$this->data['product_attributes'] = array();
		}

		if (isset($this->request->post['product_option'])) {
			$product_options = $this->request->post['product_option'];
		} elseif (isset($product_info)) {
			$product_options = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);
		} else {
			$product_options = array();
		}

		$this->data['product_options'] = array();

		foreach ($product_options as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' ||$product_option['type'] == 'virtual_product' ||$product_option['type'] == 'color'  ) {
				$product_option_value_data = array();

				foreach ($product_option['product_option_value'] as $product_option_value) {
					$product_option_value_data[] =$product_option_value;
				}
				
				$this->data['product_options'][] = array(
					'product_option_id'    => $product_option['product_option_id'],
					'option_id'            => $product_option['option_id'],
					'name'                 => $product_option['name'],
					'type'                 => $product_option['type'],
					'product_option_value' => $product_option_value_data,
					'required'             => $product_option['required']
				);
			} else {
				$this->data['product_options'][] = $product_option;
			}
		}
		
		$this->load->model('sale/customer_group');

		$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();

		if (isset($this->request->post['product_discount'])) {
			$this->data['product_discounts'] = $this->request->post['product_discount'];
		} elseif (isset($product_info)) {
			$this->data['product_discounts'] = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);
		} else {
			$this->data['product_discounts'] = array();
		}

		if (isset($this->request->post['product_special'])) {
			$this->data['product_specials'] = $this->request->post['product_special'];
		} elseif (isset($product_info)) {
			$this->data['product_specials'] = $this->model_catalog_product->getProductSpecials($this->request->get['product_id']);
		} else {
			$this->data['product_specials'] = array();
		}

		if (isset($this->request->post['product_image'])) {
			$product_images = $this->request->post['product_image'];
		} elseif (isset($product_info)) {
			$product_images = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
		} else {
			$product_images = array();
		}

		$this->data['product_images'] = array();

		foreach ($product_images as $product_image) {
			if ($product_image['image'] && file_exists(DIR_IMAGE . $product_image['image'])) {
				$image = $product_image['image'];
			} else {
				$image = 'no_image.jpg';
			}

			$this->data['product_images'][] = array(
				'image'   => $image,
				'preview' => $this->model_tool_image->resize($image, 100, 100)
			);
		}

		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

		$this->load->model('catalog/download');

		$this->data['downloads'] = $this->model_catalog_download->getDownloads();

		if (isset($this->request->post['product_download'])) {
			$this->data['product_download'] = $this->request->post['product_download'];
		} elseif (isset($product_info)) {
			$this->data['product_download'] = $this->model_catalog_product->getProductDownloads($this->request->get['product_id']);
		} else {
			$this->data['product_download'] = array();
		}

		$this->load->model('catalog/category');

		$this->data['categories'] = $this->model_catalog_category->getCategories(0);

		if (isset($this->request->post['product_category'])) {
			$this->data['product_category'] = $this->request->post['product_category'];
		} elseif (isset($product_info)) {
			$this->data['product_category'] = $this->model_catalog_product->getProductCategories($this->request->get['product_id']);
		} else {
			$this->data['product_category'] = array();
		}

		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} elseif (isset($product_info)) {
			$products = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
		} else {
			$products = array();
		}

		$this->data['product_related'] = array();

		foreach ($products as $product_id) {
			$related_info = $this->model_catalog_product->getProduct($product_id);

			if ($related_info) {
				$this->data['product_related'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}

   		$this->product_setter(isset($product_info)?$product_info : NULL, 'points');

		if (isset($this->request->post['product_reward'])) {
			$this->data['product_reward'] = $this->request->post['product_reward'];
		} elseif (isset($product_info)) {
			$this->data['product_reward'] = $this->model_catalog_product->getProductRewards($this->request->get['product_id']);
		} else {
			$this->data['product_reward'] = array();
		}

		if (isset($this->request->post['product_layout'])) {
			$this->data['product_layout'] = $this->request->post['product_layout'];
		} elseif (isset($product_info)) {
			$this->data['product_layout'] = $this->model_catalog_product->getProductLayouts($this->request->get['product_id']);
		} else {
			$this->data['product_layout'] = array();
		}

		$this->load->model('design/layout');
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->template = 'catalog/product_form.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
  	}

  	private function validateForm() {
  		$rules=$this->load->rule();
  		$this->load_language('error_msg');
    	
    	if (!$this->user->hasPermission('modify', 'catalog/product')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}

    	foreach ($this->request->post['product_description'] as $language_id => $value) {
      		if ((utf8_strlen(utf8_decode($value['name'])) < $rules['str_lenth']) ) {
        		$this->error['name'][$language_id] = $this->language->get('error_name');
      		}
    	}


		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

    	if (!$this->error) {
			return true;
    	} else {
      		return false;
    	}
  	}

  	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'catalog/product')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}

		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}

  	private function validateCopy() {
    	if (!$this->user->hasPermission('modify', 'catalog/product')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}

		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}

	public function option() {
		$output = '';

		$this->load->model('catalog/option');

		$results = $this->model_catalog_option->getOptionValues($this->request->get['option_id']);

		foreach ($results as $result) {
			$output .= '<option value="' . $result['option_value_id'] . '"';

			if (isset($this->request->get['option_value_id']) && ($this->request->get['option_value_id'] == $result['option_value_id'])) {
				$output .= ' selected="selected"';
			}

			$output .= '>' . $result['name'] . '</option>';
		}

		$this->response->setOutput($output);
	}

	public function autocomplete() {
		
		$json = array();
		
		if(isset($this->request->post['filter_name']))
			$this->request->get['filter_name']=$this->request->post['filter_name'];
			
		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model']) || isset($this->request->get['filter_sku'])  || isset($this->request->get['filter_category_id'])) {
			$this->load->model('catalog/product');
			
			$requestes=array(
			    'filter_name' => '',
			    'filter_model' => '',
				'filter_sku' => '',
			    'filter_category_id' => '',
			    'filter_sub_category' => '',
			    'limit' => 20
			  );
			  
			foreach ($requestes as $key => $value) {
			    if (isset($this->request->get[$key])&&$this->request->get[$key]!='') {
			      $$key = $this->request->get[$key];
			    } else {
			      $$key = $value;
			    }
			 }

			$data = array(
				'filter_name'         => $filter_name,
				'filter_model'        => $filter_model,
				'filter_sku'       		=> $filter_sku,
				'filter_category_id'  => $filter_category_id,
				'filter_sub_category' => $filter_sub_category,
				'start'               => 0,
				'limit'               => $limit
			);
			
			$results = $this->model_catalog_product->getProducts($data);
			
			foreach ($results as $result) {
				$option_data = array();
				
				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);	
				
				foreach ($product_options as $product_option) {
					if ($product_option['type'] == 'select' ||$product_option['type'] == 'virtual_product' ||$product_option['type'] == 'color' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
						$option_value_data = array();
					
						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_data[] = array(
								'product_option_value_id' => $product_option_value['product_option_value_id'],
								'option_value_id'         => $product_option_value['option_value_id'],
								'name'                    => $product_option_value['name'],
								'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
								'price_prefix'            => $product_option_value['price_prefix']
							);	
						}
					
						$option_data[] = array(
							'product_option_id' => $product_option['product_option_id'],
							'option_id'         => $product_option['option_id'],
							'name'              => $product_option['name'],
							'type'              => $product_option['type'],
							'option_value'      => $option_value_data,
							'required'          => $product_option['required']
						);	
					} else {
						$option_data[] = array(
							'product_option_id' => $product_option['product_option_id'],
							'option_id'         => $product_option['option_id'],
							'name'              => $product_option['name'],
							'type'              => $product_option['type'],
							'option_value'      => $product_option['option_value'],
							'required'          => $product_option['required']
						);				
					}
				}
				
				$json[] = array(
					'product_id' => $result['upid'],
					'name'       => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),	
					'model'      => $result['model'],
					'sku'      => $result['sku'],
					'option'     => $option_data,
					'price'      => $result['price']
				);	
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>