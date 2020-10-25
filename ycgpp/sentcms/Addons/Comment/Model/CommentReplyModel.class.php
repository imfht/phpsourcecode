<?php 
namespace Addons\Comment\Model;
use Think\Model;
class CommentReplyModel extends Model{
	protected $_auto = array(
		array('uid','session',self::MODEL_INSERT,'function','user_auth.uid'),
		array('nickname','session',self::MODEL_INSERT,'function','user_auth.username'),
		array('content','checkpost',self::MODEL_INSERT,'callback'),
		array('ip','get_client_ip',self::MODEL_INSERT,'function'),
		array('status','getStatus',self::MODEL_INSERT,'callback'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_BOTH,'function'),
	);

	public function getStatus(){
		return 1;
	}

	public function checkpost(){
		return htmlspecialchars(trim($_POST['content']));
	}

	/*获取指定的回复信息*/
	public function getAllReply($commenid){
		$map['commentid'] = $commenid;
		$map['status'] = 1;
		$result = $this->where($map)->order('create_time DESC')->select();
		array_shift($result);
		foreach ($result as $key => $value) {
			foreach ($value as $k => $v) {
				if($k == 'create_time'){
					$result[$key]['create_time'] = time_format($v);
				}
			}
		}
		return json_encode($result);
	}

	/*获取指定的回复信息--按照发布时间来排序*/
	public function getAllReplyOrder($commenid){
		$map['commentid'] = $commenid;
		$map['status'] = 1;
		return $this->where($map)->order('create_time DESC')->find();
	}

}