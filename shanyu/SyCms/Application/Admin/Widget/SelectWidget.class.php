<?php
namespace Admin\Widget;
use Think\Controller;

class SelectWidget extends Controller {
	public function city($name='province,city,area',$value=array(),$select=array()){

		$name_select=explode(',', $name);
		if(!empty($value)){
			foreach ($name_select as $v) {
				$values[]=$value[$v];
			}
			$this->assign('value',$values);
		}else{
			$this->assign('value','');
		}

		$this->assign('name_select',$name_select);
		$name='"'.implode('","', $name_select).'"';
		$this->assign('name',$name);

		$this->assign('level',$level);
		$this->assign('select',$select);
		
		$this->display(MODULE_PATH.'Widget/Tpl/Select/city.html');
	}

}
