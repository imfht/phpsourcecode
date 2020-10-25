<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
class searchApp {
    public $methods = array('iCMS');
    public static $router   = null;
    public static $data     = array();
    public static $callback = array();
	public function do_iCMS(){
        return $this->search();
	}
    public function API_iCMS(){
        return $this->search();
    }
    public function search($tpl=false) {
        $q  = rawurldecode($_GET['q']);
        $q  = iSecurity::encoding($q);
        $q  = iSecurity::escapeStr($q);

        filterApp::$disable = iCache::get('search/disable');
        $fwd = iPHP::callback(array("filterApp","run"),array(&$q),false);
        $fwd && iPHP::error_404('非法搜索词!', 60002);

        self::$data['keyword'] = $q;
        self::$data['title']   = stripslashes($q);
        self::$data['iurl']    = (array)self::iurl($q);
        $q && $this->search_log($q);
        $tpl===false && $tpl = '{iTPL}/search.htm';
        return appsApp::render(self::$data,$tpl,'search');
    }
    public static function iurl($q,$query=null,$page=true) {
        $query===null && $query = array('app'=>'search','q'=>$q);
        $iURL           =  new stdClass();
        $iURL->url      = iURL::make($query,self::$router?:'router::api');
        $iURL->pageurl  = iURL::make('page={P}',$iURL->url);
        $iURL->href     = $iURL->url;
        if (self::$callback['iurl'] && is_callable(self::$callback['iurl'])) {
            $iURL = call_user_func_array(self::$callback['iurl'],array($iURL,$query));
        }
        $page && iURL::page_url($iURL);
        return $iURL;
    }
    private function search_log($search){
        // $interval = 30;
        // $ip    = iPHP::get_ip();
        $time  = time();
        // $key   = 'search/'.$ip;
        // $stime = iCache::get($key);

        // if($stime && $time-$stime<$interval){
        //     iPHP::error_404('您搜索太快休息下,'.format_time($interval,'cn').'之后再继续', 60003);
        // }
        // iCache::set($key,$time,$interval);

        $sid  = iDB::value("SELECT `id` FROM `#iCMS@__search_log` WHERE `search` = '$search' LIMIT 1");
        if($sid){
            iDB::query("
                UPDATE `#iCMS@__search_log`
                SET `times` = times+1
                WHERE `id` = '$sid';
            ");
        }else{
            iDB::query("
                INSERT INTO `#iCMS@__search_log` (`search`, `times`, `addtime`)
                VALUES ('$search', '1', '".$time."');
            ");
        }
    }
}
