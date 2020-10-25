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
class Approval extends AdminBase{
   /**
    * [index 内容审核]
    * @return [type] [description]
    */
    public function content(){
    	
    return $this->fetch('admin/approval/content');
  }
  /**
   * [user 用户审核]
   * @return [type] [description]
   */
  public function user(){
    return $this->fetch('admin/approval/user');
  }
  /**
   * [report_list 用户举报]
   * @return [type] [description]
   */
  public function report_list(){
    return $this->fetch('admin/approval/report_list');
  }

}