<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\article\controller;
use app\common\controller\AdminBase;

class Admin extends AdminBase
{
  public function lists(){
  	$where="";
 	if(input('search_field')){
 		$search_arr = explode("|", input('search_field'));
 		foreach ($search_arr as $k => $v) {
 			$where.= "qu.$v like '%".input('keyword')."%' OR ";
 		}
 	}

 	$order =input('_order')?input('_order')." ".input('_by'):"qu.id desc";
 	$where = rtrim($where," OR ");
  	$re = model('Base')->getpages('article',['where'=>$where,'alias'=>'qu','leftjoin'=>[[config('database.prefix').'users u','u.uid=qu.uid']],'order'=>$order,'list_rows'=>$_GET['list_rows']]);
  	// 使用ZBuilder构建数据表格
	return $this->builder('table')
	->setPageTitle('列表')
	->setSearch(['id' => 'id', 'title' => '文章标题']) // 设置搜索参数
	->setTableName('article')
	->addOrder('id,add_time')
	->addColumn('id', 'views')
	->addColumn('title', '文章标题')
	->addColumn('comments', '评论')
	->addColumn('views', '浏览')
	->addColumn('user_name', '作者')
	->addColumn('add_time', '发布时间')
	->addColumn('right_button', '操作', 'btn')
    ->addRightButtons(['edit', 'delete' => ['data-tips' => '删除后无法恢复。','field'=>'id']]) // 批量添加右侧按钮
    ->setRowList($data_list) // 设置表格数据
	->setRowList($re) // 设置表格数据
	->fetch();
  }

}
