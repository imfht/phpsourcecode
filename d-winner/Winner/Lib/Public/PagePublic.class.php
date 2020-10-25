<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

//簡單分頁类
class PagePublic extends Action {
	public $rows;
	public $offset;
	public $theme = array(
		'config'=>array('frist'=>'第一页','last'=>'上一页','next'=>'下一页','end'=>'最后一页'),
		'theme'=>'<span class="tpt"><span class="rpl">%frist% %last% %next% %end%</span><span class="rpr">显示%from%到%to%,共%total%记录</span></span>',
	);
	
	//獲取分頁
	/*
	$mode		要获取记录数的模型
	*/
	public function show($total,$rows=10){
		$page = intval(I('page'));
		$page = $page ? $page : 1;
		$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		$this->rows = $rows;
		$this->offset = ((int)$page-1)*$this->rows;
		$t_page = ceil($total/$this->rows);
		
		$frist=''; $end=''; $last=''; $next='';
		if($page>1 && $t_page>1){
			$frist = '<a href="javascript:toPage(\''.$url.'&page=1\');" style="margin-right:6px;">'.$this->theme['config']['frist'].'</a>';
			$last = '<a href="javascript:toPage(\''.$url.'&page='.($page-1).'\');" style="margin-right:6px;">'.$this->theme['config']['last'].'</a>';
		}
		if($t_page>1 && $page<$t_page){
			$next = '<a href="javascript:toPage(\''.$url.'&page='.($page+1).'\');" style="margin-right:6px;">'.$this->theme['config']['next'].'</a>';
			$end = '<a href="javascript:toPage(\''.$url.'&page='.$t_page.'\');" style="margin-right:6px;">'.$this->theme['config']['end'].'</a>';
		}
		$from = (int)$this->offset+1;
		$to = (int)$this->offset+(int)$this->rows;
		$pageStr = str_replace(
			array('%frist%','%last%','%next%','%end%','%from%','%to%','%total%'),
            array($frist,$last,$next,$end,$from,$to,$total),				
			$this->theme['theme']
		);

		return $pageStr;
	}
}