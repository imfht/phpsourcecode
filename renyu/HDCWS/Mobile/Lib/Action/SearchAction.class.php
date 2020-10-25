<?php

class SearchAction extends GlobalAction {

	public function index(){

		import('ORG.Util.Page');
		
		$keyword = I('keyword', '', '');

		$product = D('product');

    	$condition = ' where (name like "%' . $keyword . '%" or keywords like "%' . $keyword . '%" or description like "%' . $keyword . '%") and status = 1 ';

		$count = $product -> query('select count(*) counts from ' . C('DB_PREFIX') . 'product ' . $condition);

		$count = empty($count) ? 0 : $count[0]['counts'];
		
		$Page = new Page($count, 16);
		
		$show = $Page -> show();
		
		$sql = 'select * from ' . C('DB_PREFIX') . 'product ' . $condition . ' order by time desc limit '. $Page -> firstRow . ',' . $Page -> listRows;

		$list = $product -> query($sql);
		
		$this -> assign('list', $list);
		
		$this -> assign('pageLink', $show);
		
		$this -> assign('title', '搜索中心');

		$this -> display();

	}

}