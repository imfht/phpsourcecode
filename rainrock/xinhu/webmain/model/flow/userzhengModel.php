<?php
//证书
class flow_userzhengClassModel extends flowModel
{
	
	
	public function flowrsreplace($rs)
	{
		if($rs['edt'] && $rs['edt']<$this->rock->date){
			$rs['ishui'] = 1;
			$rs['explain'].='已过期';
		}
		
		return $rs;
	}
	
}