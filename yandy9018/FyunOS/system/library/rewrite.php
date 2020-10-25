<?php
class Rewrite {
	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->request = $registry->get('request');
		$this->db = $registry->get('db');
		$this->url = $registry->get('url');
		$this->registry=$registry;
		$this->url->addRewrite($this);
	}
	
	public function rewrite($link) {
		$seo= new SEO($this->registry);
		if ($this->config->get('config_seo_url')) {
			$url_data = parse_url(str_replace('&amp;', '&', $link));
		
			$url = ''; 
			
			$data = array();
			if(isset($url_data['query']))
				parse_str($url_data['query'], $data);
			
			foreach ($data as $key => $value) {
				if (isset($data['route'])) {
					$url .= $seo->rewrite($data['route']);
					
					if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/product' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");
					
						if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
							
							unset($data[$key]);
						}					
					} elseif ($key == 'path') {
						$categories = explode('_', $value);
						
						foreach ($categories as $category) {
							$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int)$category . "'");
					
							if ($query->num_rows) {
								$url .= '/' . $query->row['keyword'];
							}							
						}
						
						unset($data[$key]);
					}
				}
			}
		
			if ($url) {
				unset($data['route']);
			
				$query = '';
			
				if ($data) {
					foreach ($data as $key => $value) {
						$query .= '&' . $key . '=' . $value;
					}
					
					if ($query) {
						$query = '?' . trim($query, '&');
					}
				}
			
				return strtolower($url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '') . str_replace('/index.php', '', $url_data['path']) . $url . $query);
			} else {
				// rewrite URL base rule
				if ($this->config->get('config_seo_url')) {
					return $link;
				}else{
					return $seo->rewrite_base_rule($link);
				}
				// end 
			}
		} else {
				// rewrite URL base rule
				if ($this->config->get('config_seo_url')) {
					return $seo->rewrite_base_rule($link);
				}else{
					return $link;
				}
				// end 
		}		
	}	
	
	
}
?>