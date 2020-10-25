<?php
 //resource 插件自动生成示例的view control,请访问index.php?act=demo
class demoControl extends Control
{
  public  function  index(){

      //查询所有客户预约的贵宾
      $time=date('m月d日 H:i');
      //筛选预约表
      $main= Plugin('T')->include_file('demo',array('time'=>$time));
$this->display_layout($main);
}


}