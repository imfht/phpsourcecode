<?php
/*http://www.zswin.cn
QQ:49007623
company:kssoulmate.com*/
namespace Admin\Model;
use Think\Model;


class LocalCommentModel extends CommonModel {
	
public function _after_find(&$result,$options) {
	
	$result['rowtitle'] =D($result['app'].'/'.$result['con'])->where(array('id'=>$result['row_id']))->getField('title');
	
	if(strtolower($result['con'])=='article'){
		$result['rowurl'] = U('Home/Index/artc',array('id'=>$result['row_id']));
	}
	if(strtolower($result['con'])=='music'){
		$result['rowurl'] = U('Home/Index/musicc',array('id'=>$result['row_id']));
	}
	if(strtolower($result['con'])=='group'){
		$result['rowurl'] = U('Home/Index/groupc',array('id'=>$result['row_id']));
	}
	
	
	

}

public function _after_select(&$result,$options){
	foreach($result as &$record){
			
		$this->_after_find($record,$options);
	}
}
}