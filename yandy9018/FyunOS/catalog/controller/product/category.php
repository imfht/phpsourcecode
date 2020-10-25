<?php 
class ControllerProductCategory extends Controller {  
	public function index() { 
	    $this->session->data['menu']="category";
		$this->data['storeType'] = $this->config->get('config_store_type');
		$this->load_language('product/category');
		
		$this->load->model('catalog/category');
		
		$this->load->model('catalog/product');
		
		
		
		//判断用户来自哪里
		if (!$this->customer->isLogged()) {
			if($this->config->get('config_bind_status')=='1'){
				if($this->config->get('config_phone_login')=='1'){
					$this->data['login'] = $this->url->link('account/login&phone=1', '', 'SSL');
				}else{
					$this->customer->weixinLogin();
					}
		}elseif($this->config->get('config_bind_status')=='2'){
				if(isset($this->request->get['openId'])){
					$this->session->data['open_id'] = $this->request->get['openId'];
					$this->data['login'] = $this->url->link('account/account', '', 'SSL');
					}else{
						$this->data['login'] = $this->url->link('account/login&ubind=1', '', 'SSL');
						}
				}else{
				$this->data['login'] = $this->url->link('account/login', '', 'SSL');
					}
		}else{
			$this->data['login'] = $this->url->link('account/login', '', 'SSL');
			}
		
		
		$this->load->model('tool/image'); 
		$this->data['account'] = $this->url->link('account/account&m=anleye');
		$this->data['categories'] = array();
		$this->document->addScript("catalog/view/javascript/fyun/slide/jquery.pageslide.min.js");	
		$this->document->addStyle("catalog/view/theme/diancan/stylesheet/jquery.pageslide.css");		
		$categories = $this->model_catalog_category->getCategories(0);
		
		foreach ($categories as $category) {
			if ($category['top']) {
				$children_data = array();
				
				$children = $this->model_catalog_category->getCategories($category['category_id']);
				
				foreach ($children as $child) {
					$data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true	
					);		
						
					//$product_total = $this->model_catalog_product->getTotalProducts($data);
									
					$children_data[] = array(
						'name'  => $child['name'] ,
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])	
					);					
				}
				
				// Level 1
				$this->data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'category_id'   => $category['category_id'],
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else { 
			$page = 1;
		}	
							
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_catalog_limit');
		}
					
		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
       		'separator' => false
   		);	
			
		if (isset($this->request->get['path'])) {
			$path = '';
		
			$parts = explode('_', (string)$this->request->get['path']);
		
			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}
									
				$category_info = $this->model_catalog_category->getCategory($path_id);
				
				if ($category_info) {
	       			$this->data['breadcrumbs'][] = array(
   	    				'text'      => $category_info['name'],
						'href'      => $this->url->link('product/category', 'path=' . $path),
        				'separator' => $this->language->get('text_separator')
        			);
				}
			}		
		
			$category_id = array_pop($parts);
		} else {
			//$this->request->get['path'] = 0;
			$category_id = 0;
		}
		
		$category_info = $this->model_catalog_category->getCategory($category_id);
		if ($category_info or $category_id == 0) {
		  if($category_info){
			if(isset($category_info['meta_title'])&&$category_info['meta_title']!='')
	  			$this->document->setTitle($category_info['meta_title']);
			else
				$this->document->setTitle($category_info['name']);
			
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);
			
			$this->data['heading_title'] = $category_info['name'];
			
			$this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			if ($category_info['image']) {
				$this->data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
			} else {
				$this->data['thumb'] = '';
			}
									
			$this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$this->data['compare'] = $this->url->link('product/compare');
			
			
			$url = '';
			 }else{
				 $this->document->setTitle("菜单");
				 $this->data['heading_title'] = "菜单";
				 
				 
				 }
			
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}	

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}	
			
			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
			
			if (isset($this->request->get['q'])) {
				$this->data['q'] =  $this->request->get['q'];
			}else{
				$this->data['q'] = "";
				}
			
			$this->data['products'] = array();
			
			$data = array(
				'filter_category_id' => $category_id, 
				'sort'               => $sort,
				'filter_name'        => $this->data['q'],
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);
					
			$product_total = $this->model_catalog_product->getTotalProducts($data); 
			
			$results = $this->model_catalog_product->getProducts($data);
	
			if(empty($results)){
				$this->data['is_results'] = $this->language->get('text_empty');
			}else{
				$this->data['is_results'] ="";
				}
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $result['image'].'!list';
					$yimage = $result['image'];
				} else {
					$image = "http://fyunimage.b0.upaiyun.com/no_image.jpg!list";
					$yimage = "http://fyunimage.b0.upaiyun.com/no_image.jpg";
				}
				
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
				
				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}	
				
				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}	
				
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
				} else {
					$tax = false;
				}				
				
				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}
			
				if (!isset($result['tag'])) {
				    $result['tag'] = false;
				} 
				
				$this->load->model('localisation/unit');
				$units = $this->model_localisation_unit->getUnit($result['unit']);
				$unit_id = $units['name'];
				
				$this->data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'ythumb'       => $yimage,
					'name'        => $result['name'],
					'quantity'        => $result['quantity'],
					'reward'        => $result['reward'],
					'description' => truncate(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 200) . '..',
					'price'       => $price,
					'special'     => $special,
					'unit'     => $unit_id,
					'tax'         => $tax,
					'tag'         =>  $result['tag'],
					'rating'      => $result['rating'],
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
					'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'])
				);
			
		
			}
			
			$url = '';
	
			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
							
			$this->data['sorts'] = array();
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
			);
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)
			);

			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)
			);

			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
			); 

			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
			); 
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_desc'),
				'value' => 'rating-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
			); 
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_asc'),
				'value' => 'rating-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
			);
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)
			);

			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)
			);
			
			$url = '';
	
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}	

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			$this->data['limits'] = array();
			
			$this->data['limits'][] = array(
				'text'  => $this->config->get('config_catalog_limit'),
				'value' => $this->config->get('config_catalog_limit'),
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $this->config->get('config_catalog_limit'))
			);
						
			$this->data['limits'][] = array(
				'text'  => 25,
				'value' => 25,
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=25')
			);
			
			$this->data['limits'][] = array(
				'text'  => 50,
				'value' => 50,
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=50')
			);

			$this->data['limits'][] = array(
				'text'  => 75,
				'value' => 75,
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=75')
			);
			
			$this->data['limits'][] = array(
				'text'  => 100,
				'value' => 100,
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=100')
			);
						
			$url = '';
	
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}	

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
	
			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
					
					
		    if($product_total>$this->config->get('config_catalog_limit')){
				$this->data['more'] = ' <div id="links" class="singleProductPurchaseButton">下一页</div>';
				}else{
					$this->data['more']="";
					}
			
			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->text = $this->language->get('text_pagination');
			$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&ajxa=1&page={page}');
		
			$this->data['pagination'] = $pagination->render();
			
			$pagination = new MobilePagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->style_links = 'ui-btn ui-corner-all ui-shadow ui-btn-inline ui-mini';
			$pagination->url = $this->url->link('product/category', 'page={page}', 'SSL');
			$this->data['pagination'] = $pagination->render();

			
			
		
			$this->data['sort'] = $sort;
			$this->data['order'] = $order;
			$this->data['limit'] = $limit;
		  
			$this->data['continue'] = $this->url->link('common/home');

			if (isset($this->request->get['ajxa'])) {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/ajxa_category.tpl')) {
					$this->template = $this->config->get('config_template') . '/template/product/ajxa_category.tpl';
				} else {
					$this->template = 'default/template/product/ajxa_category.tpl';
				}
				
			}else{
				
				  if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/category.tpl')) {
					  $this->template = $this->config->get('config_template') . '/template/product/category.tpl';
				  } else {
					  $this->template = 'default/template/product/category.tpl';
				  }	
			}
				
			 
			 
			
			
			$this->children = array(
				'common/header',
				'common/nav'
			);
				
			$this->response->setOutput($this->render());										
    	} else {
			$url = '';
			
			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}
									
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}	

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
				
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
						
			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
						
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_error'),
				'href'      => $this->url->link('product/category', $url),
				'separator' => $this->language->get('text_separator')
			);
				
			$this->document->setTitle($this->language->get('text_error'));

      		$this->data['heading_title'] = $this->language->get('text_error');

      		$this->data['text_error'] = $this->language->get('text_error');

      		$this->data['button_continue'] = $this->language->get('button_continue');

      		$this->data['continue'] = $this->url->link('common/home');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
			} else {
				$this->template = 'default/template/error/not_found.tpl';
			}
			
			$this->children = array(
				'common/header',
				'common/nav'
			);
					
			$this->response->setOutput($this->render());
		}
  	}
}
?>