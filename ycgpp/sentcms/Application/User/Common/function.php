<?php
function get_receiv(){
	$uid = session('user_auth.uid');
	$res = D('Receiv')->where(array('uid'=>$uid))->select();
	foreach ($res as $key => $value) {
		$value['all'] = $value['address'];
		$list[$key] = $value;
	}
	return $list;
}