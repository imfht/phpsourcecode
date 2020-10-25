<?php
 
class mode_workClassAction extends inputAction{
	
	
	protected function savebefore($table, $arr, $id, $addbo){
		
	}
	
	
	protected function saveafter($table, $arr, $id, $addbo){
		
	}
	
	public function projectdata()
	{
		$rows 	= m('project')->getall('id>0 and status in(0,3)','`id`,`type`,`title`,`progress`','optdt desc');
		$arr	= array();
		foreach($rows as $k=>$rs){
			$arr[] = array(
				'name' => '['.$rs['type'].']'.$rs['title'].'('.$rs['progress'].'%)',
				'value' => $rs['id']
			);
		}
		return $arr;
	}
}	
			