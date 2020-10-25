<?php
final class SEO {
	// edit here to rewritte SEO URL
	private $common = array(
			'home' => 'common/home',
			'my-account'  => 'account/account',
			'voucher'=> 'checkout/voucher',
			'contact-us'=>'information/contact',
			'return-service' =>'account/return/insert',
			'sitemap' =>'information/sitemap',
			'brands' =>'product/manufacturer',
			'affiliate' =>'affiliate/account',
			'affiliate-register' =>'affiliate/register',
			'affiliate-login' =>'affiliate/login',
			'affiliate-edit' =>'affiliate/edit',
			'affiliate-payment' =>'affiliate/payment',	
			'affiliate-password' =>'affiliate/password',	
			'affiliate-tracking' =>'affiliate/tracking',	
			'affiliate-transaction' =>'affiliate/transaction',	
			'affiliate-forgotten' =>'affiliate/forgotten',	
			'affiliate-logout' =>'affiliate/logout',	
			'special'    =>'product/special',
			'order-history' =>'account/order',
			'order-detail' =>'account/order/info',
			'wishlist' =>'account/wishlist',
			'login' =>'account/login',
			'logout' =>'account/logout',
			'checkout' =>'checkout/checkout',
			'compare' =>'product/compare',
			'newsletter' =>'account/newsletter',
			'forgotten' =>'account/forgotten',
			'cart' =>'checkout/cart',
			'register' =>'account/register',
			'edit-account' =>'account/edit',
			'address' =>'account/address',
			'password' =>'account/password',
			'mydownload' =>'account/download',
			'reward' =>'account/reward',
			'transaction' =>'account/transaction',
			'return' =>'account/return',
			'update-address'  =>'account/address/update',
			'delete-address'  =>'account/address/delete',
			'return-info'=>'account/return/info',
			'invite'=>'account/invite',
	);
	
	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->request = $registry->get('request');
		$this->db = $registry->get('db');
		$this->log = $registry->get('log');
	}
	
	
	public function get_route() {
		// Decode URL
		if (isset($this->request->get['_route_'])) {
			// decode URL base rule
			$decode=$this->decode_base_rule($this->request->get['_route_']);
			if($decode)
				return $decode;
			// end
				
			$parts = explode('/', $this->request->get['_route_']);
				
			foreach ($parts as $part) {
				$this->log->write( 'keyword = '.$part.'  | ');
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE lower(keyword) = '" . strtolower($this->db->escape($part)) . "'");
	
				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);
						
					$seo->decode($url[0]);
	
					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}
						
					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}
						
					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}
						
					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}
				} else {
					$this->request->get['route'] = 'error/not_found';
				}
			}
				
			if (isset($this->request->get['product_id'])) {
				$this->request->get['route'] = 'product/product';
			} elseif (isset($this->request->get['path'])) {
				$this->request->get['route'] = 'product/category';
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$this->request->get['route'] = 'product/manufacturer/product';
			} elseif (isset($this->request->get['information_id'])) {
				$this->request->get['route'] = 'information/information';
			}
				
			if (isset($this->request->get['route'])) {
				return $this->request->get['route'];
			}
		}
	}
	
	public function decode_base_rule($link) {
		global  $decodes;
		foreach ($decodes as $key => $value) {
			$pattern =$key;
			$replacement = $value;
				
			if(preg_match($pattern,$link)){
				$target=preg_replace($pattern, $replacement, $link);
				$target_data=$this->getURL(HTTP_SERVER.'index.php?route='.$target);
				foreach ($target_data as $key => $value) {
					$this->request->get[$key]=$value;
				}
				$this->request->get['route']=$target_data['route'];
				return $target_data['route'];
			}
		}
		
		return 0;
	}
	
	public function rewrite_base_rule ($link) {
		global  $rewirtes;
		
		$link=str_replace(HTTP_SERVER.'index.php?route=','',str_replace('&amp;', '&', $link));
		
		foreach ($rewirtes as $key => $value) {
			if(preg_match($key,$link)){
				return strtolower(preg_replace($key, $value, $link));
			}
		}
	
		return strtolower($link);
	}

	private function getURL($str){
		$data = array();
		$url=explode('?',$str);
		$parameter = explode('&',end($url));
		foreach($parameter as $val){
			$tmp = explode('=',$val);
			$data[$tmp[0]] = $tmp[1];
		}
		return $data;
	}

	public function rewrite ($route) {
		$url='';
  		 if (in_array($route, array_values($this->common))) {
  		 	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE lower(`query`) = '".strtolower($route)."'");
			if ($query->num_rows) {
				$url= '/' . strtolower($query->row['keyword']);
			}		
		} 
		
		return $url;
    }

	public function decode ($url) {
		if (in_array($url,array_values($this->common))) {
			$this->request->get['route'] = strtolower($url);	
		}		
	 }

	public function generateSEOURL () {
		 $this->clear();
  		 $this->generateCommon();
         $this->generateCategories();
         $this->generateProducts();
         $this->generateInformations();
         $this->generateManufacturers();
    }
    
    public function generateCategories () {
        $categories = $this->getCategories();
		 foreach ($categories as $category) {
			$uniqueSlug = $this->makeSlugs($category['name']);
           	$query_url = 'category_id=' . (int)$category['category_id'] ;
            $this->delete($query_url);
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = '".$query_url."', keyword = '" . $this->db->escape($uniqueSlug) . "'");
        }
    }
    
    public function generateProducts () {
    	$products = $this->getProducts();
		foreach ($products as $product) {
            $uniqueSlug = $this->makeSlugs($product['name']);
         	$query_url = 'product_id=' . (int)$product['product_id'] ;
            $this->delete($query_url);
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = '".$query_url."', keyword = '" . $this->db->escape($uniqueSlug) . "'");
        }
     
    }
    
     public function generateInformations () {
    	$informations = $this->getInformations();
    	foreach ($informations as $information) {
            $uniqueSlug = $this->makeSlugs($information['title']);
         	$query_url = 'information_id=' . (int)$information['information_id'] ;
            $this->delete($query_url);
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = '".$query_url."', keyword = '" . $this->db->escape($uniqueSlug) . "'");
        }
    	
    }
    
  	public function generateManufacturers () {
    	$manufacturers = $this->getManufacturers();
    	foreach ($manufacturers as $manufacturer) {
            $uniqueSlug = $this->makeSlugs($manufacturer['name']);
         	$query_url = 'manufacturer_id=' . (int)$manufacturer['manufacturer_id'] ;
            $this->delete($query_url);
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = '".$query_url."', keyword = '" . $this->db->escape($uniqueSlug) . "'");
        }
    }
    
    public function generateCommon () {
    	foreach ($this->common as $key => $value) {
		 	 $this->delete($value);
            $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = '".$value."', keyword = '" . $this->db->escape($key) . "'");
		}
    }
    
    private function getCategories () {
        $query = $this->db->query("SELECT c.category_id, cd.name,c.parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
        return $query->rows;
    }
    
    private function getInformations() {
     	$sql = "SELECT i.information_id, id.title FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";
        $query = $this->db->query($sql);
        return $query->rows;
    }
    
  	private function getProducts () {
        $query = $this->db->query("SELECT p.product_id, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY p.product_id ASC");
        return $query->rows;
    }
    
    private function getManufacturers () {
       $query = $this->db->query("SELECT manufacturer_id, name FROM " . DB_PREFIX . "manufacturer ORDER BY name");
        return $query->rows;
    }
    // Taken from http://code.google.com/p/php-slugs/
    private function my_str_split ($string) {
        $slen = strlen($string);
        for ($i = 0; $i < $slen; $i ++) {
            $sArray[$i] = $string{$i};
        }
        return $sArray;
    }
    private function noDiacritics ($string) {

        $i =  strpos($string, '(');
        if($i){
        	
        	$string = substr($string,0,$i);
        }
        
        $cyrylicFrom = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $cyrylicTo = array('A', 'B', 'W', 'G', 'D', 'Ie', 'Io', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Ch', 'C', 'Tch', 'Sh', 'Shtch', '', 'Y', '', 'E', 'Iu', 'Ia', 'a', 'b', 'w', 'g', 'd', 'ie', 'io', 'z', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ch', 'c', 'tch', 'sh', 'shtch', '', 'y', '', 'e', 'iu', 'ia');
        $from = array("Á", "À", "Â", "Ä", "Ă", "Ā", "Ã", "Å", "Ą", "Æ", "Ć", "Ċ", "Ĉ", "Č", "Ç", "Ď", "Đ", "Ð", "É", "È", "Ė", "Ê", "Ë", "Ě", "Ē", "Ę", "Ə", "Ġ", "Ĝ", "Ğ", "Ģ", "á", "à", "â", "ä", "ă", "ā", "ã", "å", "ą", "æ", "ć", "ċ", "ĉ", "č", "ç", "ď", "đ", "ð", "é", "è", "ė", "ê", "ë", "ě", "ē", "ę", "ə", "ġ", "ĝ", "ğ", "ģ", "Ĥ", "Ħ", "I", "Í", "Ì", "İ", "Î", "Ï", "Ī", "Į", "Ĳ", "Ĵ", "Ķ", "Ļ", "Ł", "Ń", "Ň", "Ñ", "Ņ", "Ó", "Ò", "Ô", "Ö", "Õ", "Ő", "Ø", "Ơ", "Œ", "ĥ", "ħ", "ı", "í", "ì", "i", "î", "ï", "ī", "į", "ĳ", "ĵ", "ķ", "ļ", "ł", "ń", "ň", "ñ", "ņ", "ó", "ò", "ô", "ö", "õ", "ő", "ø", "ơ", "œ", "Ŕ", "Ř", "Ś", "Ŝ", "Š", "Ş", "Ť", "Ţ", "Þ", "Ú", "Ù", "Û", "Ü", "Ŭ", "Ū", "Ů", "Ų", "Ű", "Ư", "Ŵ", "Ý", "Ŷ", "Ÿ", "Ź", "Ż", "Ž", "ŕ", "ř", "ś", "ŝ", "š", "ş", "ß", "ť", "ţ", "þ", "ú", "ù", "û", "ü", "ŭ", "ū", "ů", "ų", "ű", "ư", "ŵ", "ý", "ŷ", "ÿ", "ź", "ż", "ž");
        $to = array("A", "A", "A", "A", "A", "A", "A", "A", "A", "AE", "C", "C", "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E", "E", "G", "G", "G", "G", "G", "a", "a", "a", "a", "a", "a", "a", "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e", "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H", "I", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L", "N", "N", "N", "N", "O", "O", "O", "O", "O", "O", "O", "O", "CE", "h", "h", "i", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k", "l", "l", "n", "n", "n", "n", "o", "o", "o", "o", "o", "o", "o", "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z", "Z", "Z", "r", "r", "s", "s", "s", "s", "B", "t", "t", "b", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y", "z", "z", "z");
		$f  = array("&",'(',')','.');
		$t  = array(" ",' ',' ',' ');
        $from = array_merge($from, $cyrylicFrom,$f);
        $to = array_merge($to, $cyrylicTo,$t);
        $newstring = str_replace($from, $to, $string);
        return $newstring;
    }
    
    public function makeSlugs ($string, $maxlen = 0) {
        //TODO: improve the url rewrite
    	$string=str_replace(array(" ","*","+"),'-',$string); 
//    	$string=str_replace(array("\"","'"),'',$string); 
    	
    	$string=$this->removeDuplicates("--", '-', $string);
    	
    	return  $string;
    	/*$newStringTab = array();
        $string = strtolower($this->noDiacritics($string));
        if (function_exists('str_split')) {
            $stringTab = str_split($string);
        } else {
            $stringTab = $this->my_str_split($string);
        }
        $numbers = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-");
        foreach ($stringTab as $letter) {
            if (in_array($letter, range("a", "z")) || in_array($letter, $numbers)) {
                $newStringTab[] = $letter;
            } elseif ($letter == " ") {
                $newStringTab[] = "-";
            }
        }
        if (count($newStringTab)) {
            $newString = implode($newStringTab);
            if ($maxlen > 0) {
                $newString = substr($newString, 0, $maxlen);
            }
            $newString = $this->removeDuplicates('--', '-', $newString);
        } else {
            $newString = '';
        }
        return $newString;*/
    }
    
    private function removeDuplicates ($sSearch, $sReplace, $sSubject) {
        $i = 0;
        do {
            $sSubject = str_replace($sSearch, $sReplace, $sSubject);
            $pos = strpos($sSubject, $sSearch);
            $i ++;
            if ($i > 100) {
                die('removeDuplicates() loop error');
            }
        } while ($pos !== false);
        return $sSubject;
    }

	private function delete($query_url = ''){
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = '".$query_url."'");	
    	return true;
    }  
    
    private function clear($query_url = ''){
    	$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias");
    	return true;
    }
}
?>