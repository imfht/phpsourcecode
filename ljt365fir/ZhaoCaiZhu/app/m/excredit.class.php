<?php
class m_excredit extends base_m {
	public function primarykey() {
		return 'mid';
	}
	public function tableName() {
		return base_Constant::TABLE_PREFIX . 'exlog';
	}
	public function relations() {
		return array ();
	}
	public function insertlog($mid,$data) {
		if (! $mid) {
			$this->setError ( 0, "缺少必要参数" );
			return false;
		}
		$data['mid']=$mid;
		$data['extime']=$this->_time;

		 $rs = $this->insert($data);
		if ($rs)
			return true;
		return $data;
	}
	public function updatecache($mid,$sql) {
		if (! $mid) {
			$this->setError ( 0, "缺少必要参数" );
			return false;
		}
		 $this->update("mid={$mid}",$sql);
	}
	public function setexCredit($mid,$exchangecredit) {
		$rs = $this->update ( "mid={$mid}", "exchangecredit=exchangecredit+{$exchangecredit}" );
		if ($rs)
			return true;
		return false;
	}
}