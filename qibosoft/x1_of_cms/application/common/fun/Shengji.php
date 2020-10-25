<?php
namespace app\common\fun;
use think\Db;
class Shengji{
	public function status($uid=0,$gid=11){
		if(empty($uid)){
			$map['uid']=login_user('uid');
		}else{
			$map['uid']=$uid;
		}
		$map['gid']=$gid;
		$grouplog=Db::name('grouplog')->where($map)->field('status,refuse_reason,check_time')->order('id desc')->find();
		return $grouplog;
	}
}