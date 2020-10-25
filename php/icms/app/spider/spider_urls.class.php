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

spider_urls::$timer[0] = time();

class spider_urls {
    public static $urls  = null;
    public static $rule  = null;
    public static $work  = null;
    public static $ids   = array();
    public static $timer = array();

    public static function crawl($work = NULL,$pid = NULL,$_rid = NULL,$_urls=null,$callback=null,$_nocheck=false) {
        @set_time_limit(0);
        $pid === NULL && $pid = spider::$pid;

        if ($pid) {
            $project = spider_project::get($pid);
            $cid = $project['cid'];
            $rid = $project['rid'];
            $prule_list_url = $project['list_url'];
            $lastupdate     = $project['lastupdate'];
        } else {
            $cid = spider::$cid;
            $rid = spider::$rid;
        }

        if($_rid !== NULL) $rid = $_rid;

        if($work=='shell'){
            $lastupdate = $project['lastupdate'];
            if($project['psleep']){
                if(time()-$lastupdate<$project['psleep']){
                    echo date("Y-m-d H:i:s ").'采集方案['.$pid."]:".format_date($lastupdate)."刚采集过了,请".($project['psleep']/3600)."小时后在继续采集\n";
                    return;
                }
            }
            if($pid){
                echo date("Y-m-d H:i:s ")."\033[32m开始采集方案[".$pid."] \033[0m\n";
            }
            if($rid){
                echo date("Y-m-d H:i:s ")."\033[32m使用采集规则[".$rid."] \033[0m\n";
            }
        }

        $srule = spider_rule::get($rid);
        $rule  = $srule['rule'];
        self::$rule && $rule = self::$rule;
        $urls  = $rule['list_urls'];

        $project['urls']&& $urls = $project['urls'];
        self::$urls     && $urls = self::$urls;
        $_urls          && $urls = $_urls;

        self::$ids = array('pid'=>$pid,'sid'=>$sid,'rid'=>$rid);

        $urlsArray = self::make_list_urls($urls,$work);
        if(empty($urlsArray)){
            if($work=='shell'){
                spider_error::log("采集列表为空!请填写!",$url,'urls.empty',self::$ids);
                echo PHP_EOL;
                return false;
            }
            iUI::alert('采集列表为空!请填写!', 'js:parent.window.iCMS_MODAL.destroy();');
        }

//      if(spider::$ruleTest){
//          echo "<pre>";
//          print_r(iSecurity::escapeStr($project));
//          print_r(iSecurity::escapeStr($rule));
//          echo "</pre>";
//          echo "<hr />";
//      }
        if($rule['mode']=="2"){
            iPHP::vendor('phpQuery');
            spider::$ruleTest && $_GET['pq_debug'] && phpQuery::$debug =1;
        }

        $pubArray           = array();
        $pubCount           = array();
        $pubAllCount        = array();
        $urlsAllCount       = count($urlsArray);

        spider::$curl_proxy = $rule['proxy'];
        spider::$urlslast   = null;

        if (spider::$ruleTest) {
            echo '<b>最终需抓取列表总共:</b>'.$urlsAllCount. "条<br />";
            echo '<pre>';
            print_r($urlsArray);
            echo '</pre>';
            $urlsArray = array(reset($urlsArray));
            echo '<b>测试第一条</b><br />';
        }
        if($work=='shell'){
            echo date("Y-m-d H:i:s ")."\033[36m最终需抓取列表总共(".$urlsAllCount.")条\033[0m\n";
        }

        $lastkey_file = iPHP_APP_CACHE."/spider.{$pid}.lastkey.pid";

        foreach ($urlsArray AS $key => $url) {
            $url = trim($url);
            spider::$urlslast = $url;

            if($pid && $work=='shell'){
                if(file_exists($lastkey_file)){
                    $lastkey     = file_get_contents($lastkey_file);
                    $lastkeytime = filemtime($lastkey_file);
                    if(trim($lastkey)>$key && time()-$lastkeytime < $project['psleep']){
                        iPHP_SHELL && print date("Y-m-d H:i:s ")."\033[32m[".$key.'] '.$url." 该列表已经抓取过...\033[0m\n";
                        continue;
                    }
                }
                file_put_contents($lastkey_file, $key);
            }


            if($work=='shell'){
                echo date("Y-m-d H:i:s ")."\033[32m开始抓取 ".$key.'/'.$urlsAllCount.' 条列表链接:'.$url."\033[0m\n";
            }

            if (spider::$ruleTest) {
                echo '<b>抓取列表:</b>'.$url . "<br />";
            }
            $html = spider_tools::remote($url,'spider_urls::crawl');
            if(empty($html)){
                $msg = "采集列表内容为空!";
                $msg.= var_export(spider_tools::$curl_info,true);
                spider_error::log($msg,$url,'url.empty',self::$ids);
                echo PHP_EOL;
                continue;
            }
            $rule['list_urls_format'] && $html = spider_tools::dataClean($rule['list_urls_format'], $html);
            if ($rule['list_urls_format'] && spider::$ruleTest) {
                echo '<b>列表采集结果整理结果:</b><div style="max-height:300px;overflow-y: scroll;">';
                echo iSecurity::escapeStr($html);
                echo "</div><hr />";
            }
            if($rule['mode']=="2"){
                $doc       = phpQuery::newDocumentHTML($html,'UTF-8');
                $list_area = $doc[trim($rule['list_area_rule'])];
                // if(strpos($rule['list_area_format'], 'DOM::')!==false){
                //     $list_area = spider_tools::dataClean($rule['list_area_format'], $list_area);
                // }

                if($rule['list_area_format']){
                    $list_area_format = trim($rule['list_area_format']);
                    //ARRAY::div.class
                    if(strpos($list_area_format, 'ARRAY::')!==false){
                        $list_area_format = str_replace('ARRAY::', '', $list_area_format);
                        $lists = array();
                        foreach ($list_area as $la_key => $la) {
                            $lists[] = phpQuery::pq($list_area_format,$la);
                        }
                    }else{
                        $lists = phpQuery::pq($list_area_format,$list_area);
                    }
                }else{
                    $lists = $list_area;
                }

                // $lists = $list_area;
                //echo 'list:getDocumentID:'.$lists->getDocumentID()."\n";
            }elseif($rule['mode']=="3"){
                $list_area = json_decode($html,true);
                if (spider::$ruleTest && is_null($list_area)) {
                    echo '<b>JSON ERROR:'.json_last_error_msg().'</b>';
                    echo "<hr />";
                }

                if($rule['list_area_rule']){
                    $list_area_rule = explode('->', $rule['list_area_rule']);
                    $level = 0;
                    $lists = spider_tools::array_filter_key($list_area,$list_area_rule,$level);
                }else{
                    $lists = $list_area;
                }
                if ($rule['list_area_format']) {
                    $lists = spider_tools::dataClean($rule['list_area_format'], $lists);
                }
            }else{
                $list_area_rule = spider_tools::pregTag($rule['list_area_rule']);
                if ($list_area_rule && $rule['list_area_rule']!='<%content%>') {
                    preg_match('|' . $list_area_rule . '|is', $html, $matches);
                    $list_area = $matches['content'];
                } else {
                    $list_area = $html;
                }


                if (spider::$ruleTest) {
                    echo iSecurity::escapeStr($rule['list_area_rule']);
                    echo "<hr />";
                }
                if ($rule['list_area_format']) {
                    $list_area = spider_tools::dataClean($rule['list_area_format'], $list_area);
                }

                preg_match_all('|' . spider_tools::pregTag($rule['list_url_rule']) . '|is', $list_area, $lists, PREG_SET_ORDER);
            }

            $html = null;
            unset($html);

            if (spider::$ruleTest) {
                echo '<b>列表区域规则:</b>'.iSecurity::escapeStr($rule['list_area_rule']);
                echo "<hr />";
                echo '<b>列表区域抓取结果:</b><div style="max-height:300px;overflow-y: scroll;">';
                if(is_array($list_area)){
                    echo "<pre>";var_dump($list_area);echo "</pre>";
                }else{
                    echo iSecurity::escapeStr($list_area);
                }
                echo '</div>';
                echo "<hr />";
                echo '<b>列表链接规则:</b>'.iSecurity::escapeStr($rule['list_url_rule']);
                echo "<hr />";
                if($prule_list_url){
                    echo '<b>方案网址合成规则:</b>'.iSecurity::escapeStr($prule_list_url);
                }else{
                    echo '<b>规则网址合成规则:</b>'.iSecurity::escapeStr($rule['list_url']);
                }
                echo "<hr />";
            }
            $list_area = null;
            unset($list_area);

            if($prule_list_url){
                $rule['list_url']   = $prule_list_url;
            }

            $urlsData = self::lists_item_data($lists,$rule,$url);
            if ($rule['sort'] == "1") {
                //arsort($lists);
            } elseif ($rule['sort'] == "2") {
                krsort($urlsData);
            } elseif ($rule['sort'] == "3") {
                shuffle($urlsData);
            }

            if (spider::$callback['urls'] && is_callable(spider::$callback['urls'])) {
                $_work = call_user_func_array(spider::$callback['urls'],array(&$urlsData,$url));
                if($_work===false){
                    continue;
                }
                $_work && $work = $_work;
            }

            $urlsDataCount = count($urlsData);

            if(empty($urlsDataCount)){
                spider_error::log("采集列表记录为0!",$url,'url.zero',self::$ids,false);
                continue;
            }

            //PID@xx 返回URL列表
            if($callback=='CALLBACK@URL'){
                $cbListUrl = array();
                foreach ($urlsData AS $lkey => $value) {
                    if($value['url']===false){
                        continue;
                    }
                    // if(spider::checker($work)===true){
                        $cbListUrl[] = $value['url'];
                    // }
                }
                return $cbListUrl;
            }

            if($work=="WEB@MANUAL"){
                $listsArray[$url] = $urlsData;
            }
            if($work=="shell"){
                $pubCount[$key]['url']   = $url;
                $pubCount[$key]['count'] = $urlsDataCount;
                $pubAllCount['count']+=$pubCount[$key]['count'];
                echo date("Y-m-d H:i:s ")."\033[32m开始采集 列表 ".$url." (".$pubCount[$key]['count'].")条记录\033[0m\n\n";
                $_index = 1;
                foreach ($urlsData AS $lkey => $value) {
                    if($value['url']===false) continue;

                    spider::$title = $value['title'];
                    spider::$url   = $value['url'];

                    echo date("Y-m-d H:i:s ")."\033[32m开始采集...(".$_index."/".$urlsDataCount.")\033[0m\n";
                    echo date("Y-m-d H:i:s ")."\033[36mtitle:\033[0m".spider::$title."\n";
                    echo date("Y-m-d H:i:s ")."\033[36murl:\033[0m".spider::$url."\n";
                    $_index++;

                    spider::$rid = $rid;
                    $hash        = md5(spider::$url);
                    $checker     = spider::checker($work,$pid,spider::$url,spider::$title);
                    if($checker===true){
                        $wait = 3;
                        $wait_start = time();
                        $callback  = spider::publish("shell");
                        if ($callback['code'] == "1001") {
                            $pubCount[$key]['success']++;
                            $pubAllCount['success']++;
                            $wait+= time()-$wait_start;
                            echo date("Y-m-d H:i:s ")."\033[32m采集完成并发布成功".str_repeat('.',$wait)."√\033[0m\n\n";
                            if($project['sleep']){
                                if($rule['mode']!="2"){
                                    unset($lists[$lkey]);
                                }
                                gc_collect_cycles();

                                $usleep = $project['sleep']*1000;
                                echo date("Y-m-d H:i:s ")."\033[31m暂停".($project['sleep']/1000)."秒后继续\033[0m\n\n";
                                usleep($usleep); //1000000 = 1s
                            }else{
                                //sleep(1);
                            }
                        }else{
                            $pubCount[$key]['error']++;
                            $pubAllCount['error']++;
                            echo date("Y-m-d H:i:s ")."\033[31m error \033[0m\n\n";
                            continue;
                        }
                    }else{
                        $pubCount[$key]['published']++;
                        $pubAllCount['published']++;
                    }
                }
                if($rule['mode']=="2"){
                    phpQuery::unloadDocuments($doc->getDocumentID());
                }else{
                    unset($lists);
                }
            }
            if($work=="WEB@AUTO"||$work=='DATA@RULE'){
                spider::$spider_url_ids = array();
                foreach ($urlsData AS $lkey => $value) {
                    if($value['url']===false) continue;

                    spider::$title = $value['title'];
                    spider::$url   = $value['url'];
                    unset($value['title'],$value['url']);

                    $hash  = md5(spider::$url);
                    if (spider::$ruleTest) {
                        echo '<b>列表抓取结果:</b>'.$lkey.'<br />';
                        echo spider::$title . ' (<a href="' . __ADMINCP__ . '=spider_project&do=test'.
                            '&url=' . urlencode(spider::$url) .
                            '&rid=' . $rid .
                            '&pid=' . $pid .
                            '&title=' . urlencode(spider::$title) .
                            '" target="_blank">测试内容规则</a>) <br />';
                        echo spider::$url . "<br />";
                        echo $hash . "<br />";
                        if($value){
                            echo '<b>其它采集结果:</b>';
                            echo '<pre>';
                            var_dump(array_map('htmlspecialchars', $value));
                            echo '</pre>';
                        }
                        echo "<hr />";
                    } else {
                        $_flag = spider::$dataTest;
                        $_flag OR $_flag = $_nocheck;
                        $_flag OR $_flag = (spider::checker($work,$pid,spider::$url,spider::$title)===true);
                        if($_flag){
                            $suData = array(
                                'sid'   => 0,
                                'url'   => spider::$url,'title' => spider::$title,
                                'cid'   => $cid,'rid' => $rid,'pid' => $pid,
                                'hash'  => $hash
                            );
                            switch ($work) {
                                case 'DATA@RULE':
                                    $contentArray[$lkey] = spider_data::crawl($pid,$rid,spider::$url,spider::$title);
                                    if(iPHP_SHELL){
                                        echo "\t".spider::$url.PHP_EOL;
                                    }
                                    // $contentArray[$lkey] = spider_urls::crawl($work,$_pid);
                                    unset($suData['sid']);
                                    $suData['title'] = addslashes($suData['title']);
                                    $suData = array_merge($suData,array(
                                        'addtime' => time(),
                                        'status'  => '2','publish' => '2',
                                        'indexid' => '0','pubdate' => '0'
                                    ));
                                    if($_nocheck||spider::$dataTest){

                                    }else{
                                        $suid = iDB::insert('spider_url',$suData);
                                        // iDB::insert('spider_url_data',array('id'=>$suid,'data'=>addslashes(json_encode($value))));
                                    }
                                    spider::$spider_url_ids[$lkey] = $suid;
                                break;
                                case 'WEB@AUTO':
                                    $pubArray[] = $suData;
                                break;
                            }
                        }
                    }
                }
            }
        }
        $lists = null;
        unset($lists);
        gc_collect_cycles();

        switch ($work) {
            case 'WEB@AUTO':
                return $pubArray;
            break;
            case 'DATA@RULE':
                return $contentArray;
            break;
            case 'WEB@MANUAL':
                return array(
                    'cid'        => $cid,
                    'rid'        => $rid,
                    'pid'        => $pid,
                    'sid'        => $sid,
                    'work'       => $work,
                    'rule'       => $rule,
                    'listsArray' => $listsArray
                );
            break;
            case "shell":
                iDB::update('spider_project',array('lastupdate'=>time()),array('id'=>$pid));
                self::$timer[1] = time();

                echo str_repeat("=", 30).PHP_EOL;
                $logfile = iPHP_APP_CACHE."/spider.{$pid}.log";
                echo date("Y-m-d H:i:s ")."\033[33m采集数据统结果\033[0m\n";
                print_r($pubAllCount);
                echo date("Y-m-d H:i:s ")."\033[33m全部采集完成\033[0m\n";
                echo date("Y-m-d H:i:s ")."\033[33m用时:".format_time((self::$timer[1]-self::$timer[0]),'cn').",".date("Y-m-d H:i:s",self::$timer[0])."-".date("Y-m-d H:i:s",self::$timer[1])."\033[0m\n";
                echo date("Y-m-d H:i:s ")."\033[33m详细采集结果请查看:".iSecurity::filter_path($logfile)."\033[0m\n";
                echo str_repeat("=", 30).PHP_EOL;

                file_exists($lastkey_file) && @unlink($lastkey_file);
                file_put_contents($logfile, var_export($pubCount,true));
                file_put_contents($logfile, var_export($pubAllCount,true),FILE_APPEND);
            break;
        }
    }
    /**
     * 列表生成
     * @param  [type] $urls [description]
     * @return [type]       [description]
     */
    public static function make_list_urls($urls,$work){
        $urlsArray  = explode("\n", $urls);
        $urlsArray  = array_filter($urlsArray);
        $_urlsArray = $urlsArray;
        $urlsList   = array();
        if($work=='shell'){
            // echo "$urls\n";
            print_r($urlsArray);
        }

        foreach ($_urlsArray AS $_key => $_url) {
            $_url = trim($_url);
            if(empty($_url)){
                continue;
            }

            $_url      = htmlspecialchars_decode($_url);
            $_urlsList = array();
            /**
             * RULE@rid@url
             * url使用[rid]规则采集并返回列表结果
             */
            if(strpos($_url, 'RULE@')!==false){
                if($work=='shell'){
                    echo str_repeat("-=", 30).PHP_EOL;
                }
                list($___s,$_rid,$_urls) = explode('@', $_url);
                if (spider::$ruleTest) {
                    print_r('<b>使用[rid:'.$_rid.']规则抓取列表</b>:'.$_urls);
                    echo "<hr />";
                }
                $_urlsList = (array)self::crawl($work,false,$_rid,$_urls,'CALLBACK@URL');

                if($work=='shell'){
                    echo date("Y-m-d H:i:s ").'使用[rid:'.$_rid.']规则抓取列表'.PHP_EOL;
                    echo date("Y-m-d H:i:s ")."获取链接:".count($_urlsList).'条记录'.PHP_EOL;
                }

                foreach ($_urlsList as $uk => $vurl) {
                    $urls_match = self::urls_match($vurl);
                    if($urls_match){
                        $urlsList  = array_merge($urlsList,$urls_match);
                        unset($_urlsList[$uk]);
                    }
                }
                $_urlsList && $urlsList  = array_merge($urlsList,$_urlsList);
                unset($urlsArray[$_key]);
                if($work=='shell'){
                    echo str_repeat("-=", 30).PHP_EOL;
                }
            }else{
                $urls_match = self::urls_match($_url);
                if($urls_match){
                    $urlsList  = array_merge($urlsList,$urls_match);
                    unset($urlsArray[$_key]);
                }
            }
        }
        $urlsList && $urlsArray = array_merge($urlsArray,$urlsList);
        unset($_urlsArray,$_key,$_url,$_matches,$_urlsList,$urlsList,$urls_match);
        $urlsArray = array_filter($urlsArray);
        $urlsArray = array_unique($urlsArray);
        return $urlsArray;
    }
    /**
     * 列表链接规则
     * @param  [type] $lists [description]
     * @param  [type] $rule  [description]
     * @param  [type] $url   [description]
     * @return [type]        [description]
     */
    public static function lists_item_data($lists,$rule,$url){
        if (spider::$callback['lists_item_data'] && is_callable(spider::$callback['lists_item_data'])) {
            return call_user_func_array(spider::$callback['lists_item_data'],array($lists,$rule,$url));
        }
        $array = array();
        if($lists)foreach ($lists AS $lkey => $row) {
            $LD = array();
            $data = spider_tools::listItem($row,$rule,$url);
            if($data)foreach ($data as $key => $value) {
                if(is_numeric($key)|| strpos($key, 'var_')!==false){
                    unset($data[$key]);
                }
                if(strpos($key, 'var_')===false && $key!='title' && $key!='url'){
                    $LD[$key] = $value;
                }
            }
            $data['url'] && $LD && spider_tools::listData($data['url'],$LD);
            $data && $array[$lkey] = $data;
        }
        return $array;
    }
    /**
     * 列表网址生成
     * @param  [type] $_url [description]
     * @return [type]       [description]
     */
    public static function urls_match($url){
        preg_match_all('|<(.*?)>|',$url, $matches);
        foreach ($matches[1] as $key => $value) {
           $url = self::urls_make($url,$value);
        }
        return (array)$url;
    }
    public static function urls_make($url,$rule){
        $urlsList = array();
        if(is_array($url)){
            foreach ($url as $key => $vurl) {
                $_urlsList = self::urls_make($vurl,$rule);
                $urlsList = array_merge($urlsList,$_urlsList);
            }
        }else{
            if(strpos($rule, 'DATE:')!==false){
                list($type,$format) = explode(':',$rule);
                $urlsList[]= str_replace('<'.$rule.'>', date($format),trim($url));
            }elseif(strpos($rule, 'FOR:')!==false){
                //<FOR:1-100>
                list($type,$format) = explode(':',$rule);
                list($start,$end) = explode('-', $format);
                if($start>$end){
                //<FOR:100-1>
                    for ($i = $start; $i >=$end; $i--) {
                        $urlsList[]= str_replace('<'.$rule.'>', $i,trim($url));
                    }
                }else{
                //<FOR:1-100>
                    for ($i = $start; $i <=$end; $i++) {
                        $urlsList[]= str_replace('<'.$rule.'>', $i,trim($url));
                    }
                }
            }elseif(strpos($rule, 'EACH:')!==false){
                //<EACH:1,2,3,4>
                list($type,$format) = explode(':',$rule);
                $array = explode(',', $format);
                foreach ($array as $key => $value) {
                    $urlsList[]= str_replace('<'.$rule.'>', $value,trim($url));
                }
            }else{
                list($format,$begin,$num,$step,$zeroize,$reverse) = explode(',',$rule);
                $url = str_replace($rule, '*',trim($url));
                $_urlsList = spider_tools::mkurls($url,$format,$begin,$num,$step,$zeroize,$reverse);
                $urlsList = array_merge($urlsList,$_urlsList);
            }
        }
        return $urlsList;
    }

}
