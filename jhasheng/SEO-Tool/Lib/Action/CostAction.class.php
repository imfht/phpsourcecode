<?php
/**
 * @name 消费管理
 */
class CostAction extends CommonAction
{
	public function index()
	{

	}

	public function lists()
	{
		$webid = I('webid',0,int);
		$date = I('date',strtotime(date('Y-m-d')),int);

		$where = array(
			'w.webid'=>$webid,
			'c.costdate'=>1379347200
			);
		$webinfo = M('webinfo w');
		$rs = $webinfo->join('sc_keywords AS k ON w.webid = k.webid')->join('sc_costlog AS c ON c.keyid = k.keyid')->where($where)->sum('cost');
		//echo $webinfo->getLastSql();
		//var_dump($rs);
		$this->display();
	}
}