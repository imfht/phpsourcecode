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
class Adv extends AdminBase
{
  public function lists(){
    //当前位置ID下面的广告例表
    $postion_id = (int)input("postion_id");
    if(!$postion_id) $this->error('没有指定广告位置');
    $postioninfo = $this->getbase->getone('adv_postion',['field'=>"name"]);
     $map = $this->getMap();
    $order = $this->getOrder();
    $data = $this->getbase->getdb('adv')
                          ->order($order)
                          ->where($map)
                          ->paginate();
    // 分页数据
    $page = $data->render();
      // 使用ZBuilder构建数据表格
    return $this->builder('table')
    ->setPageTitle('广告列表')
    ->setSearch(['id' => 'id', 'title' => '导航标题']) // 设置搜索参数
    ->setTableName('adv')
    ->setPrimaryKey('id')
    ->addOrder('id')
    ->addColumn('id', 'id')
    ->addColumn('name', '广告位名称')
    ->addColumn('tagname', '广告位标题识')
    ->addColumn('ad_type', '广告类型','','',['1' =>'图片','2' => 'html','0' => '未设置'])
     ->addColumn('timeset', '时间限制', '', '', ['0' =>'永不过期','1' => '在设内时间内有效'])
     ->addColumn('start_time', '开始时间')
     ->addColumn('end_time', '结束时间')
     ->addColumn('content', '广告内容')
     ->addColumn('create_time', '创建时间')
     ->addColumn('update_time', '修改时间')
     ->addColumn('expcontent', '过期显示内容')
    ->addColumn('status', '状态',"switch")
    ->addColumn('right_button', '操作', 'btn')
    ->addTopButton('edit',['class'=>"btn btn-default",'title'=>'添加广告','href'=>"/admin/adv/edit/postion_id/{$postion_id}"]) // 添加顶部按钮
    ->addRightButtons(['edit'=>['href'=>"/admin/adv/edit/postion_id/{$postion_id}/id/__id__/"], 'delete' => ['data-tips' => '删除后无法恢复。','field'=>'id']]) 
    ->setRowList($data) // 设置表格数据
    ->setPages($page) // 设置分页数据
    ->fetch();
      
  }
  public function edit(){
    $postion_id = (int)input('postion_id');
    $id = (int)input('id');
    if($id){
      $adv = $this->getbase->getone('adv',['where'=>['id'=>$id]]);
      extract($adv);
    }
      return $this->builder('form')
      ->setUrl(url('systems/ajax/tmkedit'))
      ->addHidden('field','id')
      ->addHidden('gourl','/admin/adv/lists/postion_id/'.input('postion_id'))
      ->addHidden('id',$id)
      ->addHidden('table','adv')
      ->setPageTitle('编辑广告')
      ->addText('name','广告位名称', '',$name)
      ->addText('tagname','广告位标识', '',$tagname)

      ->addRadio('ad_type', '广告类型','',[1=>'图片',2=>'html'],$ad_type?$ad_type:1)
      ->setTrigger('ad_type', '1', 'img')
      ->setTrigger('ad_type', '2', 'content')

      ->addRadio('timeset', '时间限制','',[0=>'永不过期',1=>'在设内时间内有效'],$timeset?$timeset:0)
      ->setTrigger('timeset', '1', 'start_time,end_time')
      ->addDaterange('start_time,end_time', '日期范围',"{$start_time},{$end_time}")

      ->addImage('img', '广告图片','',$img)
      ->addSummernote('content', '广告内容','',$content)
      // ->addTextarea('expcontent', '过期显示内容','',$expcontent)
      ->setTrigger('city', 'gz', 'zipcode')
      ->setTrigger('city', 'sz', 'mobile')
      ->addRadio('status', '状态','',[0=>'禁用',1=>'开启'],empty($status)?1:$status)
      ->addValidate([
                  'name'          => 'require|max:50',
                  'tagname'       => 'require|max:50',
                    ],[
                  'name.require'  => '标题为必填',
                  'name.max'      => '标题不能超过50个字符',
                  'tagname.max'   => '广告位标识不能超过50个字符',
                  'tagname.require'  => '广告位标识为必填',


                    ])
      ->fetch(); 
  }

  public function advpostion(){
    $map = $this->getMap();
    $order = $this->getOrder();
    $data = $this->getbase->getdb('adv_postion')
                          ->order($order)
                          ->where($map)
                          ->paginate();
    // 分页数据
    $page = $data->render();
      // 使用ZBuilder构建数据表格
    return $this->builder('table')
    ->setPageTitle('位置列表')
    // ->setSearch(['id' => 'id', 'title' => '导航标题']) // 设置搜索参数
    ->setTableName('adv_postion')
    ->setPrimaryKey('id')
    ->addOrder('id')
    ->addColumn('id', 'id')
    ->addColumn('name', '位置名称')
    ->addColumn('status', '状态',"switch")
    ->addColumn('right_button', '操作', 'btn')
    ->addTopButton('edit',['class'=>"btn btn-default",'title'=>'添加广告位置','href'=>'/admin/adv/editpostion/']) // 添加顶部按钮
    ->addRightButtons([
                        'list' => ['title'=>'广告列表','href'=>'/admin/adv/lists/postion_id/__id__/','icon'=>'fa fa-outdent','class'=>'btn btn-default btn-xs'],
                        'edit'=>['href'=>'/admin/adv/editpostion/id/__id__/'], 
                        'delete' => ['data-tips' => '删除后无法恢复。','field'=>'id'],
                        
                        ]) 
    ->setRowList($data) // 设置表格数据
    ->setRowList($re) // 设置表格数据
    ->setPages($page) // 设置分页数据
    ->fetch();
  }
  public function editpostion(){
    if($id = input('id')){
      $advcat = $this->getbase->getdb('adv_postion')->where("id = '{$id}'")->find();
      extract($advcat);
    }
    return $this->builder('form')
      ->setUrl(url('systems/ajax/tmkedit'))
      ->addHidden('field','id')
      ->addHidden('gourl','/admin/adv/advpostion/')
      ->addHidden('id',$id)
      ->addHidden('table','adv_postion')
      ->setPageTitle('广告位置')
      ->addText('name', '位置名称', '',$name)
      ->addValidate([
                  'name'          => 'require|max:50',
                    ],[
                  'name.require'  => '位置名称为必填',
                  'name.max'      => '位置名称不能超过50个字符',
                    ])
      ->addRadio('status', '状态','',[0=>'隐藏',1=>'开启'],$status)
      ->fetch(); 
  
  }



}
