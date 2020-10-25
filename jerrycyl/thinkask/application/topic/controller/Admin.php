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
namespace app\topic\controller;
use app\common\controller\AdminBase;

class Admin extends AdminBase
{
	   /**
		 * [list 列表]
		 * @return [type] [description]
		 */
	  public function lists(){
			 $where="";
		 	if(input('search_field')){
		 		$search_arr = explode("|", input('search_field'));
		 		foreach ($search_arr as $k => $v) {
		 			$where.= "qu.$v like '%".input('keyword')."%' OR ";
		 		}
		 	}

		 	$order =input('_order')?input('_order')." ".input('_by'):"topic_id desc";
		 	$where = rtrim($where," OR ");
		  	$re = model('Base')->getpages('topic',['where'=>$where,'alias'=>'qu','leftjoin'=>[[config('database.prefix').'users u','u.uid=qu.uid']],'order'=>$order,'list_rows'=>$_GET['list_rows']]);
		  	// 使用ZBuilder构建数据表格
			return $this->builder('table')
			->setPageTitle('列表')
			->setSearch(['topic_id' => 'id', 'topic_title' => '话题标题']) // 设置搜索参数
			->setTableName('topic')
			->addColumn('topic_id', 'id')
			 ->addOrder('topic_id,discuss_count,focus_count')
			->setPrimaryKey('topic_id')
			->addColumn('topic_title', '话题标题')
			->addColumn('discuss_count', '讨论')
			->addColumn('focus_count', '关注')
			->addColumn('right_button', '操作', 'btn')
		    ->addRightButtons(['edit', 'delete' => ['data-tips' => '删除后无法恢复。','field'=>'topic_id']]) // 批量添加右侧按钮
		    ->setRowList($data_list) // 设置表格数据
			->setRowList($re) // 设置表格数据
			->fetch();
	  }
	  /**
	   * [parent 根话题 ]
	   * @return [type] [description]
	   */
	 public function  parent(){

	 
	  	return $this->fetch('topic/admin/parent');
	  }
	  /**
	   * [creat 新建话题]
	   * @return [type] [description]
	   */
	 public function  creat(){

	 
	  	return $this->fetch('topic/admin/creat');
	  }

}
