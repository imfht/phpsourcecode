<?php 
namespace Addons\Comment\Model;
use Think\Model;
class CommentModel extends Model {

	protected $_auto = array(
		array('uid','session',self::MODEL_INSERT,'function','user_auth.uid'),
		array('nickname','session',self::MODEL_INSERT,'function','user_auth.username'),
		array('ip','get_client_ip',self::MODEL_INSERT,'function'),
		array('status','getStatus',self::MODEL_INSERT,'callback'),
		array('create_time','time',self::MODEL_INSERT,'function'),
	);

	public function getStatus(){
		return 1;
	}


	/*查询评论*/
	public function getAllComment(){
		$map['aid'] = I('get.id');
		$map['status'] = 1;
		return $this->where($map)->field('id,uid,aid,nickname,content,create_time,reply_num,ip,status')->order('create_time DESC')->select();
	}
}