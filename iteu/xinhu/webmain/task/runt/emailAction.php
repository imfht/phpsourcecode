<?php
/**
*	要用计划任务运行的，建议半小时运行一次
*	php task.php email
*/
class emailClassAction extends runtAction
{
	
	/**
	*	自动接收邮件，必须用cli运行的哦
	*/
	public function defaultAction()
	{
		$rows 	= $this->db->getall("select b.`id`,b.`name`,b.`email` from `[Q]option` a left join `[Q]admin` b on a.`optid`=b.`id` where b.`status`=1 and a.`num` like 'email_recexin_%' and ifnull(a.`value`,'')<>''");
		$cg 	= $sb = 0;
		$tzid 	= '';
		$estr 	= '';
		foreach($rows as $k=>$rs){
			if(isempt($rs['email']))continue; //没设置邮箱
			$uid = $rs['id'];
			$this->rock->adminid 	= $uid;
			$this->rock->adminname 	= '系统';
			$barr = m('emailm')->receemail($uid);
			if(is_array($barr)){
				$cg+=$barr['count'];
				if($barr['count']>0)$tzid.=','.$uid.'';
			}else{
				$estr.=''.$uid.'.'.$barr.';';
				$sb++;
			}
		}
		if($estr!='')m('log')->addlogs('收邮件', $estr, 2);
		
		//发通知
		if($tzid!=''){
			$flow = m('flow')->initflow('emailm');
			$flow->push(substr($tzid,1),'','有未读的新邮件，请进入邮件应用查看详情。','新邮件提醒', 0, array(
				'wxurl' => $flow->getwxurl()
			));
		}
		
		return 'success('.$cg.'),fail('.$sb.')';
	}
}