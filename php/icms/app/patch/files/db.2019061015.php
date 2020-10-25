<?php
@set_time_limit(0);
defined('iPHP') OR require (dirname(__FILE__).'/../../../iCMS.php');

return patch::upgrade(function(){
    $date = date("Ymd");
    iDB::query("CREATE TABLE `#iCMS@__spider_rule_".$date."` LIKE `#iCMS@__spider_rule` ");
    if(iDB::check_table('spider_rule_'.$date)){
      iDB::query("INSERT INTO `#iCMS@__spider_rule_".$date."` SELECT * FROM `#iCMS@__spider_rule`");
    }
    //旧版本
    $oldArray = array(
        'format','cleanhtml','mergepage','autobreakpage',
        'trim','filter','json_decode','array',
        'capture','download','img_absolute'
    );
    $all = iDB::all("SELECT * FROM `#iCMS@__spider_rule`");
    foreach ($all as $idx => $row) {
      $rid   = $row['id'];
      $rule  = stripslashes_deep(unserialize($row['rule']));
      $isser = preg_match('/^a:\d+:{s:/', $row['rule']);
      if(is_array($rule)){
        //转换旧版
        if(is_array($rule['data']))foreach ($rule['data'] as $key => $value) {
          if(isset($value['process'])){
            continue;
          }
          $rule['data'][$key]['process'] = spider_rule_7014($value);
          unset($rule['data'][$key]['cleanbefor'],$rule['data'][$key]['helper'],$rule['data'][$key]['cleanafter']);
          //转换上上版
          if(is_array($value))foreach ($value as $k => $v) {
            $fk = array_search($k, $oldArray);
            if($fk!==false){
              $rule['data'][$key]['process'][] = array('helper'=>$k);
              unset($rule['data'][$key][$k]);
            }
          }
        }

        $rule = addslashes(json_encode($rule));
        iDB::update('spider_rule',array('rule'=>$rule),array('id'=>$rid));
        $msg.='采集规则 rid:'.$rid.' 转换成功<iCMS>';
      }else{
        $msg.='<span class="label label-important">采集规则 rid:'.$rid.' 转换失败</span><iCMS>';
      }
    }
    return $msg;
});

function spider_rule_7014($value) {
  $process = array();
  if($value['cleanbefor']){
    $cleanArray = explode("\n", $value['cleanbefor']);
    foreach ($cleanArray as $k => $v) {
      $v && $process[] = array('helper'=>'dataclean','rule'=>$v);
    }
  }
  if($value['helper']) foreach ($value['helper'] as $k => $v) {
    $v && $process[] = array('helper'=>$v);
  }
  if($value['cleanafter']){
    $cleanArray = explode("\n", $value['cleanafter']);
    foreach ($cleanArray as $k => $v) {
      $v && $process[] = array('helper'=>'dataclean','rule'=>$v);
    }
  }
  return $process;
}
