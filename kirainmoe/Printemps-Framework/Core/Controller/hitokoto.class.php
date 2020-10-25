<?php
class hitokotoController extends Printemps{
	function __construct(){
		parent::__construct();
	}

	function index(){
		header("Content-Type:text/json;charset=UTF-8");
		$query = $this->db->select("*","hitokoto",array("ORDER"=>"RAND()"));
		$array = $this->db->fetch($query,'assoc');
		echo json_encode($array);
	}
}