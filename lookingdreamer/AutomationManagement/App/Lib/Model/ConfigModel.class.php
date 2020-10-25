<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

// 配置模型
class ConfigModel extends CommonModel {

	protected $_validate = array(
		array('name','require','参数名称必须'),
		array('name','checkName','参数已经定义',0,'callback'),
		);

	protected $_auto		=	array(
		array('create_time','time','function',self::MODEL_INSERT),
		);

	public function checkName() {
		$map['name']	 =	 $_POST['name'];
        if(!empty($_POST['id'])) {
			$map['id']	=	array('neq',$_POST['id']);
        }
		$result	=	$this->find(array('where'=>$map,'field'=>'id'));
        if($result) {
        	return false;
        }else{
			return true;
		}
	}
}
?>