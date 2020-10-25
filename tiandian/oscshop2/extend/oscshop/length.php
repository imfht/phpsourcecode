<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace oscshop;
use think\Db;
class Length {
	
	private $lengths = array();
	
	private static $instance;
	
	private function __construct() {
		
		$length_class_query = Db::name('length_class')->select(); 		
		foreach ($length_class_query as $result) {
			$this->lengths[$result['length_class_id']] = array(
				'length_class_id' => $result['length_class_id'],
				'title'           => $result['title'],
				'unit'            => $result['unit'],
				'value'           => $result['value']
			);
		}
	}
	//单例模式	
	public static function getInstance(){    
        if (!(self::$instance instanceof self))  
        {  
            self::$instance = new self();  
        }  
        return self::$instance;  
    }
	//禁克隆
	private function __clone(){} 
	
	public function convert($value, $from, $to) {
		if ($from == $to) {
			return $value;
		}

		if (isset($this->lengths[$from])) {
			$from = $this->lengths[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->lengths[$to])) {
			$to = $this->lengths[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function format($value, $length_class_id, $decimal_point = '.', $thousand_point = ',') {
		if (isset($this->lengths[$length_class_id])) {
			return number_format($value, 2, $decimal_point, $thousand_point) . $this->lengths[$length_class_id]['unit'];
		} else {
			return number_format($value, 2, $decimal_point, $thousand_point);
		}
	}

	public function getUnit($length_class_id) {
		if (isset($this->lengths[$length_class_id])) {
			return $this->lengths[$length_class_id]['unit'];
		} else {
			return '';
		}
	}
}

?>