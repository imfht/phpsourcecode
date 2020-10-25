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

class spider_tools {
    public static $listArray   = array();
    public static $curl_info   = array();
    public static $safe_port   = array('80','443');//检测采集url端口
    public static $safe_url    = false; //是否检测采集url安全性
    public static $curl_proxy  = false;
    public static $proxy_array = array();
    public static $callback    = array();
    public static $debug       = true;
    public static $handle      = null;

    public static $CURL_INFO              = null;
    public static $CURL_ERRNO             = 0;
    public static $CURL_ERROR             = null;
    public static $CURLOPT_ENCODING       = null;
    public static $CURLOPT_REFERER        = null;
    public static $CURLOPT_TIMEOUT        = 60; //数据传输的最大允许时间
    public static $CURLOPT_CONNECTTIMEOUT = 15; //连接超时时间
    public static $CURLOPT_USERAGENT      = null;
    public static $CURLOPT_COOKIE         = null;
    public static $CURLOPT_COOKIEFILE     = null;
    public static $CURLOPT_COOKIEJAR      = null;
    public static $CURLOPT_HTTPHEADER     = null;
    /**
     * 在数据项里调用之前采集的数据[DATA@name][DATA@name.key]
     * [DATA@list:name]调用列表其它数据
     */
    public static function getDATA($responses,$content){
        preg_match_all('#\[DATA@(.*?)\]#is', $content,$data_match);
        $_data_replace = array();
        if(strpos($content, 'DATA@list:')!==false){
            $listData = self::listData($responses['reurl']);
        }
        foreach ((array)$data_match[1] as $_key => $_name) {
            $_nameKeys = explode('.', $_name);
            if(strpos($_name, 'list:')!==false){
                $_name    = str_replace('list:','',$_name);
                $_content = $listData[$_name];
            }else{
                $_content  = $responses[$_nameKeys[0]];
            }
            if(count($_nameKeys)>1){
                foreach ((array)$_nameKeys as $kk => $nk) {
                    $kk && $_content = $_content[$nk];
                }
            }
            $_data_replace[$_key]=$_content;
        }
        if($_data_replace){
            if(count($data_match[0])>1||!is_array($_data_replace[0])){
                $content = str_replace($data_match[0], $_data_replace, $content);
            }else{
                $content = $_data_replace[0];
            }
        }
        unset($data_match,$_data_replace,$_content);
        return $content;
    }
    public static function domAttr($DOM,$selectors,$fun='text'){
        $selectors = str_replace('DOM::','',$selectors);
        list($selector,$attr) = explode("@", $selectors);

        if($attr){
            if($attr=='text'){
                return trim($DOM[$selector]->text());
            }
            return $DOM[$selector]->attr($attr);
        }else{
            return $DOM[$selector]->$fun();
        }
    }
    public static function listData($url,$data=null){
        if(spider::$callback['tools:listData']===false){
            return false;
        }
        $url = self::URN($url);
        if (spider::$callback['tools:listData'] && is_callable(spider::$callback['tools:listData'])) {
            return call_user_func_array(spider::$callback['tools:listData'],array($url,$data));
        }
        if($data===null){
            $json = iDB::value("SELECT `data` FROM `#iCMS@__spider_url_data` where `url`='$url'");
            return json_decode($json,true);
        }else{
            iDB::insert("spider_url_data",array(
                'url'  =>$url,
                'data' =>addslashes(json_encode($data))
                ),true
            );
        }
    }
    public static function listItem($data,$rule,$baseUrl=null){
        $responses = array();

        if(strpos($rule['list_url_rule'], '<%url%>')!==false){
            $responses = $data;
        }elseif($rule['mode']=="3"){
            $list_url_rule = explode("\n", $rule['list_url_rule']);
            foreach ($list_url_rule as $key => $value) {
                $key_rule = trim($value);
                if(empty($key_rule)){
                    continue;
                }
                $rkey = $key_rule;
                $dkey = $key_rule;
                if(strpos($key_rule, '@@')!==false){
                    list($rkey,$dkey) = explode("@@", $key_rule);
                }
                $data[$dkey] && $responses[$rkey] = $data[$dkey];
            }
        }elseif($rule['mode']=="2"){
        // }else if(is_object($data)){
            $DOM = phpQuery::pq($data);

            $dom_key_map = array('title','url');
            $list_url_rule = explode("\n", $rule['list_url_rule']);
            empty($list_url_rule) && $list_url_rule = $dom_key_map;
            foreach ($list_url_rule as $key => $value) {
                $dom_rule = trim($value);
                if(empty($dom_rule)){
                    continue;
                }
                //pic@@DOM::img@src
                $content  = '';
                $dom_key  = '';
                if(strpos($dom_rule, '@@')!==false){
                    list($dom_key,$dom_rule) = explode("@@", $dom_rule);
                }
                if(strpos($dom_rule, 'DOM::')!==false){
                    $content = self::domAttr($DOM,$dom_rule);
                }else{
                    if($dom_rule=='url'||$dom_rule=='href'){
                        $dom_key  = 'url';
                        $dom_rule = 'href';
                    }
                    if($dom_rule=='title'||$dom_rule=='text'){
                        $dom_key  = 'title';
                        $dom_rule = 'text';
                    }
                    if($dom_rule=='@title'){
                        $dom_key  = 'title';
                        $dom_rule = 'title';
                    }
                    if($dom_rule=='text'){
                        $content = $DOM->text();
                    }else{
                        $content = $DOM->attr($dom_rule);
                    }
                }
                empty($dom_key) && $dom_key  = $dom_key_map[$key];
                $responses[$dom_key] = str_replace('&nbsp;','',trim($content));
            }
            unset($DOM);
        }
        $title = trim($responses['title']);
        $url   = trim($responses['url']);
        $url   = str_replace('<%url%>',$url, htmlspecialchars_decode($rule['list_url']));

        preg_match_all('#<%(\w{3,20})%>#is',$url,$f_match);
        foreach ((array)$f_match[1] as $_key => $_name) {
            $url = str_replace($f_match[0][$_key],trim($responses[$_name]),$url);
        }

        if(strpos($url, 'AUTO::')!==false && $baseUrl){
            $url = str_replace('AUTO::','',$url);
            $url = self::url_complement($baseUrl,$url);
        }

        iFS::checkHttp($url) OR $url = self::url_complement($baseUrl,$url);

        if($rule['list_url_clean']){
            $url = self::dataClean($rule['list_url_clean'],$url);
            if($url===null){
                return array();
            }
        }
        $title = preg_replace('/<[\/\!]*?[^<>]*?>/is', '', $title);

        $responses['title'] = $title;
        $responses['url'] = $url;

        return $responses;
    }

    public static function pregTag($rule) {
        $rule = trim($rule);
        if(empty($rule)){
            return false;
        }
        $rule = str_replace("%>", "%>\n", $rule);
        preg_match_all("/<%(.+)%>/i", $rule, $matches);
        $pregArray = array_unique($matches[0]);
        $pregflip = array_flip($pregArray);
        foreach ((array)$pregflip AS $kpreg => $vkey) {
            $pregA[$vkey] = "@@@@iCMS_PREG_" . rand(1, 1000) . '_' . $vkey . '@@@@';
        }
        $rule = str_replace($pregArray, $pregA, $rule);
        $rule = preg_quote($rule, '~');
        $rule = str_replace($pregA, $pregArray, $rule);
        $rule = str_replace("%>\n", "%>", $rule);
        $rule = preg_replace('~<%(\w{3,20})%>~i', '(?<\\1>.*?)', $rule);
        $rule = str_replace(array('<%', '%>'), '', $rule);
        unset($pregArray,$pregflip,$matches);
        gc_collect_cycles();
        //var_dump(htmlspecialchars($rule));
        return $rule;
    }
    public static function dataClean($rules, $content) {
        iPHP::vendor('phpQuery');
        $ruleArray = explode("\n", $rules);
        $NEED = $NOT = array();
        foreach ($ruleArray AS $key => $rule) {
            $rule = trim($rule);
            $rule = str_replace('<BR>', "\n", $rule);
            $rule = str_replace('<n>', "\n", $rule);
            if(strpos($rule, 'BEFOR::')!==false){
              $befor = str_replace('BEFOR::','', $rule);
              $content = $befor.$content;
            }else if(strpos($rule, 'AFTER::')!==false){
              $after = str_replace('AFTER::','', $rule);
              $content = $content.$after;
            }else if(strpos($rule, 'IF::')!==false){
              list($expr,$tf) = explode('?', $rule);
              $find = str_replace('IF::','', $expr);
              empty($tf) && $tf = '1:0';
              list($t,$f) = explode(':', $tf);
              $t = str_replace('<%SELF%>',$content, $t);
              $content = strpos($content, $find)===false?$f:$t;
            }else if(strpos($rule, 'CUT::')!==false){
              $len = str_replace('CUT::','', $rule);
              $content = csubstr($content,$len);
            }else if(strpos($rule, 'SPLIT::')!==false){
              $delimiter = str_replace('SPLIT::','', $rule);
              $content = explode($delimiter, $content);
            }else if(strpos($rule, '<%SELF%>')!==false){
              $content = str_replace('<%SELF%>',$content, $rule);
            }else if(strpos($rule, '<%nbsp%>')!==false){
                $content  = str_replace(array('&nbsp;','&#12288;'),'', $content);
                $_content = htmlentities($content);
                $content  = str_replace(array('&nbsp;','&#12288;','&amp;nbsp;','&amp;#12288;'),'', $_content);
                $content  = html_entity_decode($content);
                unset($_content);
            }else if(strpos($rule, 'HTML::')!==false){
                $tag = str_replace('HTML::','', $rule);
                if($tag=='ALL'){
                    $content = preg_replace('/<[\/\!]*?[^<>]*?>/is','',$content);
                }else {
                    $rep ="\\1";
                    if(strpos($tag, '*')!==false){
                        $rep ='';
                        $tag =str_replace('*', '', $tag);
                    }
                    $content = preg_replace("/<{$tag}[^>].*?>(.*?)<\/{$tag}>/si", $rep,$content);
                    $content = preg_replace("@<{$tag}[^>]*>@is", "",$content);
                }
            }else if(strpos($rule, 'LEN::')!==false){
                $len        = str_replace('LEN::','', $rule);
                $len_content = preg_replace(array('/<[\/\!]*?[^<>]*?>/is','/\s*/is'),'',$content);
                if(cstrlen($len_content)<$len){
                    return null;
                }
            }else if(strpos($rule, 'IMG::')!==false){
                $img_count = str_replace('IMG::','', $rule);
                preg_match_all("/<img.*?src\s*=[\"|'](.*?)[\"|']/is", $content, $match);
                $img_array  = array_unique($match[1]);
                if(count($img_array)<$img_count){
                    return null;
                }
            }else if(strpos($rule, 'DOM::')!==false){
                $rule = str_replace('DOM::','', $rule);
                $dflag = false;
                if($rule[0]==':'){
                    $dflag = true;
                    $rule = substr($rule, 1);
                }
                $doc = phpQuery::newDocumentHTML($content,'UTF-8');
                //DOM::div.class::attr::ooxx
                //DOM::div.class[fun][attr]
                //DOM::div.title[attr][data-title]
                list($pq_dom, $pq_fun,$pq_attr) = explode("::", $rule);
                if(strpos($rule, '][')!==false){
                    list($pq_dom, $pq_fun,$pq_attr) = explode("[", $rule);
                    $pq_fun  = rtrim($pq_fun,']');
                    $pq_attr = rtrim($pq_attr,']');
                }
                $pq_array = phpQuery::pq($pq_dom);
                foreach ($pq_array as $pq_key => $pq_val) {
                    if($pq_fun){
                        if($pq_attr){
                            $pq_content = phpQuery::pq($pq_val)->$pq_fun($pq_attr);
                        }else{
                            $pq_content = phpQuery::pq($pq_val)->$pq_fun();
                        }
                    }else{
                        $pq_content = (string)phpQuery::pq($pq_val);
                    }
                    $pq_pattern[$pq_key] = $pq_content;
                }
                phpQuery::unloadDocuments($doc->getDocumentID());
                if($dflag){
                    $_content[$key] = implode('', (array)$pq_pattern);
                }else{
                    $content = str_replace($pq_pattern,'', $content);
                }
                unset($doc,$pq_array);
            }else if(strpos($rule, '==')!==false){
                list($_pattern, $_replacement) = explode("==", $rule);
                $_pattern     = trim($_pattern);
                $_replacement = trim($_replacement);
                $_replacement = str_replace('\n', "\n", $_replacement);
                if(strpos($_pattern, '~SELF~')!==false){
                    $_pattern = str_replace('~SELF~',$content, $_pattern);
                }
                if(strpos($_replacement, '~SELF~')!==false){
                    $_replacement = str_replace('~SELF~',$content, $_replacement);
                }
                if(strpos($_replacement, '~S~')!==false){
                    $_replacement = str_replace('~S~',' ', $_replacement);
                }
                if(strpos($_replacement, '~N~')!==false){
                    $_replacement = str_replace('~N~',"\n", $_replacement);
                }
                $replacement[$key] = $_replacement;
                $pattern[$key] = '|' . self::pregTag($_pattern) . '|is';
                $content = preg_replace($pattern, $replacement, $content);
            }else if(strpos($rule, 'KEY::')!==false){
                $rule = str_replace('KEY::','', $rule);
                $content = $content[$rule];
            }else if(strpos($rule, 'FUNC::')!==false){
              preg_match('/FUNC::(\w+)\(/is', $rule,$func_match);
              $func = $func_match[1];
              preg_match_all('/[\"|\'](.*?)[\"|\']/is',$rule,$param_match);
              $param = $param_match[1];
              foreach ($param as $key => $value) {
                  if($value=='@me'){
                    $param[$key] = $content;
                  }
              }
              $content = call_user_func_array($func, $param);
            }else if(strpos($rule, 'NEED::')!==false){
                $NEED[$key]= self::data_check('NEED::',$rule,$content);
            }else if(strpos($rule, 'NOT::')!==false){
                $NOT[$key]= self::data_check('NOT::',$rule,$content);
            }else{
                $content = preg_replace('|' . self::pregTag($rule) . '|is','', $content);
            }
        }
        if(is_array($_content)){
            $content = implode('', $_content);
        }
        if($NOT){
            $content = self::data_check_result($NOT,'NOT::');
            if($content === null){
                return null;
            }
        }
        if($NEED){
            $content = self::data_check_result($NEED,'NEED::');
            if($content === null){
                return null;
            }
        }
        unset($NOT,$NEED);
        return $content;
    }
    public static function data_check_result($variable,$prefix){
        foreach ((array)$variable as $key => $value) {
            if($value!=$prefix){
                return $value;
            }
        }
        return null;
    }
    public static function data_check($prefix,$rule,$content){
        $check = str_replace($prefix,'', $rule);
        $bool  = array(
            'NOT::'  => false,
            'NEED::' => true
        );
        if(strpos($check,',')===false){
            if(strpos($content,$check)===false){
                $checkflag = false;
            }else{
                $checkflag = true;
            }
        }else{
            $checkArray = explode(',', $check);
            foreach ($checkArray as $key => $value) {
                if(strpos($content,$value)===false){
                    $checkflag = false;
                }else{
                    $checkflag = true;
                }
                if($checkflag==$bool[$prefix]){
                    break;
                }
            }
        }
        return $checkflag===$bool[$prefix]?$content:$prefix;
    }
    public static function charsetTrans($html,$content_charset,$encode, $out = 'UTF-8') {
        if (spider::$dataTest || spider::$ruleTest) {
            echo '<b>规则设置编码:</b>'.$encode . '<br />';
        }

        $encode == 'auto' && $encode = null;
        /**
         * 检测http返回的编码
         */
        if($content_charset){
            $content_charset = rtrim($content_charset,';');
            if(empty($encode)||strtoupper($encode)!=strtoupper($content_charset)){
                $encode = $content_charset;
            }
            if (spider::$dataTest || spider::$ruleTest) {
                echo '<b>检测http编码:</b>'.$encode . '<br />';
            }
            if(strtoupper($encode)==$out){
                return $html;
            }
        }
        /**
         * 检测页面编码
         */
        preg_match('/<meta[^>]*?charset=(["\']?)([a-zA-z0-9\-\_]+)(\1)[^>]*?>/is', $html, $charset);
        $meta_encode = str_replace(array('"',"'"),'', trim($charset[2]));
        if(empty($encode)){
            $meta_encode && $encode = $meta_encode;
            if (spider::$dataTest || spider::$ruleTest) {
                echo '<b>检测页面编码:</b>'.$meta_encode . '<br />';
            }
        }
        preg_match('/<meta[^>]*?http-equiv=(["\']?)content-language(\1)[^>]*?content=(["\']?)([a-zA-z0-9\-\_]+)(\3)[^>]*?>/is', $html, $language);
        $lang_encode = str_replace(array('"',"'"),'', trim($language[4]));
        if(empty($encode)){
            $lang_encode && $encode = $lang_encode;
            if (spider::$dataTest || spider::$ruleTest) {
                echo '<b>检测页面meta编码声明:</b>'.$lang_encode . '<br />';
            }
        }
        if($content_charset && $meta_encode && strtoupper($meta_encode)!=strtoupper($content_charset)){
            $encode = $meta_encode;
            if (spider::$dataTest || spider::$ruleTest) {
                echo '<b>检测到http编码与页面编码不一致:</b>'.$content_charset.','.$meta_encode.'<br />';
            }
        }

        if($lang_encode && $meta_encode && strtoupper($meta_encode)!=strtoupper($lang_encode)){
            $encode = null;
            if (spider::$dataTest || spider::$ruleTest) {
                echo '<b>检测到页面存在两种不一样的编码声明:</b>'.$lang_encode.','.$meta_encode.'<br />';
            }
        }

        if(function_exists('mb_detect_encoding') && empty($encode)) {
            $detect_encode = mb_detect_encoding($html, array("ASCII","UTF-8","GB2312","GBK","BIG5"));
            $detect_encode && $encode = $detect_encode;
            if (spider::$dataTest || spider::$ruleTest) {
                echo '<b>程序自动识别页面编码:</b>'.$detect_encode . '<br />';
            }
        }

        if(strtoupper($encode)==$out){
            return $html;
        }
        if(strtoupper($encode)=='GB2312'){
            $encode = 'GBK';
        }
        if (spider::$dataTest || spider::$ruleTest) {
            echo '<b>页面编码不一致,进行转码['.$encode.'=>'.$out.']</b><br />';
        }
        $html = preg_replace('/(<meta[^>]*?charset=(["\']?))[a-zA-z0-9\-\_]*(\2[^>]*?>)/is', "\\1$out\\3", $html,1);

        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($html,$out,$encode);
        } elseif (function_exists('iconv')) {
            return iconv($encode,$out, $html);
        } else {
            iPHP::error_throw('charsetTrans failed, no function');
        }
    }
    public static function check_content($content,$code) {
        if(strpos($code, 'DOM::')!==false){
            iPHP::vendor('phpQuery');
            $doc     = phpQuery::newDocumentHTML($content,'UTF-8');
            $pq_dom  = str_replace('DOM::','', $code);
            $matches = (bool)(string)phpQuery::pq($pq_dom);
            phpQuery::unloadDocuments($doc->getDocumentID());
            unset($doc,$content);
        }else{
            $_code = self::pregTag($code);
            if (preg_match('/(<\w+>|\.\*|\.\+|\\\d|\\\w)/i', $code)) {
                preg_match('|' . $_code . '|is', $content, $_matches);
                $matches = $_matches['content'];
            }else{
                $matches = strpos($content, $code);
            }
            unset($content);
        }
        return $matches;
    }
    public static function check_content_code($content,$type=null) {
        if (spider::$content_right_code && $type=='right') {
            $right_code = self::check_content($content,spider::$content_right_code);
            if ($right_code===false) {
                return false;
            }
        }
        if (spider::$content_error_code && $type=='error') {
            $error_code = self::check_content($content,spider::$content_error_code);
            if ($error_code!==false) {
                return false;
            }
        }
        return true;
    }
    public static function mkurls($url,$format,$begin,$num,$step,$zeroize,$reverse) {
        $urls = array();
        $start = (int)$begin;
        if($format==0){
            $num = $num-1;
            if($num<0){
                $num = 1;
            }
            $end = $start+$num*$step;
        }else if($format==1){
            // $end = $start*pow($step,$num-1);
            $end = $start+$num*$step;
        }else if($format==2){
            $start = ord($begin);
            $end   = ord($num);
            $step  = 1;
        }
        $zeroize = ($zeroize=='true'?true:false);
        $reverse = ($reverse=='true'?true:false);
        //var_dump($url.','.$format.','.$begin.','.$num.','.$step,$zeroize,$reverse);
        if($reverse){
            for($i=$end;$i>=$start;){
                $id = $i;
                if($format==2){
                    $id = chr($i);
                }
                if($zeroize){
                    $len = strlen($end);
                    //$len==1 && $len=2;
                    $id  = sprintf("%0{$len}d", $i);
                }
                $urls[]=str_replace('<*>',$id,$url);
                // if($format==1){
                //   $i=$i/$step;
                // }else{
                // }
                  $i=$i-$step;
            }
        }else{
            for($i=$start;$i<=$end;){
                $id = $i;
                if($format==2){
                    $id = chr($i);
                }
                if($zeroize){
                    $len = strlen($end);
                    //$len==1 && $len=2;
                    $id  = sprintf("%0{$len}d", $i);
                }
                $urls[]=str_replace('<*>',$id,$url);
                // if($format==1){
                //   $i=$i*$step;
                // }else{
                // }
                  $i=$i+$step;
            }
        }
        return $urls;
    }
    public static function insert_urls($iid,$url,$source) {
        return iDB::insert("spider_url_list",array(
            'iid'    =>$iid,
            'url'    =>$url,
            'source' =>$source
        ),true);
    }
    public static function check_urls($content) {
        if(is_array($content)){
            $content = array_filter($content);
            $content = array_unique($content);
        }
        $sql = iSQL::in($content,'url',false,true);
        $sql && $all = iDB::all("SELECT `id`,`url` FROM `#iCMS@__spider_url_list` WHERE $sql ");
        if($all){
            $urls   = array_column($all, 'url','id');
            $content = array_diff($content, $urls);
            if(spider::$work=='shell'){
                print self::datetime()."\033[36mspider_tools::check_urls\033[0m => 已采[".count($urls)."]条,还剩[".count($content)."]条".PHP_EOL;
            }
        }
        return $content;
    }
    public static function collect_urls($content) {
        if(is_array($content)){
            $content = array_filter($content);
            $content = array_unique($content);
        }

        if(spider::$dataTest){

        }

        $pid   = spider::$pid;
        $rid   = spider::$rid;
        $table = 'spider_collect_urls_r'.$rid;
        $path  = iPHP_APP_CACHE.'/spider/'.$table.'.txt';
        if($rid){
            if(!file_exists($path)){
                if(!iDB::check_table($table)){
                    $sql ='
    CREATE TABLE `#iCMS@__'.$table.'` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `url` varchar(255) NOT NULL DEFAULT \'\',
      `pid` int(10) unsigned NOT NULL DEFAULT \'0\',
      `iid` int(10) unsigned NOT NULL DEFAULT \'0\',
      PRIMARY KEY (`id`),
      UNIQUE KEY `url` (`url`),
      KEY `pid` (`pid`),
      KEY `iid` (`iid`)
    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET='.iPHP_DB_CHARSET;
                    iDB::query($sql);
                }
                iFS::mkdir(dirname($path));
                file_put_contents($path, time());
            }

            if($pid){
                $data = array();
                if(is_array($content)){
                    foreach ($content as $url) {
                        $data[]= array('url'=>$url,'pid'=>$pid);
                    }
                    $data && iDB::insert_multi($table,$data,true);
                }else{
                    iDB::insert($table,array('url'=>$content,'pid'=>$pid),true);
                }
            }
        }
        return $content;
    }
    public static function url_complement($baseUrl,$href){
        $href = trim($href);
        if (iFS::checkHttp($href)){
            return $href;
        }else{
            if (substr($href, 0,1)=='/'){
                $base_uri  = parse_url($baseUrl);
                if($href[1]=='/'){
                    $base_host = $base_uri['scheme'].':/';
                }else{
                    $base_host = $base_uri['scheme'].'://'.$base_uri['host'];
                }
                return $base_host.'/'.ltrim($href,'/');
            }else{

                if(substr($baseUrl, -1)!='/'){
                    $info = pathinfo($baseUrl);
                    $info['extension'] && $baseUrl = $info['dirname'];
                }
                $baseUrl = rtrim($baseUrl,'/');
                return iFS::path($baseUrl.'/'.ltrim($href,'/'));
            }
        }
    }
    public static function img_url_complement($content,$baseurl){
        preg_match_all("/<img.*?src\s*=[\"|'](.*?)[\"|']/is", $content, $img_match);
        if($img_match[1]){
            $_img_array = array_unique($img_match[1]);
            $_img_urls  = array();
            foreach ((array)$_img_array as $_img_key => $_img_src) {
                $_img_urls[$_img_key] = spider_tools::url_complement($baseurl,$_img_src);
            }
           $content = str_replace($_img_array, $_img_urls, $content);
        }
        unset($img_match,$_img_array,$_img_urls,$_img_src);
        return $content;
    }
    public static function checkpage(&$newbody, $bodyA, $_count = 1, $nbody = "", $i = 0, $k = 0) {
        $ac = count($bodyA);
        $nbody.= $bodyA[$i];
        $pics    = filesApp::get_content_pics($nbody);
        $_pcount = count($pics);
        //  print_r($_pcount);
        //  echo "\n";
        //  print_r('_count:'.$_count);
        //  echo "\n";
        //  var_dump($_pcount>$_count);
        if ($_pcount >= $_count) {
            $newbody[$k] = $nbody;
            $k++;
            $nbody = "";
        }
        $ni = $i + 1;
        if ($ni <= $ac) {
            self::checkpage($newbody, $bodyA, $_count, $nbody, $ni, $k);
        } else {
            $newbody[$k] = $nbody;
        }
    }
    public static function mergePage($content){
        $_content = $content;
        $pics     = filesApp::get_content_pics($_content);
        $_pcount  = count($pics);
        if ($_pcount < 4) {
            $content = str_replace('#--iCMS.PageBreak--#', "", $content);
        } else {
            $contentA = explode("#--iCMS.PageBreak--#", $_content);
            $newcontent = array();
            self::checkpage($newcontent, $contentA, 4);
            if (is_array($newcontent)) {
                $content = array_filter($newcontent);
                $content = implode('#--iCMS.PageBreak--#', $content);
                //$content      = addslashes($content);
            } else {
                //$content      = addslashes($newcontent);
                $content = $newcontent;
            }
            unset($newcontent,$contentA);
        }
        unset($_content);
        return $content;
    }
    public static function textlen($string){
        return function_exists('mb_strlen')?mb_strlen($string, "UTF-8"):strlen($string);
    }
    public static function autoBreakPage($content,$pageBit = '15000',$pageBreak='#--iCMS.PageBreak--#'){
        $text      = str_replace('</p><p>', "</p>\n<p>", $content);
        $textArray = explode("\n", $text);
        $pageNum   = 0;
        $resource  = array();
        $textLen   = strlen($text);
        $resLen    = 0;
        foreach ($textArray as $key => $p) {
            $pageLen = strlen($resource[$pageNum]);
            $pLen   += strlen($p);
            $slen    = $pLen>0?$textLen-$pLen:0;
            // echo $key.' '.$pageLen.' '.$textLen.' '.$pLen.' '.$slen.PHP_EOL;
            if($pageLen>$pageBit && $slen>$pageBit){
                $pageNum++;
                $resource[$pageNum] = $p;
            }else{
                $resource[$pageNum].= $p;
            }
            unset($textArra[$key]);
        }
        unset($text,$textArray);
        if($pageBreak===false){
            return $resource;
        }
        return implode($pageBreak, (array)$resource);
    }
    public static function URN($url) {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $scheme && $url  = str_replace($scheme.'://', '', $url);
        return $url;
    }
    public static function safe_url($url) {
        $parsed = parse_url($url);
        $validate_ip = true;

        if($parsed['port'] && is_array(self::$safe_port) && !in_array($parsed['port'],self::$safe_port)){
            if (spider::$dataTest || spider::$ruleTest) {
                echo "<b>请求错误:非正常端口,因安全问题只允许抓取80,443端口的链接,如有特殊需求请自行修改程序</b>".PHP_EOL;
            }
            return false;
        }else{
            preg_match('/^\d+$/', $parsed['host']) && $parsed['host'] = long2ip($parsed['host']);
            $long = ip2long($parsed['host']);
            if($long===false){
                $ip = null;
                if(self::$safe_url){
                    @putenv('RES_OPTIONS=retrans:1 retry:1 timeout:1 attempts:1');
                    $ip   = gethostbyname($parsed['host']);
                    $long = ip2long($ip);
                    $long===false && $ip = null;
                    @putenv('RES_OPTIONS');
                }
            }else{
                $ip = $parsed['host'];
            }
            $ip && $validate_ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        }

        if(!in_array($parsed['scheme'],array('http','https')) || !$validate_ip){
            if (spider::$dataTest || spider::$ruleTest) {
                echo "<b>{$url} 请求错误:非正常URL格式,因安全问题只允许抓取 http:// 或 https:// 开头的链接或公有IP地址</b>".PHP_EOL;
            }
            return false;
        }else{
            return $url;
        }
    }
    public static function datetime() {
        $mtimestamp   = sprintf("%.3f", microtime(true)); // 带毫秒的时间戳
        $timestamp    = floor($mtimestamp); // 时间戳
        $milliseconds = round(($mtimestamp - $timestamp) * 1000); // 毫秒
        $milliseconds = sprintf ("%-'03s", $milliseconds);
        return date("Y-m-d H:i:s", $timestamp) . '.' . $milliseconds.' ';
    }
    public static function remote($url,$ref=null,$_count = 0) {
        if(self::safe_url($url)===false) return false;

        (iPHP_SHELL && self::$debug) && print self::datetime()."\033[36mspider_tools::remote\033[0m [".($_count+1)."] => ".$url.PHP_EOL;

        $parsed = parse_url($url);
        $url = str_replace('&amp;', '&', $url);
        if(empty(spider::$referer)){
            spider::$referer = $parsed['scheme'] . '://' . $parsed['host'];
        }

        $options = array(
            CURLOPT_URL                  => $url,
            CURLOPT_REFERER              => self::$CURLOPT_REFERER?self::$CURLOPT_REFERER:spider::$referer,
            CURLOPT_USERAGENT            => self::$CURLOPT_USERAGENT?self::$CURLOPT_USERAGENT:spider::$useragent,
            CURLOPT_ENCODING             => self::$CURLOPT_ENCODING?self::$CURLOPT_ENCODING:spider::$encoding,
            CURLOPT_TIMEOUT              => self::$CURLOPT_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT       => self::$CURLOPT_CONNECTTIMEOUT,
            CURLOPT_RETURNTRANSFER       => 1,
            CURLOPT_FAILONERROR          => 0,
            CURLOPT_HEADER               => 0,
            CURLOPT_NOSIGNAL             => true,
            // CURLOPT_DNS_USE_GLOBAL_CACHE => true,
            // CURLOPT_DNS_CACHE_TIMEOUT    => 86400,
            CURLOPT_SSL_VERIFYPEER       => false,
            CURLOPT_SSL_VERIFYHOST       => false
            // CURLOPT_FOLLOWLOCATION => 1,// 使用自动跳转
            // CURLOPT_MAXREDIRS => 7,//查找次数，防止查找太深
        );
        spider::$cookie && $options[CURLOPT_COOKIE] = spider::$cookie;

        if (self::$CURLOPT_COOKIE) {
            $options[CURLOPT_COOKIE] = self::$CURLOPT_COOKIE;
        }
        if (self::$CURLOPT_COOKIEFILE) {
            $options[CURLOPT_COOKIEFILE] = self::$CURLOPT_COOKIEFILE;
        }
        if (self::$CURLOPT_COOKIEJAR) {
            $options[CURLOPT_COOKIEJAR] = self::$CURLOPT_COOKIEJAR;
        }
        if (self::$CURLOPT_HTTPHEADER) {
            $options[CURLOPT_HTTPHEADER] = self::$CURLOPT_HTTPHEADER;
        }
        if(spider::$curl_proxy||spider_tools::$curl_proxy){
            $proxy = self::proxy_test($options);
            if (spider::$dataTest || spider::$ruleTest) {
                echo "<b>使用代理:</b>";
                echo $proxy;
                echo '<hr />';
            }
            $proxy && iHttp::proxy_set($options,$proxy);
        }

        spider::$PROXY_URL && $options[CURLOPT_URL] = spider::$PROXY_URL.urlencode($url);

        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
            $options[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
        }
        if (self::$callback['progress'] && is_callable(self::$callback['progress'])) {
            $options[CURLOPT_NOPROGRESS] = FALSE;
            $options[CURLOPT_PROGRESSFUNCTION] = array(__CLASS__,'curl_progressfunction');
        }
        if (self::$callback['header'] && is_callable(self::$callback['header'])) {
            $options[CURLOPT_HEADERFUNCTION] = self::$callback['header'];
        }
        if (self::$callback['options'] && is_callable(self::$callback['options'])) {
            call_user_func_array(self::$callback['options'],array(&$options,$ref,&$_count));
        }

        self::$handle = curl_init();
        curl_setopt_array(self::$handle,$options);
        $responses = curl_exec(self::$handle);
        $info  = self::$CURL_INFO   = curl_getinfo(self::$handle);
        self::$CURL_ERRNO  = curl_errno(self::$handle);
        self::$CURL_ERRNO && self::$CURL_ERROR = curl_error(self::$handle);

        if (spider::$dataTest || spider::$ruleTest) {
            echo "<b>{$url} 请求信息:</b>";
            echo "<pre style='max-height:90px;overflow-y: scroll;'>";
            print_r($info);
            echo '</pre><hr />';
            if($_GET['breakinfo']){
            	exit();
            }
        }
        if (in_array($info['http_code'],array(301,302)) && $_count < 5) {
            $_count++;
            $newurl = $info['redirect_url'];
	        if(empty($newurl)){
		    	curl_setopt(self::$handle, CURLOPT_HEADER, 1);
		    	$header		= curl_exec(self::$handle);
		    	preg_match ('|Location: (.*)|i',$header,$matches);
		    	$newurl 	= ltrim($matches[1],'/');
			    if(empty($newurl)) return false;

		    	if(!strstr($newurl,'http://')){
			    	$host	= $parsed['scheme'].'://'.$parsed['host'];
		    		$newurl = $host.'/'.$newurl;
		    	}
	        }
	        $newurl	= trim($newurl);
			curl_close(self::$handle);
			unset($responses,$info);
            return self::remote($newurl,$ref,$_count);
        }
        if (in_array($info['http_code'],array(404,500))) {
			curl_close(self::$handle);
			unset($responses,$info);
            return false;
        }

        if ((empty($responses)||$info['http_code']!=200) && $_count < 5) {
            $_count++;
            if (spider::$dataTest || spider::$ruleTest) {
                echo $url . '<br />';
                echo "获取内容失败,重试第{$_count}次...<br />";
            }
			curl_close(self::$handle);
			unset($responses,$info);
            return self::remote($url,$ref,$_count);
        }
        $pos = stripos($info['content_type'], 'charset=');
        $pos!==false && $content_charset = trim(substr($info['content_type'], $pos+8));
        $responses = self::charsetTrans($responses,$content_charset,spider::$charset);
		curl_close(self::$handle);
		unset($info);
        if (spider::$dataTest || spider::$ruleTest) {
            echo '<pre>';
            print_r(htmlspecialchars(substr($responses,0,800)));
            echo '</pre><hr />';
        }
        spider::$url = $url;

        // (iPHP_SHELL && self::$debug) && print self::datetime()."\033[36mspider_tools::remote\033[0m OK ".PHP_EOL;

        return $responses;
    }
    public static function get_cookie($url,$data=null,$flag=false){
        iHttp::$CURLOPT_TIMEOUT        = 60; //数据传输的最大允许时间
        iHttp::$CURLOPT_CONNECTTIMEOUT = 10;  //连接超时时间
        $host = parse_url($url, PHP_URL_HOST);
        $path = iPHP_APP_CACHE.'/spider/cookie.'.$host.'.txt';
        iFS::mkdir(dirname($path));
        iHttp::$CURLOPT_COOKIEJAR = $path;
        if($data && is_string($data)){
            $data = parse_url_qs($data);
        }
        $ret = iHttp::post($url,$data);
        $flag===true && self::$CURLOPT_COOKIEFILE = $path;
        is_callable(self::$callback['get_cookie']) && call_user_func_array(self::$callback['get_cookie'],array($ret,$path));
        return array($ret,$path);
    }
    public static function proxy_test($options=null){
        iHttp::$CURL_PROXY = self::$curl_proxy?:spider::$curl_proxy;
        // iHttp::$CURL_PROXY_ARRAY = self::$proxy_array?:spider::$proxy_array;
        return iHttp::proxy_test($options);
    }
    public static function curl_progressCallback($a){
        static $previousProgress = 0;
        $download_size = $a[1];
        $downloaded_size = $a[2];
        if ($download_size == 0 ){
            $progress = 0;
        }else{
            $progress = round($downloaded_size/$download_size,2)*100;
        }
        if ( $progress > $previousProgress){
            $previousProgress = $progress;
            if($progress%2){
                echo '.';
            }
        }
    }
    public static function curl_progressfunction($resource, $download_total = 0, $downloaded_size = 0, $upload_total = 0, $uploaded_size = 0){
        if (version_compare(PHP_VERSION, '5.5.0') < 0) {
            $download_total  = $resource;
            $downloaded_size = $download_total;
            $upload_total    = $downloaded_size;
            $uploaded_size   = $upload_total;
            $resource        = null;
        }
        $args = array($resource, $download_total, $downloaded_size, $upload_total, $uploaded_size);
        call_user_func_array(self::$callback['progress'],array($args));
    }

	public static function str_cut($str, $start, $end) {
	    $content = strstr($str, $start);
	    $content = substr($content, strlen($start), strpos($content, $end) - strlen($start));
	    return $content;
	}

	public static function utf8_num_decode($entity) {
	    $convmap = array(0x0, 0x10000, 0, 0xfffff);
	    return mb_decode_numericentity($entity, $convmap, 'UTF-8');
	}
	public static function utf8_entity_decode($entity) {
	    $entity  = '&#'.hexdec($entity).';';
	    $convmap = array(0x0, 0x10000, 0, 0xfffff);
	    return mb_decode_numericentity($entity, $convmap, 'UTF-8');
	}
    public static function array_filter_key($array,$filter,$level){
        $_filter = $filter[$level];unset($filter[$level]);
        foreach ((array)$array as $key => $value) {
            if($key==$_filter){
                if(empty($filter)){
                    return $value;
                }else{
                    ++$level;
                    return self::array_filter_key($value,$filter,$level);
                }
            }else{

            }
        }

    }
    public static function real_empty($text){
        is_array($text) && $text = implode('', $text);

        $text = strip_tags($text, '<img>');
        $text = preg_replace(array('/\s*/','/\r*/','/\n*/'), '', $text);
        $text = str_replace(array('&nbsp;','&#12288;'),'', $text);
        $text = htmlentities($text);
        $text = str_replace(array('&nbsp;','&#12288;','&amp;nbsp;','&amp;#12288;'),'', $text);
        $text = trim($text);
        return $text;
    }
}
