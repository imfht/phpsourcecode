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

class spider_data {

    public static function crawl($_pid = NULL,$_rid = NULL,$_url = NULL,$_title = NULL,$_cid = NULL) {
        @set_time_limit(0);
        $sid = spider::$sid;
        if ($sid) {
            $sRs   = iDB::row("SELECT * FROM `#iCMS@__spider_url` WHERE `id`='$sid' LIMIT 1;");
            $title = $sRs->title;
            $cid   = $sRs->cid;
            $pid   = $sRs->pid;
            $url   = $sRs->url;
            $rid   = $sRs->rid;
       } else {
            $title = spider::$title;
            $cid   = spider::$cid;
            $pid   = spider::$pid;
            $url   = spider::$url;
            $rid   = spider::$rid;

            $_title === NULL OR $title = $_title;
            $_cid   === NULL OR $cid = $_cid;
            $_pid   === NULL OR $pid = $_pid;
            $_url   === NULL OR $url = $_url;
            $_rid   === NULL OR $rid = $_rid;
        }

        if($pid){
            $project        = spider_project::get($pid);
            $prule_list_url = $project['list_url'];
        }

        $srule           = spider_rule::get($rid);
        $rule            = $srule['rule'];
        $dataArray       = $rule['data'];

        if($prule_list_url){
            $rule['list_url']   = $prule_list_url;
        }

        if (spider::$dataTest) {
            echo "<b>抓取规则信息</b><pre style='max-height:300px;overflow-y: scroll;'>";
            print_r(iSecurity::escapeStr($srule));
            print_r(iSecurity::escapeStr($project));
            echo "</pre><hr />";
        }

        $rule['proxy']          && spider::$curl_proxy = $rule['proxy'];
        $rule['data_charset']   && spider::$charset = $rule['data_charset'];
        $rule['data_user_agent']&& spider::$useragent = $rule['data_user_agent'];

        $responses = array();
        $html      = spider_tools::remote($url,'spider_data::crawl');
        if(empty($html)){
            $msg = "采集错误 " . $url . ' 内容为空'.PHP_EOL;
            $msg.= '请检查请求是否正常'.PHP_EOL;
            $msg.= var_export(spider_tools::$curl_info,true);
            if(spider::$work=='shell'){
                echo PHP_EOL;
                spider_error::log($msg,$url,'data.empty',array('pid'=>$pid,'sid'=>$sid,'rid'=>$rid));
                return false;
            }else{
                iUI::alert($msg);
            }
        }

        spider::$allHtml        = array();
        $rule['__url__']        = spider::$url;
        $responses['reurl']     = spider::$url;
        $responses['__title__'] = $title;

        self::set_http_config($rule);
        self::set_watermark_config($rule);

        $subArray = array();

        foreach ((array)$dataArray AS $key => $data) {
            $dname = $data['name'];
            if(empty($dname)){
                continue;
            }

            //子采集
            //ooxx@字段,ooxx@URLS
            //@URLS 必需设置
            //chapter@URLS
            //chapter@title
            if(strpos($dname, '@')!==false){
                list($sn,$sf) = explode('@', $dname);
                if($sf==='POST'){
                    $subArray[$sn]['POST'] = $data['rule'];
                    continue;
                }
                if($sf!=='URLS'){
                    $subArray[$sn]['RULES'][$sf] = $data;
                    continue;
                }
            }

            $content_html = $html;
            //设置数据源
            if($data['data@source']){
                $content_html = spider_tools::getDATA($responses,$data['data@source']);
            }

            /**
             * [UNSET:name]
             * 注销[name]
             * @var string
             */
            if (strpos($dname,'UNSET:')!== false){
                $_dname = str_replace('UNSET:', '', $dname);
                unset($responses[$_dname]);
                continue;
            }
            /**
             * [DATA:name]
             * 把之前[name]处理完的数据当作原始数据
             * 如果之前有数据会叠加
             * 用于数据多次处理
             * @var string
             */
            if (strpos($dname,'DATA:')!== false){
                $_dname = str_replace('DATA:', '', $dname);
                $content_html = $responses[$_dname];
                unset($responses[$dname]);
            }
            /**
             * [PRE:name] 前置采集
             * 把PRE:name采集到的数据 当做原始数据
             * 一般用于抓取内容
             */
            $pre_dname = 'PRE:'.$dname;
            if(isset($responses[$pre_dname])){
                $content_html = $responses[$pre_dname];
                unset($responses[$pre_dname]);
            }

            /**
             * [EMPTY:name]
             * 如果[name]之前抓取结果数据为空使用这个数据项替换
             */
            if (strpos($dname,'EMPTY:')!== false){
                $_dname = str_replace('EMPTY:', '', $dname);
                if(empty($responses[$_dname])){
                    $dname = $_dname;
                }else{
                    //有值不执行抓取
                    continue;
                }
            }


            $content = spider_content::crawl($content_html,$data,$rule,$responses);
            if($content === null){
                $responses[$dname] = null;
                continue;
            }
            if($data['empty']){
                $empty = spider_tools::real_empty($content);
                if(empty($empty)){
                    return spider_error::msg(
                        '规则设置了不允许为空.当前抓取结果为空!请检查,规则是否正确!',
                        'content.empty',
                        $data['name'],$rule['__url__']
                    );
                }
            }
            //子采集
            //ooxx@URLS 获取主采集,抓取的链接
            if(strpos($dname, '@URLS')!==false){
                $subArray[$sn][$sf] = $content;
                continue;
            }

            unset($content_html);
            //转换二维组
            if (strpos($dname,'ARRAY:')!== false){
                $dname = str_replace('ARRAY:', '', $dname);
                $cArray = array();

                foreach ((array)$content as $k => $value) {
                    foreach ((array)$value as $key => $val) {
                        $cArray[$key][$k]=$val;
                    }
                }
                if($cArray){
                    $content = $cArray;
                    unset($cArray);
                }
            }

            /**
             * [name.xxx]
             * 采集内容做为数组
             */
            if (strpos($dname,'.')!== false){
                $f_key = substr($dname,0,stripos($dname, "."));
                $s_key = substr(strrchr($dname, "."), 1);
                // $responses = str_multi_array($dname,'.',$content);
                if(isset($responses[$f_key][$s_key])){
                    if(is_array($responses[$f_key][$s_key])){
                        $responses[$f_key][$s_key] = array_merge((array)$responses[$f_key][$s_key],(array)$content);
                    }else{
                        $responses[$f_key][$s_key].= $content;
                    }
                }else{
                    $responses[$f_key][$s_key] = $content;
                }
            }else{
                /**
                 * 多个name 内容合并
                 */
                if(isset($responses[$dname])){
                    if(is_array($responses[$dname])){
                        $responses[$dname] = array_merge((array)$responses[$dname],(array)$content);
                    }else{
                        $responses[$dname].= $content;
                    }
                }else{
                    $responses[$dname] = $content;
                }
            }
            /**
             * 对匹配多条的数据去重过滤
             */
            if(!is_array($responses[$dname]) && $data['multi']){
                if(strpos($responses[$dname], ',')!==false){
                    $_dnameArray = explode(',', $responses[$dname]);
                    $dnameArray  = array();
                    foreach ((array)$_dnameArray as $key => $value) {
                        $value = trim($value);
                        $value && $dnameArray[]=$value;
                    }
                    $dnameArray = array_filter($dnameArray);
                    $dnameArray = array_unique($dnameArray);
                    $responses[$dname] = implode(',', $dnameArray);
                    unset($dnameArray,$_dnameArray);
                }
            }

            gc_collect_cycles();
        }
        foreach ($responses as $key => $value) {
            if(strpos($key, ':')!==false){
                unset($responses[$key]);
            }else{
                if($key!='body'){
                    $responses[$key] = str_replace('#--iCMS.PageBreak--#', ',', $responses[$key]);
                }
            }
            if(is_array($responses[$key])){
                $responses[$key] = array_filter($responses[$key]);
                $responses[$key] = array_unique($responses[$key]);
            }
        }

        if(isset($responses['title']) && empty($responses['title'])){
            $responses['title'] = $responses['__title__'];
        }

        spider::$allHtml = array();
        unset($html);

        gc_collect_cycles();

        //子采集 可独立发布
        if($subArray){
            if(spider::$dataTest){
                $subData = array();
                foreach ($subArray as $key => $value) {
                    $value['URLS'] && $subData[] = self::sub_crawl($value,$rule);
                }
            }else{
                $responses['commit:callData'] = $subArray;
            }
        }

        if (spider::$dataTest) {
            echo "<b>最终采集结果:</b>";
            echo "<pre style='width:99%;word-wrap: break-word;white-space: pre-wrap;'>";
            print_r(iSecurity::escapeStr($responses));
            if($subArray){
                echo '<br /><div style="padding: 20px;background-color: #ebebeb;">';
                echo "<b>子采集结果(只测试第一条):</b><br />";
                print_r(iSecurity::escapeStr($subData));
                echo "</div>";
            }
            echo '<hr />';
            echo '使用内存:'.iFS::sizeUnit(memory_get_usage()).' 执行时间:'.iPHP::timer_stop().'s';
            echo "</pre>";
        }

        if (spider::$callback['data'] && is_callable(spider::$callback['data'])) {
            $responses = call_user_func_array(spider::$callback['data'],array($responses,$rule));
        }

        return $responses;
    }
    /**
     * [sub_crawl 执行子采集]
     * @param  [type] $sub  [子采集配置]
     * @param  [type] $rule [主采集规则]
     * @param  [type] $indexid [indexid]
     * @return [type]       [description]
     */
    public static function sub_crawl($sub,$rule,$indexid=0){
        $sPOST = $sub['POST'];
        $urls  = $sub['URLS'];
        $RULES = $sub['RULES'];
        if(!is_array($urls) && $urls){
            $urls = explode(",", $urls);
        }
        if(empty($urls)) return;

        $count = count($urls);

        if (spider::$dataTest) {
            echo '<div style="padding: 20px;background-color: #ebebeb;">';
            echo "<h3>执行子采集:</h3>";
            echo "<hr />";
        }else{
            $_POST = array();
            if($sPOST && strpos($sPOST, 'poid@')!==false){
                list($_s,$poid) = explode('@', $sPOST);
                $spost  = spider_post::get($poid);
                // $appid   = $spost->app;
                if($indexid && $spost->primary){
                    $_POST[$spost->primary] = $indexid;
                }
                $rcode = '1002';
            }
            if(spider::$work=='shell'){
                echo date("Y-m-d H:i:s ")."\033[32m执行子采集[".$poid."] 共".$count."条 \033[0m\n";
            }
        }

        $responses = array();
        $index = 1;
        foreach ($urls as $key => $url) {
            $data = self::sub_crawl_data($url,$RULES,$rule);
            if (spider::$dataTest) {
                $responses[$key] = $data;
                break;
            }
            if($spost){
                $_POST = array_merge($_POST,$data);
                $ret = spider_post::commit($rcode,false,$spost);
                is_array($ret) && $id = $ret[$spost->primary];
                if($id){
                    spider_tools::insert_urls($id,$_POST['reurl'],$spost->app);
                    if(spider::$work=='shell'){
                        echo date("Y-m-d H:i:s ")."(".$index."/".$count.")  \033[36mId:\033[0m".$id."\n";
                        $index++;
                    }
                }else{
                    spider_error::log('子采集发布错误',$url,'sub_crawl.empty',array('pid'=>spider::$pid,'sid'=>spider::$sid,'rid'=>spider::$rid));
                }
                $responses[$key] = $id;
            }
        }

        spider::$dataTest && print "</div>";

        return $responses;
    }
    //子采集数据
    public static function sub_crawl_data($url,$RULES,$rule){
        $responses = array();
        $responses['reurl'] = $url;
        $html = spider_tools::remote($url,'spider_data::sub_crawl_data');
        foreach ($RULES as $dname => $dvar) {
            $content = spider_content::crawl($html,$dvar,$rule,$responses);
            $responses[$dname] = $content;
        }
        return $responses;
    }

    public static function set_http_config($rule){
        iHttp::$CURLOPT_ENCODING       = '';
        iHttp::$CURLOPT_REFERER        = '';
        iHttp::$CURLOPT_TIMEOUT        = 10;
        iHttp::$CURLOPT_CONNECTTIMEOUT = 3;
        $rule['http']['ENCODING']      && iHttp::$CURLOPT_ENCODING        = $rule['http']['ENCODING'];
        $rule['http']['REFERER']       && iHttp::$CURLOPT_REFERER         = $rule['http']['REFERER'];
        $rule['http']['TIMEOUT']       && iHttp::$CURLOPT_TIMEOUT         = $rule['http']['TIMEOUT'];
        $rule['http']['CONNECTTIMEOUT']&& iHttp::$CURLOPT_CONNECTTIMEOUT  = $rule['http']['CONNECTTIMEOUT'];
    }
    public static function set_watermark_config($rule){
        files::$watermark_config['pos'] = iCMS::$config['watermark']['pos'];
        files::$watermark_config['x']   = iCMS::$config['watermark']['x'];
        files::$watermark_config['y']   = iCMS::$config['watermark']['y'];
        files::$watermark_config['img'] = iCMS::$config['watermark']['img'];
        files::$watermark_enable        = iCMS::$config['watermark']['enable'];

        if($rule['watermark_mode']){
            files::$watermark_config['pos'] = $rule['watermark']['pos'];
            files::$watermark_config['x']   = $rule['watermark']['x'];
            files::$watermark_config['y']   = $rule['watermark']['y'];
            $rule['watermark']['img'] && files::$watermark_config['img'] = $rule['watermark']['img'];
        }
        if($rule['watermark_mode']=="2"){
            files::$watermark_enable = false;
        }
    }
}
