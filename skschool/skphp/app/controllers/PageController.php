<?php
namespace App\Controllers;
use Skschool\Controller;

class PageController extends Controller {
	
	public function index()
	{
		$count = DB()->query("select count(*) as totalnum from articles;");
		$count = $count[0]['totalnum'];
		
		$page = new \Skschool\Page();
		$page->init($count, 5);
		
		$url = URL('/page-%5BPAGE%5D.html');
		$show = $page->show($url);
		
		$list = DB()->query("select * from articles order by id desc limit ".$page->firstRow.','.$page->listRows.";");
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	
}