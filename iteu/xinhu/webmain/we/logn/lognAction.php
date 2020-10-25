<?php 
class lognClassAction extends ActionNot{
	
	
	public function initAction()
	{
		$this->mweblogin(0, true);
	}
	
	public function defaultAction()
	{
		
		$randkey = $this->get('randkey');
		$dfrom   = $this->get('dfrom','reim');
		if(isempt($randkey))exit('无效访问');
		
		$db  = m('admin');
		$urs = $db->getone($this->adminid,'name,face');
		$urs['face'] = $db->getface($urs['face']);
		$urs['randkey'] = $randkey;
		
		$this->option->setval($randkey, '0');
		
		$this->assign('urs', $urs);
		$this->assign('dfrom', $dfrom);
		
	}

}