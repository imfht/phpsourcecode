<?php
class ModelToolSeoUrl extends Model {
	public function rewrite($link) {
		if ($this->config->get('config_seo_url')) {
		
			$url_data = parse_url(str_replace('&amp;', '&', $link));
		
			$url = ''; 
			
			$data = array();
			
			parse_str($url_data['query'], $data);
			
			foreach ($data as $key => $value) {
				
				if (($key == 'product_id') || ($key == 'manufacturer_id') || ($key == 'information_id')||($key == 'news_id')) {
					
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
				// add other sel url
				elseif ( $value == 'common/home') {
					
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE  `query` = 'common/home'");
				
					if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
					}							
					unset($data[$key]);
				}
				elseif ( $value == 'product/products') {
					
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE  `query` = 'product/products'");
				
					
					
					if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
					}		

											
					unset($data[$key]);
				}
				
				elseif ( $value == 'information/news') {
					
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE  `query` = 'information/news'");
				
					if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
					}							
					unset($data[$key]);
				}
				elseif ( $value == 'information/contact') {
					
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE  `query` = 'information/contact'");
				
					if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
					}							
					unset($data[$key]);
				}
				elseif ( $value == 'information/sitemap') {
					
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE  `query` = 'information/sitemap'");
				
					if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
					}							
					unset($data[$key]);
				}
				elseif ( $value == 'product/search') {
					
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE  `query` = 'product/search'");
				
					if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
					}							
					unset($data[$key]);
				}
				elseif ( $value == 'information/contact/success') {
					
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE  `query` = 'information/contact/success'");
				
					if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
					}							
					unset($data[$key]);
				}
				
				// end 
			}
		
			if ($url) {
				
				unset($data['route']);
			
				$query = '';
			
				if ($data) {
					foreach ($data as $key => $value) {
						$query .= '&' . $key . '=' . $value;
					}
					
					if ($query) {
						$query = '?' . str_replace('&amp;', '&', trim($query, '&'));
					}
				}

				return $url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '') . str_replace('/index.php', '', $url_data['path']) . $url . $query;
			} else {

			   return $link;

			}
		} else {
			return $link;
		}		
	}
}
?>