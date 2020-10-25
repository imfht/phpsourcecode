<?php 
class minute5ClassAction extends runtAction
{
	
	public function runAction()
	{
		$time 	= time();
		$time1 	= $time;
		$time2 	= $time1+5*60;
		$time3 	= $time1-5*60;
		$this->startdt	= date('Y-m-d H:i:s', $time1);	
		$this->enddt	= date('Y-m-d H:i:s', $time2);
		$this->enddtss	= date('Y-m-d H:i:s', $time3);
		$this->scheduletodo();
		m('flow')->initflow('meet')->meettodo(); //会议提醒的
		//$this->todologs();
		m('flowbill')->autocheck(); //自动审批作废
		m('reim')->chatpushtowx($this->enddtss); //REIM消息
		return 'success';
	}
	
	private function scheduletodo()
	{
		m('schedule')->gettododata();
		m('remind')->todorun();//单据提醒设置
	}
	
	//错误日志通知给管理员提醒
	private function todologs()
	{
		$opddt 	= date('Y-m-d H:i:s',time()-5*60);
		$rows  	= $this->db->getall("select `type`,count(1)stotal from `[Q]log` where `level`=2 and `optdt`>'$opddt' group by `type`");
		$str 	= '';
		foreach($rows as $k=>$rs)$str.=''.$rs['type'].'('.$rs['stotal'].'条);';
		
		if($str!='')m('todo')->add(1,'错误日志', $str.'请到[系统→系统工具→日志查看]下查看。');
	}
}