<?php
class Admin_model extends CI_Model
{
	public function __construct()
	{
		$this->load->database();
	}
	public function getOne($name)
	{
		$query = $this->db->get_where('cms_admin',array('name'=>$name));
		return $query->row_array();
	}
	public function add()//
	{

	}
	public function edit()
	{

	}
	public function delete()
	{

	}
}
?>