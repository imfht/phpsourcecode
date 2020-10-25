<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

/**
 * 任务
 * @author sigmazel
 * @since v1.0.2
 */
class _cron{
	//添加
	public function insert($data){
		global $config;
		
		if($config['redis']) $this->insert_redis($data);
		else $this->insert_mysql($data);
	}
	
	//添加至redis
	public function insert_redis($data){
		$redis = new \Redis();
		$redis->connect('127.0.0.1', 6379);
		
		$redis->rPush('cron', $data['ACTION'].'|'.json_encode($data['DATA']));
	}
	
	//添加至mysql
	public function insert_mysql($data){
		global $db;
		
		$data['DATA1'] = $data['DATA2'] = $data['DATA3'] = $data['DATA4'] = $data['DATA5'] = '';
		
		$datas = str_split($data['DATA'], 200);
		for ($i = 0; $i < count($datas); $i++) $data['DATA'.($i + 1)] = $datas[$i];
		
		unset($data['DATA']);
		
		$db->insert('tbl_cron', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db, $config;
		
		if($config['redis']) return;
		
		$data['DATA1'] = $data['DATA2'] = $data['DATA3'] = $data['DATA4'] = $data['DATA5'] = '';
		
		$datas = str_split($data['DATA'], 200);
		for ($i = 0; $i < count($datas); $i++) $data['DATA'.($i + 1)] = $datas[$i];
		
		unset($data['DATA']);
		
		$db->update('tbl_cron', $data, "CRONID = '{$id}'");
	}
	
	//获取任务列表
	public function get_list($limit, $wheresql = ''){
		global $db;
		
		$rows = array();
		$temp_query = $db->query("SELECT * FROM tbl_cron WHERE SEND = 0 {$wheresql} LIMIT 0, {$limit}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['DATA'] = $row['DATA1'].$row['DATA2'].$row['DATA3'].$row['DATA4'].$row['DATA5'];
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
}
?>