<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
class category {
    public static $appid = null;
    public static $priv = null;
    private static $instance = null;
    public static $callback = null;

    public static function appid($appid,$priv=null){
        $self = new self();
        $self::$appid = $appid;
        $priv && $self::$priv = $priv;
        return $self;
    }
    public static function unset_appid(){
        self::$appid = null;
    }
    public static function init_sql($appid=null,$_sql=null){
        self::$appid && $appid = self::$appid;

        if($appid && !is_numeric($appid)){
            $appid = apps::id($appid);
         }

        if(empty($appid)){
            $sql = '1 = 1';
            $_sql && $sql = $_sql;
        }else{
            $sql =" `appid`='$appid'";
            $_sql && $sql.=' AND '.$_sql;
        }

        return $sql;
    }
    public static function total($appid=null) {
        $sql = self::init_sql($appid);
        return iDB::value("SELECT count(cid) FROM `#iCMS@__category` WHERE {$sql}");
    }
    public static function is_root($rootid="0"){
        $is = iDB::value("SELECT `cid` FROM `#iCMS@__category` where `rootid`='$rootid'");
        return $is?true:false;
    }
    public static function rootid($rootids=null,$appid=null) {
        if($rootids===null) return array();

        list($rootids,$is_multi)  = iSQL::multi_var($rootids);

        $sql  = iSQL::in($rootids,'rootid',false,true);
        $sql  = self::init_sql($appid,$sql);
        $data = array();
        $rs   = iDB::all("SELECT `cid`,`rootid` FROM `#iCMS@__category` where {$sql}",OBJECT);
        if($rs){
            $_count = count($rs);
            for ($i=0; $i < $_count; $i++) {
                if($is_multi){
                    $data[$rs[$i]->rootid][$rs[$i]->cid]= $rs[$i]->cid;
                }else{
                    $data[]= $rs[$i]->cid;
                }
            }
        }
        if(empty($data)){
            return;
        }
        return $data;
    }
    public static function multi_get($rs,$field,$appid=null) {
        $cids = iSQL::values($rs,$field,'array',null);
        $data = array();
        if($cids){
          $cids = iSQL::explode_var($cids);
          $appid && self::$appid = $appid;
          $data = (array) self::get($cids);
        }
        return $data;
    }
    public static function get($cids,$callback=null,$appid=null) {
        if(empty($cids)) return array();

        $field = '*';
        if(isset($callback['field'])){
            $field = $callback['field'];
        }

        list($cids,$is_multi)  = iSQL::multi_var($cids);

        $sql  = iSQL::in($cids,'cid',false,true);
        $sql  = self::init_sql($appid,$sql);
        $data = array();
        $rs   = iDB::all("SELECT {$field} FROM `#iCMS@__category` where {$sql}",OBJECT);
        if($rs){
            if($is_multi){
                $_count = count($rs);
                for ($i=0; $i < $_count; $i++) {
                    $data[$rs[$i]->cid]= category::item($rs[$i],$callback);
                }
            }else{
                if(isset($callback['field'])){
                    return $rs[0];
                }else{
                    $data = category::item($rs[0],$callback);
                }
            }
        }
        if(empty($data)){
            return;
        }
        return $data;
    }
    public static function item($category,$callback=null) {
        $category->iurl     = iURL::get('category',(array)$category);
        $category->href     = $category->iurl->href;
        $category->CP_ADD   = category::check_priv($category->cid,'a');
        $category->CP_EDIT  = category::check_priv($category->cid,'e');
        $category->CP_DEL   = category::check_priv($category->cid,'d');
        $category->rule     = json_decode($category->rule,true);
        $category->template = json_decode($category->template,true);
        $category->config   = json_decode($category->config,true);

        $callback && $category = iPHP::callback($callback,array($category));

        return $category;
    }
    public static function get_cid($rootid=null,$where=null,$appid=null) {
        $rootid===null OR $sql.= " `rootid`='$rootid'";

        $sql.= iSQL::where($where,true);
        $sql = self::init_sql($appid,$sql);
        $variable = iDB::all("SELECT `cid` FROM `#iCMS@__category` WHERE {$sql} ORDER BY `sortnum`  ASC",ARRAY_A);
        $category = array();
        foreach ((array)$variable as $key => $value) {
            if(self::$priv){
                if(category::check_priv($value['cid'],self::$priv,null)){
                    $category[] = $value['cid'];
                }
            }else{
                $category[] = $value['cid'];
            }

        }
        return $category;
    }

    public static function get_root($cid="0",$root=null) {
        empty($root) && $root = categoryApp::get_cache('rootid');
        $ids = $root[$cid];
        if(is_array($ids)){
            $array = $ids;
            foreach ($ids as $key => $_cid) {
              $array+=self::get_root($_cid,$root);
            }
        }
        return (array)$array;
    }
    public static function get_parent($cid="0",$parent=null) {
        if($cid){
            empty($parent) && $parent = categoryApp::get_cache('parent');
            $rootid = $parent[$cid];
            if($rootid){
                return self::get_parent($rootid,$parent);
            }
        }
        return $cid;
    }
    public static function cache($appid=null) {
        $sql = 'WHERE '.self::init_sql($appid);
        $all = iDB::all("SELECT * FROM `#iCMS@__category` {$sql} ORDER BY `sortnum`  ASC");
        //生成临时缓存
        self::cache_tmp($all);
        //正式缓存
        self::cache_gold($all);
        //清除临时缓存
        self::cache_tmp($all,'delete');
        unset($all);
        self::cache_common();
        gc_collect_cycles();
    }
    //正式缓存
    public static function cache_gold($all=null) {
        foreach((array)$all AS $C) {
            $C = self::data($C);
            self::cache_item($C,'C');
        }
    }
    //临时缓存
    public static function cache_tmp($all=null,$flag=false) {
        // if(!is_array($all)){
        //     $sql = 'WHERE '.self::init_sql($all);
        //     $all = iDB::all("SELECT * FROM `#iCMS@__category` {$sql}");
        // }
        foreach((array)$all AS $C) {
            if($flag==='delete'){
               iCache::delete('category/'.$C['cid']);
           }else{
                self::cache_item($C);
           }
        }
        gc_collect_cycles();
    }
    public static function cache_common() {
        self::cache_common_hidden();
        self::cache_common_array();
        self::cache_common_rootid();
        self::cache_common_domain();
        self::cache_common_rule();
    }
    //缓存隐藏节点
    public static function cache_common_hidden() {
        $all = iDB::all("SELECT `cid` FROM `#iCMS@__category` WHERE `status`='0'");
        $arr = array_column($all, 'cid');
        iCache::set('category/hidden',$arr,0);
        unset($all,$arr);
        gc_collect_cycles();
    }
    //缓存节点目录对应CID,CID对应的rootid
    public static function cache_common_array() {
        $all  = iDB::all("SELECT `cid`,`dir`,`rootid`,`status` FROM `#iCMS@__category`");
        $arr1 = array_column($all, 'cid','dir');
        $arr2 = array_column($all, 'rootid','cid');
        iCache::set('category/dir2cid',$arr1,0);
        iCache::set('category/parent',$arr2,0);
        unset($all,$arr1,$arr2);
        gc_collect_cycles();
    }

    public static function cache_common_rootid() {
        $all  = iDB::all("SELECT `cid`,`rootid` FROM `#iCMS@__category` ORDER BY `sortnum`  ASC");
        foreach((array)$all AS $C) {
            $rootid[$C['rootid']][$C['cid']] = $C['cid'];
        }
        iCache::set('category/rootid',$rootid,0);
        unset($rootid,$all);
        gc_collect_cycles();
    }
    /**
     * [cache_domain 要在 cache_rootid 之后执行]
     * [缓存节点绑定域名,用于iURL回调函数]
     */
    public static function cache_common_domain() {
        $all    = iDB::all("SELECT `cid` FROM `#iCMS@__category` WHERE `domain`!=''");
        $rootid = iCache::get('category/rootid');
        $domain_rootid = array();
        foreach((array)$all AS $C) {
            $root = self::get_root($C['cid'],$rootid);
            $root && $domain_rootid+= array_fill_keys($root, $C['cid']);
        }
        iCache::set('category/domain_rootid',$domain_rootid,0);
        unset($all,$domain_rootid,$root);
        gc_collect_cycles();
    }
    //缓存节点URL规则,用于rewrite
    public static function cache_common_rule() {
        $all   = iDB::all("SELECT `cid`,`dir`,`rule` FROM `#iCMS@__category` WHERE `rule`!=''");
        $rules = array();
        foreach((array)$all AS $C) {
            $rule = json_decode($C['rule'],true);
            if($rule)foreach ($rule as $key => &$value) {
               if($key!='index' && $key!='list'){
                    $value = str_replace('{CDIR}', $C['dir'], $value);
               }
            }
            $rule && $rules[$C['cid']] = $rule;
        }
        iCache::set('category/rules',$rules,0);
        unset($all,$domain_rootid,$root);
        gc_collect_cycles();
    }
    //分段生成缓存
    public static function cache_burst($appid=null,$offset=0,$num=10,$flag=null) {
        if($flag ==='common'){
            return self::cache_common();
        }

        $sql = self::init_sql($appid);

        $all   = iDB::all("
            SELECT `cid` FROM `#iCMS@__category`
            WHERE {$sql}
            ORDER BY `sortnum`  ASC
            LIMIT {$offset},{$num}
        ");
        $idA = array_column($all, 'cid');
        $idA && $ids = "'".implode("','", $idA)."'";
        if($ids){
            $rs = iDB::all("SELECT * FROM `#iCMS@__category` WHERE `cid` IN({$ids}) ORDER BY FIELD(`cid`,{$ids});");
            //生成临时缓存
            $flag ==='tmp' && self::cache_tmp($rs);
            //正式缓存
            $flag ==='gold' && self::cache_gold($rs);
            //清除临时缓存
            $flag ==='delete' && self::cache_tmp($all,'delete');
        }
    }

    public static function cache_all($offset,$maxperpage,$appid=null) {
        $sql = self::init_sql($appid);
        $ids_array  = iDB::all("
            SELECT `cid`
            FROM `#iCMS@__category` {$sql}
            ORDER BY `sortnum`  ASC
            LIMIT {$offset},{$maxperpage};
        ");
        $ids   = iSQL::values($ids_array,'cid');
        $ids   = $ids?$ids:'0';
        $rs  = iDB::all("SELECT * FROM `#iCMS@__category` WHERE `cid` IN({$ids}) ORDER BY FIELD(`cid`,{$ids});");
        foreach((array)$rs AS $C) {
            $C = self::data($C);
            self::cache_item($C,'C');
        }
        unset($$rs,$C,$ids_array);
    }
    public static function cache_get($cid="0",$fix=null) {
        return iCache::get('category/'.$fix.$cid);
    }
    public static function cache_item($C=null,$fix=null){
        is_array($C) OR $C = iDB::row("SELECT * FROM `#iCMS@__category` where `cid`='$C' LIMIT 1;",ARRAY_A);
        iCache::set('category/'.$fix.$C['cid'],$C,0);
    }
    public static function cache_del($cid=null){
        if(empty($cid)){
            return;
        }
        iCache::delete('category/'.$cid);
        iCache::delete('category/C'.$cid);
    }

    public static function data($C){
        if($C['url']){
            $C['iurl']   = array('href'=>$C['url']);
            $C['outurl'] = $C['url'];
        }else{
            $C['iurl'] = (array) iURL::get('category',$C);
        }

        $C['url']    = $C['iurl']['href'];
        $C['link']   = "<a href='{$C['url']}'>{$C['name']}</a>";
        $C['sname']  = $C['subname'];

        $C['subid']  = self::get_root($C['cid']);
        $C['counts'] = $C['count'];
        foreach ((array)$C['subid'] as $skey => $scid) {
            $sc = self::cache_get($scid);
            $C['counts']+=$sc['count'];
        }

        $C['child']  = $C['subid']?true:false;
        $C['subids'] = implode(',',(array)$C['subid']);
        $C['dirs']   = self::data_dirs($C['cid']);

        $C = self::data_pic($C);
        $C = self::data_parent($C);
        $C = self::data_nav($C);
        $C+= (array)apps_meta::data('category',$C['cid']);

        //category 应用信息
        $C['sappid'] = iCMS_APP_CATEGORY;
        $ca = apps::get_app($C['sappid']);
        $C['sapp'] = apps::get_app_lite($ca);
        $ca['fields'] && formerApp::data($C['cid'],$ca,'category',$C,null,$C);
        //category 绑定的应用
        $C['appid'] && $C['app'] = apps::get_app_lite($C['appid']);

        is_string($C['rule'])    && $C['rule']     = json_decode($C['rule'],true);
        is_string($C['template'])&& $C['template'] = json_decode($C['template'],true);
        is_string($C['config'])  && $C['config']   = json_decode($C['config'],true);

        empty($C['rule'])    && $C['rule']     = array();
        empty($C['template'])&& $C['template'] = array();
        empty($C['config'])  && $C['config']   = array();

		return $C;
    }
    public static function data_dirs($cid="0") {
        $C = self::cache_get($cid);
        $C['rootid'] && $dir.=self::data_dirs($C['rootid']);
        $dir.='/'.$C['dir'];
        return $dir;
    }
    public static function data_pic($C){
        $C['pic']  = is_array($C['pic'])?$C['pic']:filesApp::get_pic($C['pic']);
        $C['mpic'] = is_array($C['mpic'])?$C['mpic']:filesApp::get_pic($C['mpic']);
        $C['spic'] = is_array($C['spic'])?$C['spic']:filesApp::get_pic($C['spic']);
        return $C;
    }
    public static function data_parent($C){
        if($C['rootid']){
            $root = self::cache_get($C['rootid']);
            $C['parent'] = self::data($root);
        }
        return $C;
    }
    public static function data_nav($C){
        $nav      = '';
        $navArray = array();
        self::data_nav_array($C,$navArray);
        krsort($navArray);
        foreach ((array)$navArray as $key => $value) {
            $nav.="<li>
            <a href='{$value['url']}'>{$value['name']}</a>
            <span class=\"divider\">".iUI::lang('iCMS:navTag')."</span>
            </li>";
        }
        $C['nav'] = $nav;
        $C['navArray'] = $navArray;
        return $C;
    }
    public static function data_nav_array($C,&$navArray = array()) {
        if($C) {
            $navArray[]= array(
                'name' => $C['name'],
                'url'  => $C['iurl']['href'],
            );
            if($C['rootid']){
                $rc = (array)self::cache_get($C['rootid']);
                $rc['iurl'] = (array) iURL::get('category',$rc);
                self::data_nav_array($rc,$navArray);
            }
        }
    }
    public static function search_sql($cid,$field='cid'){
        if($cid){
            $cids  = (array)$cid;
            $_GET['sub'] && $cids+=categoryApp::get_cids($cid,true);
            $sql= iSQL::in($cids,$field);
        }
        return $sql;
    }
    public static function func($cid="0",$level = 1) {
        $cid_array  = (array)category::get_cid($cid);//获取$cid下所有子栏目ID
        $cate_array = (array)category::get($cid_array);     //获取子栏目数据
        $root_array = (array)category::rootid($cid_array);  //获取子栏目父栏目数据
        foreach($cid_array AS $root=>$_cid) {
            $C = (array)$cate_array[$_cid];
            // $child = $root_array[$_cid];
            $child = self::get_root($_cid);
            if (self::$callback['func'] && is_callable(self::$callback['func'])) {
                $data = call_user_func_array(self::$callback['func'],array($C,$level,$child));
            }
            $child && $data.= self::func($C['cid'],$level+1);
        }
        return $data;
    }
    public static function select_lite($scid="0",$cid="0",$level = 1,$url=false,$where=null) {
        $cid_array  = (array)category::get_cid($cid,$where);//获取$cid下所有子栏目ID
        $cate_array = (array)category::get($cid_array);     //获取子栏目数据
        $root_array = (array)category::rootid($cid_array);  //获取子栏目父栏目数据
        foreach($cid_array AS $root=>$_cid) {
            $C = (array)$cate_array[$_cid];
            if($C['status']) {
                $tag      = ($level=='1'?"":"├ ");
                $selected = ($scid==$_cid)?"selected":"";
                $text     = str_repeat("│　", $level-1).$tag.$C['name']."[cid:{$_cid}]".($C['url']?"[∞]":"");
                ($C['url'] && !$url) && $selected ='disabled';
                $option.="<option name='".$C['name']."' value='{$_cid}' $selected>{$text}</option>";
            }
            $root_array[$_cid] && $option.= self::select_lite($scid,$C['cid'],$level+1,$url);
        }
        return $option;
    }
    public static function select($scid="0",$cid="0",$level = 1,$url=false,$where=null) {
        // $cc = iDB::value("SELECT count(*) FROM `#iCMS@__category`");
        return self::select_lite($scid,$cid,$level,$url,$where);
    }
    public static function priv($p) {
        $category = new category();
        $category::$priv = $p;
        return $category;
    }
    public static function check_priv($p, $act = '', $ret = '') {
        if (members::is_superadmin()) {
            return true;
        }
        if ($p === 'CIDS') {
            foreach (members::$priv['category'] as $key => $value) {
                if (strpos($value, ':') !== false) {
                    list($cid,$priv) = explode(':', $value);
                    if($act){
                        if($priv==$act){
                            $cids[$cid] = $cid;
                        }
                    }
                    // else{
                    //     if(self::check_priv($cid, $act)){
                    //         $cids[$cid] = $cid;
                    //     }
                    // }
                }
            }
            return $cids;
        }
        if(members::$priv['category']){
            if($act){
                strpos($p, ':') === false && $p = $p . ':' . $act;
            }
            $priv = check_priv((string) $p, members::$priv['category']);
        }else{
            $priv = false;
        }
        $priv OR self::permission($p, $ret);
        return $priv;
    }
    public static function permission($p=null, $ret = '') {
        if($ret){
            $title = '栏目:cid='.$p;
            if($p=="0"){
                $title = "添加顶级栏目";
            }
            iUI::permission($title, $ret);
        }
    }
    public static function update_count($cid,$math='+'){
        $math=='-' && $sql = " AND `count`>0";
        iDB::query("UPDATE `#iCMS@__category` SET `count` = count".$math."1 WHERE `cid` ='$cid' {$sql}");
    }
    public static function del_app_data($appid=null){
        iDB::query("DELETE FROM `#iCMS@__category` WHERE `appid` = '".$appid."'");
        iDB::query("DELETE FROM `#iCMS@__category_map` WHERE `appid` = '".$appid."';");
    }
}
