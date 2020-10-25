<?php
final class Cart {
  	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->db = $registry->get('db');
		$this->tax = $registry->get('tax');
		$this->weight = $registry->get('weight');

        if(isset($_COOKIE['cart'])){
			$_COOKIE['cart'] = explode(',',$_COOKIE['cart']);
			
		}else{
			$_COOKIE['cart'] =  array();
			}
       	}
	
  	public function getProducts() {
		$product_data = array();
    	foreach ($_COOKIE['cart'] as $key => $value) {
			$product = explode('-',$value);
      		$product_id = $product[0];
			if(isset($product[1])){
				$quantity = $product[1];
				}else{
					$quantity = 0;
					}
			
            $stock = true;
      		$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
      	  	
			if ($product_query->num_rows) {
      			
			
		
				$customer_group_id = $this->config->get('config_customer_group_id');
			
				
				$price = $product_query->row['price'];
				
				// Product Discounts菜品折扣
				$discount_quantity = 0;
				
				foreach ($_COOKIE['cart'] as $key_2 => $value_2) {
					$product_2 = explode('-', $value_2);
					
					if ($product_2[0] == $product_id) {
						if(isset($product_2[1])){
							$discount_quantity += $product_2[1];
							}
					}
				}
				$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");
				
				if ($product_discount_query->num_rows) {
					$price = $product_discount_query->row['price'];
				}
				
				
				// Product Specials菜品特价
				$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
			
				if ($product_special_query->num_rows) {
					$price = $product_special_query->row['price'];
				}						
		
				// Reward Points菜品积分
				$query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "'");
				
				if ($query->num_rows) {	
					$reward = $query->row['points'];
				} else {
					$reward = 0;
				}

				// Stock
				if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $quantity)) {
					$stock = false;
				}
				
      			$product_data[$key] = array(
        			'key'             => $key,
        			'product_id'      => $product_query->row['product_id'],
        			'name'            => $product_query->row['name'],
        			'model'           => $product_query->row['model'],
					'shipping'        => $product_query->row['shipping'],
        			'image'           => $product_query->row['image'],
        			'quantity'        => $product_query->row['quantity'],
					'quantity1'        =>$quantity,
        			'minimum'         => $product_query->row['minimum'],
					'subtract'        => $product_query->row['subtract'],
					'stock'           => $stock,
					'price'           => $price,
					'unit'           => $product_query->row['unit_id'],
        			'total'           => $price * $quantity,
					'allReward'       => $reward * $quantity,
					'reward'          => $reward,
					'points'          => $product_query->row['points'] * $quantity,
					'tax_class_id'    => $product_query->row['tax_class_id'],
        			'weight_class_id' => $product_query->row['weight_class_id'],
        			'length'          => $product_query->row['length'],
					'width'           => $product_query->row['width'],
					'height'          => $product_query->row['height'],
        			'length_class_id' => $product_query->row['length_class_id']					
      			);
			} else {
				$this->remove($key);
			}
    	}

		return $product_data;
  	}
		  
  	public function add($product_id, $qty = 1, $options = array()) {
    	if (!$options) {
      		$key = (int)$product_id;
    	} else {
      		$key = (int)$product_id . ':' . base64_encode(serialize($options));
    	}
    	
		if ((int)$qty && ((int)$qty > 0)) {
    		if (!isset($_COOKIE['cart'][$key])) {
      			$_COOKIE['cart'][$key] = (int)$qty;
    		} else {
      			$_COOKIE['cart'][$key] += (int)$qty;
    		}
		}
  	}

  	public function update($key, $qty) {
    	if ((int)$qty && ((int)$qty > 0)) {
      		$_COOKIE['cart'][$key] = (int)$qty;
    	} else {
	  		$this->remove($key);
		}
  	}

  	public function remove($key) {
		if (isset($_COOKIE['cart'][$key])) {
     		unset($_COOKIE['cart'][$key]);
  		}
	}
	
  	public function clear() {
	$_COOKIE['cart']=array();
	$product_data = array();
	$this->data = array();
  	}
	
  	public function getWeight() {
		$weight = 0;
	
    	foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
      			$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			}
		}
	
		return $weight;
	}
	
  	public function getSubTotal() {
		$total = 0;
		
		foreach ($this->getProducts() as $product) {
			$total += $product['total'];
		}

		return $total;
  	}
	
	public function getTaxes() {
		$taxes = array();
		
		foreach ($this->getProducts() as $product) {
			if ($product['tax_class_id']) {
				if (!isset($taxes[$product['tax_class_id']])) {
					$taxes[$product['tax_class_id']] = $product['total'] / 100 * $this->tax->getRate($product['tax_class_id']);
				} else {
					$taxes[$product['tax_class_id']] += $product['total'] / 100 * $this->tax->getRate($product['tax_class_id']);
				}
			}
		}
		
		return $taxes;
  	}

  	public function getTotal() {
		$total = 0;
		
		foreach ($this->getProducts() as $product) {
			$total += $this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax'));
		}

		return $total;
  	}
  	
	public function getTotalRewardPoints() {
		$total = 0;
		
		foreach ($this->getProducts() as $product) {
			$total += $product['reward'];
		}

		return $total;
  	}
	  	
  	public function countProducts() {
		$product_total = 0;
			
		$products = $this->getProducts();
			
		foreach ($products as $product) {
			$product_total += $product['quantity1'];
		}		
					
		return $product_total;
	}
	  
  	public function hasProducts() {
    	return count($_COOKIE['cart']);
  	}
  
  	public function hasStock() {
		$stock = true;
		
		foreach ($this->getProducts() as $product) {
			if (!$product['stock']) {
	    		$stock = false;
			}
		}
		
    	return $stock;
  	}
  
  	public function hasShipping() {
		$shipping = false;
		
		foreach ($this->getProducts() as $product) {
	  		if ($product['shipping']) {
	    		$shipping = true;
				
				break;
	  		}		
		}
		
		return $shipping;
	}
	
  	public function hasDownload() {
		$download = false;
		
		foreach ($this->getProducts() as $product) {
	  		if ($product['download']) {
	    		$download = true;
				
				break;
	  		}		
		}
		
		return $download;
	
	}	
	
}
?>