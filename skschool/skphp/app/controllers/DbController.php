<?php
namespace App\Controllers;
use Skschool\Controller;

class DbController extends Controller {
	
	public function skphp()
	{
		/**
		 * p方法  格式化打印数据
		 * 
		 *  table： articles 
		 * fields：id，content
		 */ 
		
		/* 
		 * 查询
		 */
		$list = DB()->query('SELECT * FROM articles');
		p($list);
				
		/*
		 * 增加
		$status = DB()->execute("INSERT INTO articles VALUES (NULL,'test')");
		echo $status;
		
		$id = DB()->insert('articles',array('content'=>'test'));
		echo $id;
		*/
		
		// 更多请自己查看Mysql类
		
	}
	
	public function laravel(){


		/*
		 * 查询所有的记录
		 * */
		
		$list = \App\Models\Article::all()->toArray();
		p($list);
		
		/*
		 * 查询一条记录并转化为数组
		$list = \App\Models\Article::first()->toArray();
		p($list);
		 */
		

		/*
		 * 查询指定记录
		 * 
		$list = \App\Models\Article::find(1)->toArray();
		p($list);
		 */
		
		/*
		 * 删除指定记录
		$status = \App\Models\Article::where('id', '=', 11)->delete();;
		p($status);
		*/

		// 更多请自己查看Eloquent ORM 开发手册
		
	}
	
}