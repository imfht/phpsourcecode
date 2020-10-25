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
class User extends AdminBase
{
 /**
  * [index 用户管理]
  * @return [type] [description]
  */
  public function index(){
    $map = $this->getMap();
    $order = $this->getOrder();
    $re = model('Base')->getpages('users',['where'=>$map,
                                              'alias'=>'u',
                                              'leftjoin'=>[['users_group ug','u.group_id=ug.group_id']],
                                              'order'=>$order,
                                              'list_rows'=>$_GET['list_rows']
                                              ]);
    return $this->builder('table')
    ->setPageTitle('用户列表')
    ->setSearch(['uid' => '用户id', 'user_name' => '姓名']) // 设置搜索参数
    ->setTableName('users')
    ->setPrimaryKey('uid')
    ->addOrder('uid')
    ->addColumn('uid', 'id')
    ->addColumn('user_name', '姓名')
    ->addColumn('email', 'email')
    ->addColumn('mobile', '用户手机')
    ->addColumn('avatar_file', '头像文件')
    ->addColumn('sex', '性别')
    ->addColumn('birthday', '生日')
    ->addColumn('reg_ip', '注册IP')
    ->addColumn('last_login', '最后登录时间')
    ->addColumn('last_ip', '最后登录 IP')
    ->addColumn('online_time', '在线时间')
    ->addColumn('right_button', '操作', 'btn')
      ->addRightButtons(['edit', 'delete' => ['data-tips' => '删除后无法恢复。','field'=>'uid']]) // 批量添加右侧按钮
      ->setRowList($data_list) // 设置表格数据
    ->setRowList($re) // 设置表格数据
    ->fetch();
  }
  public function creat_thumb(){
    // for ($i=1; $i < 2000; $i++) { 
    //  $name = 1;
    // $data['path'] ="/avatar/avatar{$i}.jpg"; 
    // $data['ext'] ="jpg" ;
    // $data['up_type'] ="oss"; 
    // $data['mime'] ="image/jpg"; 
    // $this->getbase->getadd('attachment',$data);
    // }
    
  }
  public function edit(){
    $id = $this->request->only(['id']);
    $id = (int)$id['id'];
    if($id>0){
      $this->assign($users = model('Base')->getone('users',['where'=>['uid'=>$id],'cache'=>false]));
      $this->assign($users_attrib = model('Base')->getone('users_attrib',['where'=>['uid'=>$id],'cache'=>false]));
    }
    $this->assign('jobs',$province=model('Base')->getall('jobs'));
    $this->assign('category',$category=model('Base')->getall('users_group'));
   return $this->fetch('admin/user/edit');
  }
  /**
   * [group 组管理]
   * @return [type] [description]
   */
  public function group(){
  	$map = $this->getMap();
    $order = $this->getOrder();
    $re = model('Base')->getpages('users_group',['where'=>$map,
                                              'order'=>$order,
                                              'list_rows'=>$_GET['list_rows']
                                              ]);
    return $this->builder('table')
    ->setPageTitle('用户组列表')
    ->setSearch(['group_id' => 'id']) // 设置搜索参数
    ->setTableName('users_group')
    ->setPrimaryKey('group_id')
    ->addOrder('group_id')
    ->addColumn('group_id', 'id')
    ->addColumn('type', '类型0-会员组 1-系统组')
    ->addColumn('group_name', '组名')
    ->addColumn('right_button', '操作', 'btn')
    ->addRightButtons(['edit', 'delete' => ['data-tips' => '删除后无法恢复。','field'=>'uid'],'custor'=>[ 'title' => '授权','icon'  => 'fa fa-shield','class'=>'btn btn-xs btn-success',]]) // 批量添加右侧按钮
    ->setRowList($data_list) // 设置表格数据
    ->setRowList($re) // 设置表格数据
    ->fetch();
  	return $this->fetch('admin/user/group');
  }
  public function editgroup(){
    $data = $this->request->param();
    if($data['group_id']){
      $this->assign(model('base')->getone('users_group',['where'=>['group_id'=>$data['group_id']]]));
    }
    return $this->fetch('admin/user/editgroup');

  }
  /**
   * [invites 批量邀请]
   * @return [type] [description]
   */
  public function invites(){
  	return $this->fetch('admin/user/invites');
  }
  /**
   * [job 职位管理]
   * @return [type] [description]
   */
  public function job(){

  	$this->assign('job',$job=model('Base')->getall('jobs'));
  	// show($job);
	return $this->fetch('admin/user/job');
  }
 

}
