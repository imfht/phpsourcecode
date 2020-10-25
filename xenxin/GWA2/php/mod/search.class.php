<?php

# work with ../ctrl/search.php
# Sun Jun  7 09:52:49 CST 2015
# by wadelau@ufqi.com
# refers: -R/j2SO 

if(!defined('__ROOT__')){
	define('__ROOT__', dirname(dirname(__FILE__)));
}
require_once(__ROOT__.'/inc/webapp.class.php'); 

class Search extends WebApp{

	var $mainTables = array("livetbl"=>array("id"=>"id", "title"=>"name", "searchkey"=>"title"), 
		"producttbl"=>array("id"=>"id","pname"=>"name", "searchkey"=>"pname"), 
		"pairtbl"=>array("id"=>"id", "ititle"=>"name", "searchkey"=>"ititle"), 
		"wanttbl"=>array("id"=>"id", "wname"=>"name", "searchkey"=>"wname"),
		"usertbl"=>array("id"=>"id", "nickname"=>"name", "searchkey"=>"nickname")
		);

	//-
	function __construct(){
		
		parent::__construct();
		
	}

	//-
	function getList($kw, $scope=''){
		$sql = ""; $pagesize = 500;
		foreach($this->mainTables as $k=>$v){
			#print __FILE__.": k:[$k] , v:[$v].";
			if($scope != ''){
				if(strpos($k, $scope) === false){ continue; }	
			}
			$sql .= "select ";
			foreach($v as $k2=>$v2){
				if($k2 == 'searchkey'){ continue; }
				else if($v2 == 'name'){
					$sql .= "concat('".$k."_',$k2) as $v2,"; 		
				}
				else{
					$sql .= "$k2 as $v2,"; 		
				}
			}
			$sql = substr($sql, 0, strlen($sql)-1)." from ".GConf::get('tblpre').$k." ";
			$sql .= "where ".$v['searchkey']." like '%$kw%' ";
			$sql .= "union all ";
		}
		$sql = substr($sql, 0, strlen($sql)-strlen("union all "))." ";
		$sql .= " order by id desc limit 0, $pagesize";
		#error_log(__FILE__.": sql: $sql , kw:$kw , sql:$sql .");
		#print(__FILE__.": sql: $sql , kw:$kw , sql:$sql .");
		$hm = $this->execBy($sql);
		#print_r($hm);

		return $hm;

	}

}

?>
