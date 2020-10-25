<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Home\Widget;
use Think\Controller;
/**
 * 轮播器
 */
class SliderWidget extends Controller{
	
	function slider_show(){
		if (!$slider_cache = S('slider_cache')) {					
			$slider=M('plugins_slider')->field('image,url')->select();
			S('slider_cache', $slider);
			$slider_cache=$slider;
		}
		$this->slider=$slider_cache;
		$this->display('Widget:slider');	
	}
	
}
