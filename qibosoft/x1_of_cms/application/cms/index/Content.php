<?php
namespace app\cms\index;

use app\common\controller\index\C; 


//内容页与列表页
class Content extends C
{
	
	/**
	 * 列表页
	 * {@inheritDoc}
	 * @see \app\common\controller\index\C::index()
	 */
	public function index($fid=0,$mid=0){
	    return parent::index($fid,$mid);
	}
	
	/**
	 * 内容详情页
	 * {@inheritDoc}
	 * @see \app\common\controller\index\C::show()
	 */
	public function show($id){
	    $this->haibao = 'content/haibao';  //默认海报模板路径
	    return parent::show($id);
	}
	
}
