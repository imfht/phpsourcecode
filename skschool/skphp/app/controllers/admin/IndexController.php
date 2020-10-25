<?php
namespace App\Controllers\Admin;
use Skschool\Controller;

class IndexController extends Controller {
	
	public function index()
	{
		$this->assign('name', 'admin\IndexController@index');
		$this->display();
	}
	
}