<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/

class appsApp {
    public $_app     = null;
    public $_primary ='id';
    public $_table   = null;
    public $_gets    = null;
    public $methods  = array('iCMS','clink','search','hits','vote','comment');

    public static $statusMap  = '1';
    public static $s_app  = null;
    public static $config = null;
    public static $DATA   = null;
    public static $category = array();

    public function __construct($app=null,$primary='id',$table=null) {
        empty($app) && trigger_error('$app is empty',E_USER_ERROR);

        $this->_app     = $app;
        $this->_primary = $primary;
        $this->_table   = $table;
        self::$s_app    = $app;
        self::$config   = iCMS::$config[$app];
        $this->add_method($app);
    }
    public function __destruct() {
        iPHP::app_destruct();
    }
    public function gets() {
        $v = (int) $_GET[$this->_primary];
        $p = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        $f = $this->_primary;
        $dir = iSecurity::escapeStr($_GET['dir']);
        $dir && $this->_gets['cid'] = categoryApp::get_cache('dir2cid',$dir);

        $cid = iSecurity::escapeStr($_GET['cid']);
        $cid && $this->_gets['cid'] = $cid;

        if(isset($_GET['clink'])){
            $v = iSecurity::escapeStr($_GET['clink']);
            $f = 'clink';
        }
        if(isset($_GET['AUTHID'])){
            $authID = iSecurity::escapeStr($_GET['AUTHID']);
            $v      = auth_decode($authID);
            $v OR iPHP::error_404('AUTHID decode error', 10001);
        }
        if(isset($_GET['HASHID'])){
            $hashID  = iSecurity::escapeStr($_GET['HASHID']);
            $salt    = iSecurity::escapeStr($_GET['salt']);
            $len     = strlen($hashID);
            $Hashids = iURL::Hashids($salt,$len);
            $result  = $Hashids->decode($hashID);
            $v       = $result[0];
            $v OR iPHP::error_404('HASHID decode error', 10002);
        }
        return array($v,$p,$f);
    }
    public function do_iCMS($a = null) {
        list($v,$p,$f) = $this->gets();
        $func = $this->_app;
        if(!method_exists($this, $func)){
            iPHP::error_404('Call to undefined method <b>' . __CLASS__ . '::'.$func.'</b>', '1004');
        }

        $cdn = iCMS::$config['CDN'];
        if($cdn['enable']){
            $expires = $cdn['expires'];
            @header("Cache-Control: ".$cdn['cache_control'].", max-age=" . $expires);
            @header("Pragma: ".$cdn['cache_control']);
            @header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
            @header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
        }
        return $this->$func($v,$p,$f);
    }
    public function do_clink($a = null) {
        return $this->do_iCMS($a);
    }
    public function do_search($a = null) {
        $app = new searchApp();
        return $app->search('{iTPL}/'.$this->_app.'.search.htm');
    }
    public function API_iCMS() {
        return $this->do_iCMS();
    }
    public function API_clink() {
        return $this->do_clink();
    }
    public function API_search($a = null) {
        return $this->do_search($a);
    }
    public function API_hits($id = null) {
        list($name,$primary,$table) = array($this->_app,$this->_primary,$this->_table);

        $id===null && $id = (int)$_GET['id'];
        if($id){
            $table===null && $table='#iCMS@__'.$name;
            self::update_hits($table,$id,$primary);
        }
    }
    public function API_comment() {
        $appid = (int) $_GET['appid'];
        $cid = (int) $_GET['cid'];
        $iid = (int) $_GET['iid'];
        $func = $this->_app;
        $this->$func($iid,1,$this->_primary,'{iTPL}/'.$this->_app.'.comment.htm');
    }
    public function ACTION_vote() {
        // user::get_cookie() OR iUI::code(0,'iCMS:!login',0,'json');
        list($name,$primary,$table) = array($this->_app,$this->_primary,$this->_table);

        $type = $_POST['type'];
        $iid  = (int) $_POST['iid'];
        $iid OR iUI::code(0, $name.':empty_id', 0, 'json');

        $ackey = $name.'_' . $type . '_' . $iid;
        $vote = iPHP::get_cookie($ackey);
        $vote && iUI::code(0, $name.':!' . $type, 0, 'json');

        if ($type == 'good') {
            $sql = '`good`=good+1';
        } else {
            $sql = '`bad`=bad+1';
        }
        $table===null && $table='`#iCMS@__'.$name.'`';

        iDB::query("
            UPDATE {$table}
            SET {$sql}
            WHERE `{$primary}` ='{$iid}'
        ");

        iPHP::set_cookie($ackey, $_SERVER['REQUEST_TIME'], 86400);
        iUI::code(1, $name.':'. $type, 0, 'json');
    }

    public function get_data($fvar,$field=null,$flag=true) {
        list($name,$primary,$table) = array($this->_app,$this->_primary,$this->_table);

        $table===null && $table = '`#iCMS@__'.$name.'`';
        $field===null && $field = $primary;

        $this->_gets['cid'] && $sql = " AND `cid`='".(int)$this->_gets['cid']."'";

        $sql.= iSQL::in(self::$statusMap, 'status');
        $data = iDB::row("
            SELECT * FROM {$table}
            WHERE `".$field."`='".$fvar. "' {$sql}
            LIMIT 1;",
        ARRAY_A);

        if($flag===false){
            return $data;
        }

        $data OR iPHP::error_404(array("{$name}:not_found",$field,$fvar), 10001);

        if ($data['url']) {
            if (iView::$gateway == "html") {
                return false;
            } else {
                $this->API_hits($data['id']);
                iPHP::redirect($data['url']);
            }
        }

        return $data;
    }

    public function add_method($methods){
        $mArray=is_array($methods)?$methods:explode(',', $methods);
        $this->methods = array_merge($mArray,$this->methods);
    }
//--------------------------------------------------------------------
    public static function render($data,$tpl,$name=null,$app=null) {
        if ($tpl===false) return $data;

        $name ===null && $name = self::$s_app;
        $app  ===null && $app  = $name;
        $view_tpl = $data['tpl'];
        $view_tpl OR $view_tpl = $data['category']['template'][$name];
        strstr($tpl, '.htm') && $view_tpl = $tpl;
        iView::set_iVARS($data['iurl'],'iURL');
        if($data['category']){
            if(!isset(iView::$handle->_vars['APP'])){
                iView::assign('APP', $data['category']['app']); //绑定的应用信息
            }
            if($tpl!==null) unset($data['category']['app']);
            iView::assign('category', $data['category']);
            if($tpl!==null) unset($data['category']);
        }
        $data['sapp'] && iView::assign('SAPP', apps::get_app_lite($data['sapp']));//自身应用信息
        iView::assign($name, $data);

        if($tpl===null) return $data;//不解析模板返回原数据

        $view = iView::render($view_tpl,$app);
        if($view) return array($view,$data);
    }

    public static function custom_data(&$data,$vars=null,$appid=null){
        if(is_array($data)){
            $appid===null && $appid = self::$s_app;
            if(empty($appid) && $data['category']){
                $appid =$data['category']['app']['app'];
            }
            $meta = (array)apps_meta::data($appid,$data['id']);
            $data = array_merge($data,$meta);
            $app  = apps::get_app($appid);
            $data['sapp'] = apps::get_app_lite($app);
            $app['fields'] && formerApp::data($data['id'],$app,$appid,$data,$vars,$data['category']);
        }
    }

    public static function hooked(&$data){
        iPHP::hook(self::$s_app,$data,iCMS::$config['hooks'][self::$s_app]);
    }

    public static function body_pics_page($pic_array,$data,$page,$total,$next_url){
        $img_array = array_unique($pic_array[0]);
        foreach ($img_array as $key => $img) {
            if(!self::$config['img_title']){
                $img = preg_replace('@title\s*=\s*(["\']?).*?\\1\s*@is', '', $img);
                $img = preg_replace('@alt\s*=\s*(["\']?).*?\\1\s*@is', '', $img);
                $img = str_replace('<img', '<img title="' . addslashes($data['title']) . '" alt="' . addslashes($data['title']) . '"', $img);
            }
            if (self::$config['pic_center']) {
                $img_replace[$key] = '<p class="pic_center">'.$img.'</p>';
            } else {
                $img_replace[$key] = $img;
            }
            if(self::$config['pic_next'] && $total>1){
                $clicknext = '<a href="'.$next_url.'"><b>'.iUI::lang('iCMS:clicknext').' ('.$page.'/'.$total.')</b></a>';
                $clickimg = '<a href="' . $next_url . '" title="' . $data['title'] . '" class="img">' . $img . '</a>';
                if (self::$config['pic_center']) {
                    $img_replace[$key] = '<p class="click2next">'.$clicknext.'</p>';
                    $img_replace[$key].= '<p class="pic_center">'.$clickimg.'</p>';
                } else {
                    $img_replace[$key] = '<p>' . $clicknext . '</p>';
                    $img_replace[$key] .= '<p>' . $clickimg . '</p>';
                }
            }
        }
        return str_replace($img_array, $img_replace, $data['body']);
    }
    public static function process($expr,&$category,&$data, $tpl = false){
        $category = categoryApp::category($data['cid'], false);
        isset($data['appid']) OR $data['appid'] = $category['app']['id'];

        if ($expr) {
            $category OR iPHP::error_404(array('category:not_found','cid',$category['cid']),10002);
        } else {
            if (empty($category)) return false;
        }

        if ($category['status'] == 0) return false;

        $app_name = $category['app']['app'];

        if(self::is_html($expr,$category,$app_name)){
            return false;
        }

        $data['iurl'] = (array)iURL::get($app_name,array($data,$category));
        $data['url']  = $data['iurl']['href'];

        self::__redirect($tpl,$category['mode'],$data['iurl']);

        if($category['app']['type']=="2"){ //自定义应用模板信息
            iPHP::callback(array("contentFunc","interfaced"),array($category['app']));
        }

        $data['category'] = categoryApp::get_lite($category);
        $tpl===null && $data['category'] = $category;

        return true;
    }
    public static function redirect_html($iurl) {
        $fp  = $iurl['path'];
        $url = $iurl['href'];

        if(iView::$gateway=='html'
            || empty($url)
            || stristr($url, '.php?')
            || iPHP_DEVICE!='desktop'
            )
        {
            return false ;
        }

        is_file($fp) && iPHP::redirect($url);
    }
    public static function is_html($expr,$C,$key) {
        if (iView::$gateway == "html"
            && $expr
            && (
                strstr($C['rule'][$key], '{PHP}')
                || $C['outurl']
                || $C['mode'] == "0")
               )
        {
            return true;
        }
        return false;
    }
    private static function __redirect($expr,$mode,$iurl){
        if($expr && $mode == '1') {
            self::redirect_html($iurl);
        }
    }
    public static function setData($key,$value) {
        self::$DATA[$key] = $value;
    }
    public static function unData($key=null) {
        if($key){
            self::$DATA[$key] = null;
        }else{
            self::$DATA = null;
        }
    }
    public static function update_hits_sql($all=true,$hit=1){
        $timer_task = iPHP::timer_task();
        if($all===false){
            $time  = time();
            $utime = iCache::get('update_hits_all');
            if($time-$utime<86400){
                return false;
            }
            iCache::set('update_hits_all',$time,0);
        }

        $pieces = array();
        $all && $pieces[] = '`hits` = hits+'.$hit;
        foreach ($timer_task as $key => $bool) {
            $field = "hits_{$key}";
            if($key=='yday'){
                if($bool==1){
                    $pieces[]="`hits_yday` = hits_today";
                }elseif ($bool>1) {
                    $pieces[]="`hits_yday` = 0";
                }
                continue;
            }
            $pieces[]="`{$field}` = ".($bool?"{$field}+{$hit}":$hit);
        }
        return implode(',', $pieces);
    }
    public static function update_hits($table,$id=1,$primary='id'){
        $sql = self::update_hits_sql(false,0);
        $sql && iDB::query("UPDATE `{$table}` SET {$sql} WHERE `{$primary}` ='$id'");
        $sql = self::update_hits_sql();
        $sql && iDB::query("UPDATE `{$table}` SET {$sql} WHERE `{$primary}` ='$id'");
    }
}
