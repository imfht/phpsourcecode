<?php
/*http://www.zswin.cn
QQ:49007623
company:kssoulmate.com*/
namespace Admin\Model;
use Think\Model;


class MessageModel extends CommonModel {
	
public function _after_find(&$result,$options) {
	$typetext = array(0=>'系统消息',1=>'私信');
	$result['typetext'] = $typetext[$result['type']];
	

}

public function _after_select(&$result,$options){
	foreach($result as &$record){
			
		$this->_after_find($record,$options);
	}
}
}