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
namespace app\Admin\controller;
use app\common\controller\AdminBase;
class Category extends AdminBase
{
 /**
  * [index 用户管理]
  * @return [type] [description]
  */
  public function index(){
      $map = $this->getMap();
      $order = $this->getOrder();

      $re = model('Base')->getpages('category',['where'=>$map,'order'=>$order,'list_rows'=>$_GET['list_rows']]);
      return $this->builder('table')
      ->setPageTitle('分类列表')
      ->setSearch(['id' => '分类id', 'title' => '分类名']) // 设置搜索参数
      ->setTableName('category')
      ->setPrimaryKey('id')
      ->addOrder('id')
      ->addColumn('id', 'id')
      ->addColumn('parent_id', 'parent_id')
      ->addColumn('title', '标题')
      ->addColumn('icon', 'icon图标')
      ->addColumn('sort', '排序')
      ->addColumn('right_button', '操作', 'btn')
      ->addRightButtons(['edit', 'delete' => ['data-tips' => '删除后无法恢复。','field'=>'id']]) // 批量添加右侧按钮
      ->setRowList($data_list) // 设置表格数据
      ->setRowList($re) // 设置表格数据
      ->fetch();
   }
  public function edit(){
    $parent = formatArr(model('base')->getall('category'),'id','title',[0=>'顶级分类']);
     return $this->builder('form')
      ->setPageTitle('添加分类')
      ->addText('title', '标题')
      ->addSelect('parent_id', '选择父级标签', '', $parent)
      ->addText('icon', 'icon图标')
      ->addNumber('sort', '排序')
      ->fetch(); 
  }

 

}
