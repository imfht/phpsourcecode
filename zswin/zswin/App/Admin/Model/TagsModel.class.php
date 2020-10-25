<?php
/*http://www.zswin.cn
QQ:49007623
company:kssoulmate.com*/
namespace Admin\Model;
use Think\Model;


class TagsModel extends CommonModel {
	
public function _after_find(&$result,$options) {
	$typetext =C('CATE_TYPE');
	
	$result['typetext'] = $typetext[$result['type']];
	$result['path']=getThumbImageById($result['img']);

}

public function _after_select(&$result,$options){
	foreach($result as &$record){
			
		$this->_after_find($record,$options);
	}
}
}