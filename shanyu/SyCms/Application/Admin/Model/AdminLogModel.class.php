<?php
namespace Admin\Model;
use Think\Model;

class AdminLogModel extends Model{

	protected $_auto = array (
		array('log_ip','get_client_ip',1,'function'),
		array('log_time','date',1,'function',array('Y-m-d H:i:s')),
	);

	public function log($action='',$type=0,$uid=0){
		$data=$this->create();
		$data['uid']=defined(UID) ? UID : $uid;
		$data['log_type']=$type;
		$data['log_action']=$action;
		$data['log_record']=$this->getRecord($data,$type);

		if(M('AdminLog')->add($data)){
			return true;
		}else{
			$this->error='添加操作记录失败';
			return false;
		}
		
	}
	private $record_tpl=array(
		'login'=>array(
			'[#username#]登陆系统',
			'IP为[#action_ip#]的用户使用账号[#username#]密码[#password#]尝试登陆系统',
			'禁用账号[#username#]使用密码[#password#]尝试登陆系统',
			'账号[#username#]使用错误密码[#password#]尝试登陆系统',
		),
	);

	private function getRecord($data=array(),$type=''){
		$post=I('post.');
		if(!empty($post)) $data=array_merge($post,$data);
		//获取记录模板
		$tpl=$this->record_tpl[$data['log_action']][$type];
		//获取待替换字符的数组
		preg_match_all('/#(\w+)#/',$tpl,$wait);
		//构建正则替换参数
		for ($i=0; $i < count($wait[0]); $i++) {
			$patterns[]='/'.$wait[0][$i].'/';
			$replacements[]=$data[$wait[1][$i]];
		}
		$result=preg_replace($patterns, $replacements, $tpl);
		return $result;
	}
}