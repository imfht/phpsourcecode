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

class spider_process {
    public static $groupMaps = array(
        '0x01' =>'常用处理',
        '0x02' =>'转义',
        '0x03' =>'分页',
        '0x04' =>'解析/解码',
        '0x05' =>'生成/编码',
        '0x06' =>'字符串',
        '0x07' =>'特殊处理'
    );
    public static $helperMaps = array(
        'dataclean'               => array(null,'数据整理','规则采集后数据整理'),
        'trim'                    => array('0x01','去首尾空白','去首尾空白'),
        'format'                  => array('0x01','HTML格式化','HTML格式化'),
        'cleanhtml'               => array('0x01','移除HTML标识','移除HTML标识'),
        'img_absolute'            => array('0x01','图片地址补全','图片地址补全'),
        'array'                   => array('0x01','返回数组','返回数组'),
        'url_absolute'            => array('0x01','URL补全','URL补全'),
        'capture'                 => array('0x01','抓取结果','抓取并直接返回结果'),
        'download'                => array('0x01','下载','下载并保存成文件'),
        'filter'                  => array('0x01','屏蔽词过滤','启用屏蔽词过滤'),
        'array_filter_empty'      => array('0x01','清除空值数组','清除空值数组'),
        'clean_cn_blank'          => array('0x01','清除中文空格','清除中文空格'),
        'array_reverse'           => array('0x01','相反数组','相反数组'),

        'stripslashes'            => array('0x02','去除反斜线','返回去除转义反斜线后的字符串'),
        'addslashes'              => array('0x02','加上反斜线','返回加上了反斜线的字符串'),
        'htmlspecialchars_decode' => array('0x02','反转义HTML字符','将特殊的 HTML 实体转换回普通字符'),
        'htmlspecialchars'        => array('0x02','转义HTML字符','将特殊字符转换为 HTML 实体'),
        'xml2array'               => array('0x02','xml转Array','xml转Array'),

        'mergepage'               => array('0x03','合并分页','合并分页'),
        'autobreakpage'           => array('0x03','自动分页','自动分页'),

        'urldecode'               => array('0x04','解码 URL 字符串(urldecode)','解码 URL 字符串(urldecode)'),
        'rawurldecode'            => array('0x04','解码 URL 字符串(rawurldecode)','解码 URL 字符串(rawurldecode)'),
        'parse_str'               => array('0x04','URL字符串解析(parse_str)','URL字符串解析(parse_str)'),
        'json_decode'             => array('0x04','JSON解码(json_decode) ','JSON解码(json_decode) '),
        'base64_decode'           => array('0x04','base64 解码(base64_decode) ','base64 解码(base64_decode) '),
        'auth_decode'             => array('0x04','解密(auth_decode) ','解密(auth_decode) '),

        'urlencode'               => array('0x05','编码 URL 字符串(urlencode)','编码 URL 字符串(urlencode)'),
        'rawurlencode'            => array('0x05','编码 URL 字符串(rawurlencode)','编码 URL 字符串(rawurlencode)'),
        'http_build_query'        => array('0x05','Array转URL字符串(http_build_query)','Array转URL字符串(http_build_query)'),
        'json_encode'             => array('0x05','JSON编码(json_encode) ','JSON编码(json_encode) '),
        'auth_encode'             => array('0x05','加密(auth_encode) ','加密(auth_encode) '),

        'explode'                 => array('0x06','字符串=>数组','字符串=>数组'),
        'array_implode'           => array('0x06','数组=>字符串','数组=>字符串','input'),

        '@check_urls'             => array('0x07','链接检查','独立检查,链接保存在新表'),
        '@collect_urls'           => array('0x07','收集链接','收集其它链接'),
    );
    public static function getArray(){
        $array = array();
        foreach (self::$helperMaps as $key => $value) {
            $gn = self::$groupMaps[$value[0]];
            $gn && $array[$gn][$key] = $value;
        }
        return $array;
    }
    public static function run($content,$data,$rule,$responses){
        if($data['process']){
            spider::$dataTest && print "<b>数据处理:</b><br />";
            foreach ($data['process'] as $key => $value) {
                if(!is_array($value)){
                    continue;
                }
                //特殊处理方法
                //@方法
                //@check_urls
                if(substr($value['helper'], 0,1)=='@'){
                    $sk = substr($value['helper'],1);
                    $value[$sk]   = true;
                    $sfuncArray[] = $value;
                    continue;
                }
                if(spider::$dataTest){
                    $hNo = $key+1;
                    echo $hNo.'# '.$value['helper'];
                    if($value['helper']=='dataclean'){
                        echo '('.htmlspecialchars($value['rule']).')';
                    }
                    echo '<br />';
                }
                $value[$value['helper']] = true;
                if(is_array($content) && substr($value['helper'], 0,6)!=='array_'){
                    foreach ($content as $idx => $con) {
                        $content[$idx] = self::helper($con,$value,$rule,$responses);
                    }
                }else{
                    $content = self::helper($content,$value,$rule,$responses);
                }
                if($content===null){
                    return null;
                }
            }
        }
        is_array($content) && $content = array_filter($content);

        if($sfuncArray)foreach ($sfuncArray as $key => $value) {
            spider::$dataTest && print ($hNo+1).'# '.$value['helper'].'<br />';
            $content = self::helper_func($content,$value,$rule);
        }

        return $content;
    }
    public static function helper($content,$process,$rule,$responses){
        if ($process['dataclean']) {
            $content = spider_tools::dataClean($process['rule'], $content);
            /**
             * 在数据项里调用之前采集的数据[DATA@name][DATA@name.key]
             */
            if(strpos($content, '[DATA@')!==false){
                $content = spider_tools::getDATA($responses,$content);
            }
        }
        if ($process['stripslashes']) {
            $content = stripslashes($content);
        }
        if ($process['addslashes']) {
            $content = addslashes($content);
        }
        if ($process['base64_encode']) {
            $content = base64_encode($content);
        }
        if ($process['base64_decode']) {
            $content = base64_decode($content);
        }
        if ($process['urlencode']) {
            $content = urlencode($content);
        }
        if ($process['urldecode']) {
            $content = urldecode($content);
        }
        if ($process['rawurlencode']) {
            $content = rawurlencode($content);
        }
        if ($process['rawurldecode']) {
            $content = rawurldecode($content);
        }
        if ($process['parse_str']) {
            $content = parse_url_qs($content);
        }
        if ($process['http_build_query'] && is_array($content)) {
            $content = http_build_query($content);
        }
        if ($process['trim']) {
            if(is_array($content)){
                $content = array_map('trim', $content);
            }else{
                $content = str_replace('&nbsp;','',trim($content));
            }
        }
        if($process['json_encode'] && is_array($content)){
            $content = json_encode($content);
        }
        if ($process['json_decode']) {
            $content = json_decode($content,true);
            if(is_null($content)){
                return spider_error::msg(
                    'JSON ERROR:'.json_last_error_msg(),
                    'content.json_decode.error',
                    $name,$rule['__url__']
                );
            }
        }
        if ($process['htmlspecialchars_decode']) {
            $content = htmlspecialchars_decode($content);
        }
        if ($process['htmlspecialchars']) {
            $content = htmlspecialchars($content);
        }
        if ($process['cleanhtml']) {
            $content = preg_replace('/<[\/\!]*?[^<>]*?>/is', '', $content);
        }
        if ($process['format'] && $content) {
            $content = autoformat($content);
        }
        if ($process['nl2br'] && $content) {
            $content = nl2br($content);
        }
        if ($process['url_absolute'] && $content) {
            $content = spider_tools::url_complement($rule['__url__'],$content);
        }
        if ($process['img_absolute'] && $content) {
            $content = spider_tools::img_url_complement($content,$rule['__url__']);
        }
        if ($process['capture']) {
            $content && $content = spider_tools::remote($content);
        }
        if ($process['download']) {
            $content && $content = iFS::http($content);
        }

        if ($process['autobreakpage'] && $content) {
            $content = spider_tools::autoBreakPage($content);
        }
        if ($process['mergepage'] && $content) {
            $content = spider_tools::mergePage($content);
        }
        if ($process['filter']) {
            $fwd = iPHP::callback(array("filterApp","run"),array(&$content),false);
            if($fwd){
                return spider_error::msg(
                    '中包含【'.$fwd.'】被系统屏蔽的字符!',
                    'content.filter',
                    $name,$rule['__url__']
                );
            }
        }
        if ($process['empty']) {
            $empty = spider_tools::real_empty($content);
            if(empty($empty)){
                return spider_error::msg(
                    '规则设置了不允许为空.当前抓取结果为空!请检查,规则是否正确!',
                    'content.empty',
                    $name,$rule['__url__']
                );
            }
            unset($empty);
        }
        if ($process['xml2array']) {
            $content = iUtils::xmlToArray($content);
        }
        if($process['array']){
            if(strpos($content, '#--iCMS.PageBreak--#')!==false){
                $content = explode('#--iCMS.PageBreak--#', $content);
            }
            if(empty($content)){
                return null;
            }
            return (array)$content;
        }
        if($process['clean_cn_blank']){
            $_content = htmlentities($content);
            $content  = str_replace(array('&#12288;','&amp;#12288;'),'', $_content);
            unset($_content);
        }
        if($process['array_filter_empty']){
            if(is_array($content)){
                $content = array_filter($content);
            }else{
                if(strpos($content, '#--iCMS.PageBreak--#')!==false){
                    $content = explode('#--iCMS.PageBreak--#', $content);
                    $content = array_filter($content);
                }
            }
        }
        if($process['array_reverse']){
            if(is_array($content)){
                $content = array_reverse($content);
            }else{
                if(strpos($content, '#--iCMS.PageBreak--#')!==false){
                    $content = explode('#--iCMS.PageBreak--#', $content);
                    $content = array_reverse($content);
                }
            }
        }
        if(($process['implode']||$process['array_implode']) && is_array($content)){
            $content = implode(',', $content);
        }
        if($process['explode'] && is_string($content)){
            $content = explode(',', $content);
        }
        return $content;
    }
    public static function helper_func($content,$process,$rule){
        //@check_urls
        if ($process['check_urls']) {
            $content && $content = spider_tools::check_urls($content);
        }
        //@collect_urls
        if ($process['collect_urls']) {
            $content && $content = spider_tools::collect_urls($content);

        }
        return $content;
    }
}
