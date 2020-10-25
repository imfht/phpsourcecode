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
class Weight {
	
	private $weights = array();
	
    private static $instance;
	
	private function __construct() {		

		$weight_class_query = Db::name('weight_class')->select(); 
		foreach ($weight_class_query as $result) {
			$this->weights[$result['weight_class_id']] = array(
				'weight_class_id' => $result['weight_class_id'],
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
	
	//单位换算,并返回计算结果
	public function convert($value, $from, $to) {
		if ($from == $to) {
			return $value;
		}

		if (isset($this->weights[$from])) {
			$from = $this->weights[$from]['value'];
		} else {
			$from = 0;
		}

		if (isset($this->weights[$to])) {
			$to = $this->weights[$to]['value'];
		} else {
			$to = 0;
		}	

		return $value * ($to / $from);
	}
	//格式化
	public function format($value, $weight_class_id, $decimal_point = '.', $thousand_point = ',') {
		if (isset($this->weights[$weight_class_id])) {
			/**
			 * php函数number_format(),通过千位分组来格式化数字，
			 * 参数1，要格式化的数字
			 * 参数2，规定多少位小数
			 * 参数3，小数点字符串
			 * 参数4 ， 千位分格符字符串
			 */
			return array(
				'num'=>number_format($value, 2, $decimal_point, $thousand_point),
				'format'=>number_format($value, 2, $decimal_point, $thousand_point) . $this->weights[$weight_class_id]['unit']
			); 
			 
		} else {
			return array(
				'num'=>number_format($value, 2, $decimal_point, $thousand_point),
			); 
		}
	}

	public function getUnit($weight_class_id) {
		if (isset($this->weights[$weight_class_id])) {
			return $this->weights[$weight_class_id]['unit'];
		} else {
			return '';
		}
	}	
} 
?>