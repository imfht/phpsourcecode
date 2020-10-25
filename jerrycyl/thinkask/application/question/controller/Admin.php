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
namespace app\question\controller;
use app\common\controller\Base;

class Admin extends base
{
   public function lists(){

 	$where="";
 	if(input('search_field')){
 		$search_arr = explode("|", input('search_field'));
 		foreach ($search_arr as $k => $v) {
 			$where.= "qu.$v like '%".input('keyword')."%' OR ";
 		}
 	}

 	$order =input('_order')?input('_order')." ".input('_by'):"qu.question_id desc";
 	$where = rtrim($where," OR ");
  	$re = model('Base')->getpages('question',['where'=>$where,'alias'=>'qu','leftjoin'=>[[config('database.prefix').'users u','u.uid=qu.published_uid']],'order'=>$order,'list_rows'=>$_GET['list_rows']]);
	return $this->builder('table')
	->setPageTitle('列表')
	->setSearch(['question_id' => 'id', 'question_content' => '文章标题']) // 设置搜索参数
	->setTableName('question')
	->setPrimaryKey('question_id')
	->addOrder('question_id,add_time')
	->addColumn('question_id', 'id')
	->addColumn('question_content', '问题标题')
	->addColumn('answer_count', '回答')
	->addColumn('focus_count', '关注')
	->addColumn('view_count', '浏览')
	->addColumn('user_name', '作者')
	->addColumn('add_time', '发布时间')
	->addColumn('update_time', '最后更新')
	->addColumn('right_button', '操作', 'btn')
    ->addRightButtons(['edit', 'delete' => ['data-tips' => '删除后无法恢复。','field'=>'question_id']]) // 批量添加右侧按钮
    ->setRowList($data_list) // 设置表格数据
	->setRowList($re) // 设置表格数据
	->fetch();
  }

}
