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
 * 
 */
namespace osc\common\service;
use think\Db;
class Transport{
	
	/**
	 * object 对象实例
	 */
	private static $instance;
	
	//禁外部实例化
	private function __construct(){}
	
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
	
	/**
	 * 计算某地区某运费模板ID下的商品总运费，如果运费模板不存在或，按免运费处理
	 *
	 * @param int $transport_id 运费模版id
	 * @param int $buy_num 商品重量
	 * @param int $area_id 地区id
	 * @return number/boolean
	 */
	public function calc_transport($transport_id, $buy_num, $area_id) {
		if (empty($transport_id) || empty($buy_num) || empty($area_id)) return 0;
		$extend_list = Db::name('TransportExtend')->where('transport_id',$transport_id)->select();
	
		if (empty($extend_list)) {
		    return 0;
		} else {
		    return $this->calc_unit($area_id,$buy_num,$extend_list);
		}
	}	

	/**
	 * 计算某个具单元的运费
	 *
	 * @param 配送地区 $area_id
	 * @param 购买数量 $num
	 * @param 运费模板内容 $extend
	 * @return number 总运费
	 */
	private function calc_unit($area_id, $num, $extend){		
		
		if (!empty($extend) && is_array($extend)){
			
			 $calc_total=array(
				'error'=>'该地区不配送！！'
			);
			
			$weight_unit=get_weight_name(config('weight_id'));
			
			foreach ($extend as $v) {
				/**
				 * strpos函数返回字符串在另一个字符串中第一次出现的位置，没有该字符返回false
				 * 参数1，字符串
				 * 参数2，要查找的字符
				 */				
				if (strpos($v['area_id'],",".$area_id.",") !== false){
					
					unset($calc_total['error']);
					
					$area_name=Db::name('Area')->field('area_name')->where('area_id',$area_id)->find();
					
					if ($num <= $v['snum']){
						//在首重数量范围内
						$calc_total['price'] = $v['sprice'];
					}else{
						//超出首重数量范围，需要计算续重
						$calc_total['price'] = sprintf('%.2f',($v['sprice'] + ceil(($num-$v['snum'])/$v['xnum'])*$v['xprice']));
					}				
					$calc_total['info']=$area_name['area_name'].'，首重(小于等于1'.$weight_unit.') '.$v['sprice'].'元'
					.' 续重(每'.$weight_unit.') '.$v['xprice'].'元，总计 '.$num.' '.$weight_unit.'';
					return $calc_total;
				}
				
			}
			//没有找到则选择默认运费选项
			if(isset($extend[0])&&is_array($extend)){
					unset($calc_total['error']);
					
					$area_name=Db::name('Area')->field('area_name')->where('area_id',$area_id)->find();
					
					if ($num <= $extend[0]['snum']){
						//在首重数量范围内
						$calc_total['price'] = $extend[0]['sprice'];
					}else{
						//超出首重数量范围，需要计算续重
						$calc_total['price'] = sprintf('%.2f',($extend[0]['sprice'] + ceil(($num-$extend[0]['snum'])/$extend[0]['xnum'])*$extend[0]['xprice']));
					}				
					$calc_total['info']=$area_name['area_name'].'，首重(小于等于1'.$weight_unit.') '.$extend[0]['sprice'].'元'
					.' 续重(每'.$weight_unit.') '.$extend[0]['xprice'].'元，总计 '.$num.' '.$weight_unit.'';
					return $calc_total;
			}
			
			
			return $calc_total;
		}
		
	}	
	
	
			
	
}