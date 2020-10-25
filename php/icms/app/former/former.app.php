<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
class formerApp{
    public static $primary_id = null;
    public function __construct() {
        $this->appid = iCMS_APP_FORMER;
    }
    /**
     * [创建表单]
     * @param  [type]  $app        [app数据/appid]
     * @param  [type]  $rs         [数据]
     * @param  boolean $union_data [是否查询数据表]
     * @return [type]              [description]
     */
    public static function add($app,$rs,$union_data=false){
        is_array($app) OR $app = apps::get($app);
        if($app['fields']){
            $dtn = apps_mod::data_table_name($app['app']);
            $data_table = $app['table'][$dtn];
            if($data_table){
                former::base_fields_merge($app,$data_table);
                if($union_data){
                    $primary_key = $data_table['primary'];
                    $union_key   = $data_table['union'];
                    $table       = reset($app['table']);
                    $id          = $rs[$table['primary']];
                    $id_key      = $union_key;
                    // $union_key && $id_key = $union_key;
                    $urs = (array)iDB::row("SELECT * FROM `{$data_table['table']}` WHERE `{$id_key}`='$id' LIMIT 1;",ARRAY_A);
                    $rs[$primary_key] = 0;
                    $rs = array_merge($rs,$urs);
                    $union_key && $rs[$union_key] = $id;
                }

            }
            former::set_template_class(array(
                'group'    => 'input-prepend input-append',
                'label'    => 'add-on',
                'label2'   => 'add-on',
                'radio'    => 'add-on',
                'checkbox' => 'add-on',
            ));
            former::$config['value']   = array(
                'userid'   => members::$userid,
                'username' => members::$data->username,
                'nickname' => members::$data->nickname
            );
            former::$config['gateway'] = 'admincp';
            former::$config['option'] = true;
            former::create($app,$rs);
        }
    }
    /**
     * [保存表单]
     * @param  [type] $app    [app数据/appid]
     * @param  [type] $pri_id [主键值]
     * @return [type]         [description]
     */
    public static function save($app,$pri_id=null){
        is_array($app) OR $app = apps::get($app);

        if($app['fields']){

            list($variable,$tables,$orig_post,$imap,$tags,$files) = former::post_data($app);

            // if(!$variable){
            //     iUI::alert("表单数据处理出错!");
            // }
            //非自定义应用数据
            if($pri_id){
                $pri_table = reset($app['table']);
            }

            $update = false;
            if($variable)foreach ($variable as $table_name => $_data) {
                // if(empty($_data)){
                //   continue;
                // }
                // if($data && $table_name==$pri_table['name']){
                //     $data = array_merge($data,$_data);
                //     continue;
                // }
                //当前表 数据
                $_table   = $app['table'][$table_name];
                //当前表 主键
                $primary = $_table['primary'];
                //关联字符 && 关联数据
                if($_table['union'] && $union_data){
                  $_data[$_table['union']] = $union_data[$_table['union']];
                }
                //非自定义应用数据
                if($pri_id && $table_name==$pri_table['name']){
                    $_data[$pri_table['primary']] = $pri_id;
                }

                $id = $_data[$primary];
                unset($_data[$primary]);//主键不更新
                if($_data){
                    if(empty($id)){ //主键值为空
                        $id = iDB::insert($table_name,$_data);
                    }else{
                        $update = true;
                        iDB::update($table_name, $_data, array($primary=>$id));
                    }
                }
                $union_id = apps_mod::data_union_key($table_name);
                if(empty($_table['union'])){
                    $union_data[$union_id] = $id;
                    self::$primary_id = $id;
                }
            }

            if($imap)foreach ($imap as $key => $value) {
                iMap::init($value[0],$app['id'],$key);
                if($update){
                    $orig = $orig_post[$key];
                    iMap::diff($value[1],$orig,$id);
                }else{
                    if($value[0]=="category"){
                        category::update_count($value[1]);
                    }
                    iMap::add($value[1],$id);
                }
            }

            if($tags)foreach ($tags as $key => $value) {
                if(empty($value[0])){
                    continue;
                }
                tag::$field = $key;
                tag::$appid = $app['id'];
                if($update){
                    $orig = $orig_post[$key];
                    tag::diff($value[0],$orig,members::$userid,$id,$value[1]);
                }else{
                    tag::add($value[0],members::$userid,$id,$value[1]);
                }
            }
            if($files)foreach ($files as $key => $value) {
                files::set_map($app['id'],$id,$value,'path');
            }
            return $update;
        }
    }
    public static function data($id,$app,$name,&$resource,$vars=null,$category=null){
        if($app['fields']){
            $dataFields = array();
            $field_array = former::fields($app['fields']);
            foreach ((array)$field_array as $fkey => $fields) {
                if($fields['field']=='MEDIUMTEXT'){
                    $dataFields[$fkey] = $fields;
                }else{
                   self::vars($fields,$fkey,$resource,$vars,$category,$name);
                }
            }

            if($dataFields){
                $dtn = iDB::table(apps_mod::data_table_name($name));
                $iDATA = apps_mod::get_data($app,$id,array($dtn));
                foreach ((array)$dataFields as $fkey => $fields) {
                    $resource[$fkey] = $iDATA[$fkey];
                    self::vars($fields,$fkey,$resource,$vars,$category,$name);
                }
            }
        }
    }
    public static function vars($field,$key,&$rs,$vars=null,$category=null,$app=null){
        $option_array = array();
        $value        = $rs[$key];
        $ret          = array();
        $nkey         = null;
        switch ($field['type']) {
            case 'multi_image':
                $nkey     = $key.'_array';
                // $valArray = unserialize($value);
                if(!is_array($value)){
                    if(preg_match('/^a:\d+:\{/', $value)){
                        $valArray = unserialize($value);
                    }else{
                        $valArray = json_decode($value,true);
                    }
                }
                if($value && empty($valArray)){
                    $valArray = explode("\n", $value);
                }
                if(is_array($valArray))foreach ($valArray as $i => $val) {
                    $val && $ret[$i]= filesApp::get_pic(trim($val));
                }
            break;
            case 'image':
                $nkey   = $key.'_array';
                $ret = filesApp::get_pic($value);
            break;
            case 'file':
                $nkey = $key.'_file';
                $pi   = pathinfo($value);
                $ret   = array(
                    'name' => $pi['filename'],
                    'ext'  => $pi['extension'],
                    'dir'  => $pi['dirname'],
                    'url'  => filesApp::get_url($pi['filename'],'download')
                );
            break;
            case 'multi_file':
                $nkey = $key.'_file';
                // $valArray = unserialize($value);
                if(preg_match('/^a:\d+:\{/', $value)){
                    $valArray = unserialize($value);
                }else{
                    $valArray = json_decode($value,true);
                }
                if($value && empty($valArray)){
                    $valArray = explode("\n", $value);
                }
                if(is_array($valArray))foreach ($valArray as $i => $val) {
                    if($val){
                        $pi   = pathinfo($val);
                        $ret[$i]   = array(
                            'name' => $pi['filename'],
                            'ext'  => $pi['extension'],
                            'dir'  => $pi['dirname'],
                            'url'  => filesApp::get_url($pi['filename'],'download')
                        );
                    }
                }
            break;
            case 'category':
                if($key=='cid'){
                    break;
                }
                $nkey      = $key.'_category';
                $_category = categoryApp::get_cache_cid($value);
                $ret    = categoryApp::get_lite($_category);
            break;
            case 'multi_category':
                $nkey   = $key.'_category';
                $valArray = explode(",", $value);
                foreach ($valArray as $i => $val) {
                    $_category  = categoryApp::get_cache_cid($val);
                    $ret[$i] = categoryApp::get_lite($_category);
                }
            break;
            case 'userid':
                if($vars['user']){
                    $nkey   = $key.'_user';
                    if ($rs['postype']) {
                        $ret = user::empty_info($value,'###');
                    } else {
                        $ret = user::info($value);
                    }
                }
            break;
            case 'radio_prop':
            case 'checkbox_prop':
            case 'multi_prop':
            case 'prop':
                if($key=='pid'){
                    break;
                }
                $nkey   = $key.'_prop';
                $propArray = propApp::field($key,$app);
                // empty($ret['prop']) && $propArray = propApp::value($key);
                if($field['type']=='multi_prop'||$field['type']=='checkbox_prop'){
                    $valArray = explode(",", $value);
                    if($propArray)foreach ($propArray as $i => $val) {
                        if(in_array($val['val'], $valArray)){
                            $ret[$val['val']] = $val;
                        }
                    }
                }else{
                    $ret = $propArray[$value];
                }
                empty($ret) && $ret = array();
                $field['option'] = null;
            break;
            case 'tag':
                $vars['tag'] && tagApp::get_array($rs,$category['name'],$key,$value);
            break;
            case 'editor';
                if($value){
                   $rs[$key.'_pics'] = filesApp::get_content_pics($value,$pic_array);
                }
            break;
            case 'markdown';
                if($value){
                    //$rs[$key] = $value;
                    $plugin = array('markdown'=> true,'htmlspecialchars' =>true);
                    $rs[$key] = iPHP::callback(array("plugin_markdown","HOOK"),array($value,&$plugin));
                }
            break;
           case 'json';
                if($value){
                    $rs[$key]  = json_decode($value,true);
                }
            break;
            default:
                // $ret = $value;
            break;
        }
        if($field['option'] && !in_array($key, array('creative','status'))){
            $nkey = $key.'_array';
            $optionArray = explode(";", $field['option']);
            $valArray = explode(",", $value);
            foreach ($optionArray as $ok => $val) {
                $val = trim($val,"\r\n");
                if($val){
                    list($opt_text,$opt_value) = explode("=", $val);
                    $option_array[$key][$opt_value] = $opt_text;
                    // $ret['option'][$opt_value] = $opt_text;
                    if($field['multiple']){
                        if(in_array($opt_value, $valArray)){
                            $ret[$opt_value] = $opt_text;
                        }
                    }else{
                        if($opt_value==$value){
                            $nkey = $key.'_value';
                            $ret = $opt_text;
                            break;
                        }
                    }
                }
            }
        }
        $nkey && $rs[$nkey] = $ret;
        $option_array && iView::assign('option_array', $option_array);
    }
}
