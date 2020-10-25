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
namespace app\admin\controller;
use app\common\controller\AdminBase;
use think\Controller;

class Nv extends AdminBase
{
  public function index(){
    //分类数据
    $catinfo = $this->getbase->getone('nv_index_cat',['where'=>["id"=>input('catid')],'field'=>'title']);
    $map = $this->getMap();
    if(input('catid')){
      $map['catid'] = input('catid');
    }
    $order = $this->getOrder();
    $data = $this->getbase->getdb('nv_index')
                          ->order($order)
                          ->where($map)
                          ->paginate();
    // 分页数据
    $page = $data->render();
      // 使用ZBuilder构建数据表格
    return $this->builder('table')
    ->setPageTitle('导航列表')
    ->setSearch(['id' => 'id', 'title' => '导航标题']) // 设置搜索参数
    ->setTableName('nv_index')
    ->setPrimaryKey('id')
    ->addOrder('id,sort')
    ->addColumn('id', 'id')
    ->addColumn('title', '导航标题')
    ->addColumn('c', 'controller')
    ->addColumn('m', 'module')
    ->addColumn('a', 'action')
    ->addColumn('url', 'url')
    ->addColumn('sort', '排序')
    ->addColumn('status', '状态',"switch")
    ->addColumn('target', '打开方式')
    ->addColumn('parentid', 'parentid')
    ->addColumn('cat-title', '所属分类','',$catinfo['title'])
    ->addColumn('right_button', '操作', 'btn')
    ->addTopButton('edit',['class'=>"btn btn-default",'title'=>'新加','href'=>'/admin/nv/edit/catid/'.input('catid')]) // 添加顶部按钮

    ->addRightButtons(['edit'=>['href'=>'/admin/nv/edit/id/__id__/catid/'.input('catid')], 'delete' => ['data-tips' => '删除后无法恢复。','field'=>'id']]) 
    ->setRowList($data) // 设置表格数据
    ->setRowList($re) // 设置表格数据
    ->setPages($page) // 设置分页数据
    ->fetch();
  }
 public function edit(){
  if($id = input('id')){
    $nvinfo = $this->getbase->getdb('nv_index')->where("id = '{$id}'")->find();
    extract($nvinfo);
  }
  $cats =$this->getbase->getdb('nv_index_cat')->select();
  return $this->builder('form')
    ->setUrl(url('systems/ajax/tmkedit'))
    ->addHidden('field','id')
    ->addHidden('gourl','/admin/nv/index/catid/'.input('catid'))
    ->addHidden('id',$id)
    ->addHidden('table','nv_index')
    ->setPageTitle('编辑导航')
    ->addText('title', '标题', '',$title)
    ->addSelect('catid','分类','',formatArr($cats,'id','title'),input('catid'))
    ->addText('m', 'm','',$m)
    ->addText('c', 'c','',$c)
    ->addText('a', 'a','',$a)
    ->addText('url', 'url地址','',$url)
    ->addNumber('sort', '排序','',$sort)

    ->addSelect('target', '打开方式','',['_self'=>'同框架','_blank'=>'新窗口','_parent'=>'父框架中','_top'=>'整个窗口中'],$target)
    ->addRadio('status', '状态','',[0=>'隐藏',1=>'开启'],$status)
    ->fetch(); 

  }
  //导航分类
  public function catlist(){
    $map = $this->getMap();
    $order = $this->getOrder();
    $data = $this->getbase->getdb('nv_index_cat')
                          ->order($order)
                          ->where($map)
                          ->paginate();
    // 分页数据
    $page = $data->render();
      // 使用ZBuilder构建数据表格
    return $this->builder('table')
    ->setPageTitle('导航列表')
    ->setSearch(['id' => 'id', 'title' => '分类标题']) // 设置搜索参数
    ->setTableName('nv_index_cat')
    ->setPrimaryKey('id')
    ->addOrder('id')
    ->addColumn('id', 'id')
    ->addColumn('title', '分类标题')
    ->addColumn('remark', '备注')
    ->addColumn('id_parentid', 'parentid')
    ->addColumn('right_button', '操作', 'btn')
    ->addTopButton('edit',['class'=>"btn btn-default",'title'=>'新加','href'=>'/admin/nv/catedit']) // 添加顶部按钮
    ->addRightButton('list', ['icon'=>'fa fa-gg ','title'=>'导航','class'=>'btn btn-default btn-xs','href'=>"/admin/nv/index/catid/__id__"])
    ->addRightButtons(['edit'=>['href'=>'/admin/nv/catedit/catid/__id__'], 'delete' => ['data-tips' => '删除后无法恢复。','field'=>'id']]) // 批量添加右侧按钮
    ->setRowList($data) // 设置表格数据
    ->setRowList($re) // 设置表格数据
    ->setPages($page) // 设置分页数据
    ->fetch();



  }
  /**
   * [catedit 分组修改]
   * @return [type] [description]
   */
  public function catedit(){
  	if($id = input('catid')){
      $cateinfo = model('Base')->getone('nv_index_cat',['where'=>['id'=>$id]]);
      extract($cateinfo);
    }
   $cats =$this->getbase->getdb('nv_index_cat')->select();
   return $this->builder('form')
    ->setUrl(url('systems/ajax/tmkedit'))
    ->addHidden('field','id')
    ->addHidden('id',$id)
    ->addHidden('table','nv_index_cat')
    ->addHidden('gourl','/admin/nv/catlist')
    ->setPageTitle('编辑导航分类')
    ->addText('title', '标题', '',$title)
    ->addTextarea('remark', '备注', '',$remark)
    ->addSelect('id_parentid','分类','',formatArr($cats,'id','title',[0=>'顶级分类']),$id_parentid)
    ->fetch(); 
    
    
   
  }
 
  
}
