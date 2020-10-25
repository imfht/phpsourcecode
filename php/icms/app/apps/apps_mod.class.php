<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/

class apps_mod {
    const DATA_TABLE_NAME  = '_cdata';
    const DATA_PRIMARY_KEY = 'cdata_id';
    const DATA_UNION_KEY   = '_id';
    public static $base_fields_key = null;

    public static function get_data_table(&$array) {
        $data_table  = next($array);
        if($data_table){
            $primary = $data_table['primary'];
            if($primary==self::DATA_PRIMARY_KEY){
                return $data_table;
            }else{
                return self::get_data_table($array);
            }
        }else{
            return false;
        }
    }
    public static function data_table_name($name){
      return $name.self::DATA_TABLE_NAME;
    }
    public static function data_union_key($name){
      return $name.self::DATA_UNION_KEY;
    }

    public static function base_fields_index(){
        return array(
            'index_id'         =>'KEY `id` (`status`,`id`)',
            'index_hits'       =>'KEY `hits` (`status`,`hits`)',
            'index_pubdate'    =>'KEY `pubdate` (`status`,`pubdate`)',
            'index_hits_week'  =>'KEY `hits_week` (`status`,`hits_week`)',
            'index_hits_month' =>'KEY `hits_month` (`status`,`hits_month`)',
            'index_cid_hits'   =>'KEY `cid_hits` (`status`,`cid`,`hits`)'
        );
    }
    public static function base_fields_key($key=null){
        if(self::$base_fields_key===null){
            $json   = apps::etc('apps','fields.source');
            $fields = json_decode($json,true);
            $array  = array_column($fields, 'name');
        }else{
          $array = self::$base_fields_key;
        }
        if($key){
            return in_array($key, $array);
        }
        return $array;
    }
    public static function data_base_fields($name=null) {
        $a[self::DATA_PRIMARY_KEY] = array (
          'id'       => self::DATA_PRIMARY_KEY,
          'label'    => '附加表id',
          'comment'  => '主键 自增ID',
          'field'    => 'PRIMARY',
          'name'     => self::DATA_PRIMARY_KEY,
          'default'  => '',
          'type'     => 'PRIMARY',
          'len'      => '10',
          'unsigned' => '1',
        );
        if($name){
            $union_id = self::data_union_key($name);
            $a[$union_id] = array (
              'id'       => $union_id,
              'label'    => '关联内容ID',
              'comment'  => '内容ID 关联'.$name.'表',
              'field'    => 'INT',
              'name'     => $union_id,
              'default'  => '',
              'type'     => 'union',
              'len'      => '10',
              'unsigned' => '1',
            );
        }

        return $a;
    }
    public static function field_array(&$fieldata=null,&$dataTable_field_array=array()){
        $fields = '';
        if(is_array($fieldata)){
          $field_array = array();
          foreach ($fieldata as $key => $value) {
            $json = stripslashes($value);
            $output = json_decode($json,true);
            if($output['name']){
              preg_match("/[a-zA-Z0-9_\-]/",$output['name']) OR iUI::alert('['.$output['label'].'] 字段名只能由英文字母、数字或_-组成,不支持中文');
              $output['label'] OR iUI::alert('发现自定义字段中空字段名称!');
              // empty($output['comment']) && $output['comment'] = $output['label'];
              $fname = $output['name'];
              $fname OR iUI::alert('发现自定义字段中有空字段名!');
              $field_array[$fname] = $output;
              if($output['field']=="MEDIUMTEXT"){
                $dataTable_field_array[$key] = $output;
                unset($fieldata[$key]);//从基本表移除
              }
            }else{
              $field_array[$key] = $output;
            }
          }
          //字段数据存入数据库
          $fields = addslashes(cnjson_encode($field_array));
        }
        return $fields;
    }
    public static function json_field($json=null){
        $json_array  = array(array(),array());
        if(empty($json)) return $json_array;
        $field_array = json_decode($json,true);
        //兼容旧的字段格式
        if (is_array($field_array)) {
          $tmp = $field_array;
          $tmp = current($tmp);
          if (!is_array($tmp)) {
            $field_array = apps_mod::get_field_array($field_array, true);
          }
        } 
        foreach ($field_array as $key => $value) {
            $a = array();
            foreach ($value as $k => $v) {
                if(in_array($k, array('field','label','name','default','len','comment','unsigned'))){
                    $a[$k] = $v;
                }
            }
            if($a){
                ksort($a);
                $json = json_encode($a);
                if(strtoupper($value['field'])=="MEDIUMTEXT"){
                    $json_array[1][$key] = $json;
                }else{
                    $json_array[0][$key] = $json;
                }
            }
        }
        return $json_array;
    }
    public static function drop_table($fieldata,&$table_array,$name) {
      if(empty($fieldata) && $table_array[$name] && iDB::check_table($name)){
        apps_db::drop_tables(array(iPHP_DB_PREFIX.$name));
        unset($table_array[$name]);
      }
    }
    public static function find_MEDIUMTEXT(&$json_field) {
        $addons_json_field = array();
        foreach ($json_field as $key => $value) {
            $a = json_decode($value,true);
            if(strtoupper($a['field'])=="MEDIUMTEXT"){
              $addons_json_field[$key] = $value;
              unset($json_field[$key]);//不参与基本表比较
            }
        }
        return $addons_json_field;
    }
    /**
     * 创建xxx_data附加表
     * @param  [type] $fieldata [description]
     * @param  [type] $name     [description]
     * @return [type]           [description]
     */
    public static function data_create_table($fieldata,$name,$union_id,$query=true) {
        $table = apps_db::create_table(
          $name,
          $fieldata,//获取字段数组
          array(//索引
            'index_'.$union_id =>'KEY `'.$union_id.'` (`'.$union_id.'`)'
          ),
          $query
        );
        array_push ($table,$union_id,'附加');
        return array($name=>$table);
    }
    /**
     * 获取字段数据
     * @param  [type]  $data [字段配置]
     * @param  boolean $ui   [是否把UI标识返回数组]
     * @return [type]        [description]
     */
    public static function get_field_array($data,$ui=false) {
        $array = array();
        if($data)foreach ($data as $key => $value) {
            if(is_array($value)){
                $array[$key] = $value;
            }else{
                $text = stripslashes($value);
                $array[$key] = json_decode($text,true);
                //向前兼容
                if($array[$key]===null){
                  if($text=='UI:BR'){
                      $output = array('type'=>'br');
                  }else{
                      strpos($text,'&')!==false && parse_str($text,$output);
                  }
                  $output && $array[$key] = $output;
                }
            }
            if($array[$key]['type']=='br' && !$ui){
                unset($array[$key]);
            }
        }
        return $array;
    }
    public static function get_data($app,$id,$filter=null) {
        $data  = array();
        if(empty($id) ){
            return $data;
        }

        $table = $app['table'];
        foreach ((array)$table as $key => $value) {
            $primary_key = $value['primary'];
            $value['union'] && $primary_key = $value['union'];
            if($filter && !in_array($value['table'],$filter)){
              continue;
            }
            $udata = (array)iDB::row("SELECT * FROM `{$value['table']}` WHERE `{$primary_key}`='$id' LIMIT 1;",ARRAY_A);
            $udata && $data+=$udata;
        }
        return $data;
    }
    public static function template($rs,$ret='string'){
      //模板标签
      if($rs['app']){
        $_app = $rs['app'];
        if($rs['config']['iFormer'] && $rs['apptype']=="2"){
          $_app = 'content';
        }
        $template = (array)apps::get_func($_app,true);
        list($path,$obj_name)= apps::get_path($_app,'app',true);

        if(is_file($path)){
            //判断是否有APP同名方法存在 如果有 $appname 模板标签可用
            $class_methods = get_class_methods ($obj_name);
            if(array_search ($_app ,  $class_methods )!==FALSE){
              array_push ($template,'$'.$_app);
            }
        }
      }
      if($rs['config']['iFormer'] && $rs['apptype']=="2"){
        foreach ((array)$template as $key => $value) {
          $template[$key] = str_replace(array(':content:','$content'), array(':'.$rs['app'].':','$'.$rs['app']), $value);
        }
      }
      return $ret=='string'?implode("\n", (array)$template):(array)$template;
    }
    public static function iurl($rs){
      if($rs['table'] && $rs['apptype']=="2"){
        $table  = reset($rs['table']);
        $rule = array('rule'=>'4','primary'=>$table['primary'],'page'=>'p');
      }else{
        $array = array(
            'http'     => array('rule'=>'0','primary'=>''),
            'index'    => array('rule'=>'0','primary'=>''),
            'category' => array('rule'=>'1','primary'=>'cid'),
            'article'  => array('rule'=>'2','primary'=>'id','page'=>'p'),
            'tag'      => array('rule'=>'3','primary'=>'id'),
        );
        $rule = $array[$rs['app']];
        if(empty($rule) && $rs['config']['iurl']){
          $rule = $rs['config']['iurl'];
        }
      }
      return $rule;
    }
}
