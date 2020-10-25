<?php
@set_time_limit(0);
defined('iPHP') OR require (dirname(__FILE__).'/../../../iCMS.php');

// patch::$check_login = 0;//debug

return patch::upgrade(function(){
    $all = iDB::all("SELECT * FROM `#iCMS@__apps` WHERE `fields`!=''");
    foreach ($all as $idx => $row) {
      $id     = $row['id'];
      $fields = json_decode($row['fields'],true);
      $fields = get_field_array($fields);
      if(is_array($fields)){
        $fields = addslashes(cnjson_encode($fields));
        iDB::update('apps',array('fields'=>$fields),array('id'=>$id));
        $msg.='应用 id:'.$id.' 转换成功<iCMS>';
      }else{
        $msg.='<span class="label label-important">应用 id:'.$id.' 转换失败或者已经转换过了</span><iCMS>';
      }
    }
    $all = iDB::all("SELECT * FROM `#iCMS@__forms` WHERE `fields`!=''");
    foreach ($all as $idx => $row) {
      $id     = $row['id'];
      $fields = json_decode($row['fields'],true);
      $fields = get_field_array($fields);
      if(is_array($fields)){
        $fields = addslashes(cnjson_encode($fields));
        iDB::update('forms',array('fields'=>$fields),array('id'=>$id));
        $msg.='表单 id:'.$id.' 转换成功<iCMS>';
      }else{
        $msg.='<span class="label label-important">表单 id:'.$id.' 转换失败或者已经转换过了</span><iCMS>';
      }
    }
    $fields  = apps_db::fields('#iCMS@__prop');
    if(empty($fields['status'])){
      iDB::query("
ALTER TABLE `#iCMS@__prop`
ADD COLUMN `status` tinyint(1) unsigned NOT NULL DEFAULT '0';
      ");
        $msg.='增加属性状态<iCMS>';
    }
    return $msg;
});

function get_field_array($data) {
    $array = array();
    if($data)foreach ($data as $key => $value) {
      $output = array();
      if(is_array($value)){
        return false;
      }
      if($value=='UI:BR'){
          $output = array('type'=>'br');
      }else{
          parse_str($value,$output);
      }
      $output && $array[$key] = $output;
    }
    return $array;
}
