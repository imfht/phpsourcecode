<?php
namespace Admin\Widget;
use Think\Controller;

class DatetimeWidget extends Controller {
	public function dateTime($name='date',$value='',$text=''){

		if(empty($value) && I($name)) $value=I($name);
		if(empty($text)){
			$text='<i class="icon-date"></i>';
		}elseif(preg_match('/^[a-z]+$/', $text)){
			$text='<i class="icon-'.$text.'"></i>';
		}
		$this->assign('text',$text);

		$this->assign('name',$name);
		$this->assign('value',$value);
		
		$this->display(MODULE_PATH.'Widget/Tpl/Datetime/dateTime.html');
	}

	public function date($name='date',$value='',$text=''){

		if(empty($value) && I($name)) $value=I($name);

		if(empty($text)){
			$text='<i class="icon-date"></i>';
		}elseif(preg_match('/^[a-z]+$/', $text)){
			$text='<i class="icon-'.$text.'"></i>';
		}
		$this->assign('text',$text);

		$this->assign('name',$name);
		$this->assign('value',$value);
		
		$this->display(MODULE_PATH.'Widget/Tpl/Datetime/date.html');
	}
}