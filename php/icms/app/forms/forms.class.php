<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
defined('iPHP') OR exit('What are you doing?');

class forms{
    public static function short_app($app){
        if(strpos($app, 'forms_') !== false) {
            $app = substr($app,6);
        }
        return $app;
    }
    public static function base_fields_index(){
        return array(
            // 'index_id' =>'KEY `id` (`status`,`id`)',
        );
    }
    public static function get($vars=0,$field='id'){
        if(empty($vars)) return array();
        if($vars=='all'){
            $sql      = '1=1';
            $is_multi = true;
        }else{
            list($vars,$is_multi)  = iSQL::multi_var($vars);
            $sql  = iSQL::in($vars,$field,false,true);
        }
        $data = array();
        $rs   = iDB::all("SELECT * FROM `#iCMS@__forms` where {$sql}");
        if($rs){
            $_count = count($rs);
            for ($i=0; $i < $_count; $i++) {
                $data[$rs[$i][$field]]= apps::item($rs[$i]);
            }
            $is_multi OR $data = $data[$vars];
        }
        if(empty($data)){
            return;
        }
        return $data;
    }
    public static function delete($app){
        is_array($app) OR $app = self::get($app);
        if($app){
            //删除表
            self::drop_table($app['table']);
            //删除数据
            self::del_data($app['id']);
        }

    }

    public static function del_data($id){
        $id && iDB::query("DELETE FROM `#iCMS@__forms` WHERE `id` = '{$id}'; ");
    }
    public static function drop_table($table){
        if($table)foreach ((array)$table as $key => $value) {
            $value['table'] && iDB::query("DROP TABLE IF EXISTS `".$value['table']."`");
        }
    }
    public static function get_data($app,$id) {
        $data  = array();
        if(empty($id) ){
            return $data;
        }

        $table = $app['table'];
        foreach ($table as $key => $value) {
            $primary_key = $value['primary'];
            $value['union'] && $primary_key = $value['union'];
            $data+= (array)iDB::row("SELECT * FROM `{$value['table']}` WHERE `{$primary_key}`='$id' LIMIT 1;",ARRAY_A);
        }
        return $data;
    }
}
