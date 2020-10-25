<?php 
class indexClassAction extends ActionNot{
	
	public function initAction()
	{
		$this->mweblogin(0, false);
	}
	
	public function defaultAction()
	{
		$this->title = getconfig('apptitle',$this->bd6('5L!h5ZG8T0E:'));
		$rows	 = $this->option->getall('pid=-101','`num`,`value`');
		$authkey = $yuming = $enddt = '';
		foreach($rows as $k1=>$rs1){
			if($rs1['num']=='auther_authkey')$authkey = $rs1['value'];
			if($rs1['num']=='auther_yuming')$yuming = $rs1['value'];
			if($rs1['num']=='auther_enddt')$enddt = $rs1['value'];
		}
		if(isempt($yuming) || isempt($enddt) || isempt($authkey))return $this->bd6('57O757uf5pyq562!5o6I5LiN6IO95L2.55So');
		if($this->jm->uncrypt($enddt)<$this->date)return $this->bd6('57O757uf562!5o6I5bey5Yiw5pyf');
		$ym 	= $this->jm->uncrypt($yuming);
		$ho		= $this->bd6('LDEyNy4wLjAuMSxsb2NhbGhvc3Qs');
		$ho1 	= ','.HOST.',';
		if(!contain($ho, $ho1) && !contain(','.$ym.',',$ho1))return str_replace('1', HOST, $this->bd6('MeWfn!WQjeacquetvuaOiOS4jeiDveS9v!eUqA::'));
		$this->assign('xhauthkey', getconfig('authkey', $authkey));
		$this->assign('tplmess', $this->option->getval('wxgzh_tplmess'));
	}
	
	private function bd6($str)
	{
		return $this->jm->base64decode($str);
	}
	
	public function editpassAction()
	{
		
	}
	
	public function testAction()
	{
		
	}
	
	
	
	/**
	*	用户信息
	*/
	public function userinfoAction()
	{
		$uid = (int)$this->get('uid');
		$urs = m('admin')->getone($uid, '`id`,`name`,`deptallname`,`ranking`,`tel`,`email`,`mobile`,`sex`,`face`');
		if(!$urs)exit('not user');
		
		//权限过滤
		$flow = m('flow')->initflow('user');
		$ursa = $flow->viewjinfields(array($urs));
		$urs  = $ursa[0];
		
		if(isempt($urs['face']))$urs['face']='images/noface.png';
		$this->assign('arr', $urs);
	}
	
	public function companyAction()
	{
		$this->assign('carr', m('admin')->getcompanyinfo($this->adminid));
		$this->assign('ofrom', $this->get('ofrom'));
	}
}