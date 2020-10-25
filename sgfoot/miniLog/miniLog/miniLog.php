<?php

/* 
 * miniLog 日志类
 * @date 2016/12/21 
 * @author 300js
 * 简单快捷debug类
 * 优点:少配置或零配置,支持任何格式数据记录,支持数G数据存储.支持在浏览或linux环境查看
 * 配置:
 * 可以在外部更改的常量:
 *  支持html便捷浏览模式或纯txt查看,值html|txt
    defined('MINI_DEBUG_TYPE') or define('MINI_DEBUG_TYPE', 'html');
    调试模式,1可写,0不可写
    defined('MINI_DEBUG_FLAG') or define('MINI_DEBUG_FLAG', 1);
    jquery 地址
    defined('MINI_DEBUG_JSPAHT') or define('MINI_DEBUG_JSPAHT', 'http://cdn.bootcss.com/jquery/1.8.3/jquery.js');
    debug 可写的目录设置,结尾一定要加 保证有可写权限
    defined('MINI_DEBUG_PATH') or define('MINI_DEBUG_PATH', __DIR__ . DIRECTORY_SEPARATOR);
 *
 * 更改存储目录:
    define('MINI_DEBUG_PATH', __DIR__ . '/');//必须后面加斜杆 /
    miniLog::log('err', 'myFlag');
 * 存储不同的文件名:
    define('MINI_DEBUG_PATH', __DIR__ . '/');
    miniLog::setCacheFile(date('Y-m-di'));//无需设置文件后缀
    miniLog::log('err', 'myFlag');
 *  更改存储格式:
    define('MINI_DEBUG_PATH', __DIR__ . '/');
    define('MINI_DEBUG_TYPE', 'txt');//默认为html
    miniLog::log('err', 'myFlag');
 * 覆盖文件,相当将之前的数据删除,写入新的数据,可做清空数据用
    define('MINI_DEBUG_PATH', __DIR__ . '/');
    miniLog::log(1, 'myFlag', false);
 */
class miniLog {
    private static $config = array(
        'isSetFixx' => false,//是否已追加后缀标识
        'cacheFile' => 'debug',//缓存的文件名,无需设置后缀
    );
    private static $_cacheFile = '';

    /**
     * 设置日志文件,可文件名,可加相对路径,无需设置后缀名称 
     * @param string $cacheFile
     */
    final public static function setCacheFile($cacheFile) {      
        self::$config['cacheFile'] = $cacheFile;
    }
    /**
     * 记录日志
     * @param mix $data 数据
     * @param string $memo 标识
     * @param boolean $isPush 是否追求,默认true
     * @param string $titleStr title
     * @return boolean 成功与否
     */
    final public static function log($data, $memo = 'None', $isPush = true, $titleStr = '日志记录') {   
        self::_setBaseParam();
        return self::_baseLog($data, $memo, $isPush, $titleStr);
    }
    /**
     * 获取当前的缓存文件
     * @return type
     */
    final public static function getCacheFile() {
        return self::$_cacheFile;
    }

    
    /**
     * 基础的日志方法
     * @param mix $data 内容 
     * @param string $memo 标识
     * @param boolean $isPush 是否追求,默认true
     * @param string $titleStr Title
     * @return boolean|int
     */
    private static function _baseLog($data, $memo, $isPush = true, $titleStr = '日志记录') {
        if(!MINI_DEBUG_FLAG) return false;        
        $cacheFile = MINI_DEBUG_PATH . self::$config['cacheFile'];
        if(!$isPush) 
            unlink ($cacheFile);
        switch (strtolower(MINI_DEBUG_TYPE)) {
            case 'txt':                
                $str = self::_setFormatTxt($data, $memo);
                break;            
            default:
                $str = self::_setFormatHtml($data, $memo, $cacheFile, $titleStr);
                break;
        }
        self::$_cacheFile = $cacheFile;
        try{
            if($isPush)
                file_put_contents($cacheFile, $str, 8);                
            else 
                file_put_contents($cacheFile, $str);
        } catch (Exception $ex) {
        }
        return true;
    }
    /**
     * 设置html格式,可以浏览器上方便查看
     * @param type $data 数据
     * @param type $memo 标识
     * @param type $cacheFile 缓存文件
     * @param type $titleStr
     * @return string
     */
    private static function _setFormatHtml($data, $memo, $cacheFile, $titleStr){
        $DebugFilePath = $_SERVER["PHP_SELF"];//当前处理页面
        $timespan = microtime(true);//时间戳   
        $sBlockHTML = "\n\n\n<div class='block' _k='".md5($memo)."' _l='".$memo."'><span style='display:none'><------orderIndex-------></span>";
        /*判断是否存在头信息*/
        $baseHtml = '';        
        if(!is_file($cacheFile)) {
            $baseHtml = self::_getBaseHtml(MINI_DEBUG_JSPAHT, $titleStr);
        }
        $str = '';
        $str .= $baseHtml;
        $str .= $sBlockHTML;
        $str .= "<span  class='no' style='color:blue;'>NO</span>:\n\n";
        $str .= "<span  style='color:blue;'>Timespan</span>:\t".$timespan."\n\n";
        $str .= "\t<span  style='color:blue;'>Date</span>:\t".date("Y-m-d H:i:s")."\n";
        $str .= "\t<span  style='color:blue;'>File</span>:\t".$DebugFilePath."\n";
        $str .= "<br/><span class='memo' style='color:blue;'>Memo</span>:\t".$memo."<br>\n";
        $str .= "<span style='color:blue;'>Action:</span>:\t".$_SERVER['QUERY_STRING']."<br>\n";
        $str .= "----------------------------------------<span class='infoswitch'><a  href='javascript:void(0)' >展开/收起</a></span>\n<div class='info'>";    
        ob_start();
        if(is_array($data))
            print_r($data);
        elseif(is_string($data))
            echo $data;
        else
            var_dump($data);
        $a = ob_get_contents();
        ob_end_clean();
        $str .= "<xmp>";
        $str .= $a;
        $str .= "</xmp>";
        $str .= "</div>\n<hr></div>\n\n\n";
        return $str;
    }
    /**
     * 设置txt纯文本格式
     * @param type $data
     * @param type $memo
     * @return type
     */
    private static function _setFormatTxt($data, $memo) {
        $DebugFilePath = $_SERVER["PHP_SELF"];//当前处理页面        
        $str = '';
        $str .= 'Memo:' . $memo;
        $str .= ' Time:' . date('Y-m-d H:i:s');
        $str .= ' File:' . $DebugFilePath;
        $str .= PHP_EOL;
        ob_start();
        if(is_array($data))
            print_r($data);
        elseif(is_string($data))
            echo $data;
        else
            var_dump ($data);
        $a = ob_get_contents();
        ob_end_clean();
        $str .= $a;        
        $str .= PHP_EOL;
        return $str;
    }

    /**
     * 获取处理的js html
     * @param type $jsPath
     * @param type $titleStr
     * @return type
     */
    private static function _getBaseHtml($jsPath, $titleStr) {        
        $baseHtml = <<<___html
    <!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>{$titleStr}</title><script src="{$jsPath}" type="text/javascript"></script><style type="text/css">
    body {
    margin: 0px;
    padding: 0px;
    height: 100%;
    }

    body, th, td {
    font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;
    font-size: 12px;
    color: #333;
    }
    </style></head><body><div id="tabs"></div></body></html>
    <script>
    $(function() {
        var _oMemo = {all:{label:'all', total:$("div.block").length}};
        var j=1;
        $("div.block").each(function(){
            if(typeof _oMemo[$(this).attr('_k')] == 'undefined') {
                _oMemo[$(this).attr('_k')] = {};
            }    
            if(typeof _oMemo[$(this).attr('_k')]['total'] == 'undefined'){                
                _oMemo[$(this).attr('_k')]['total'] = 1;
                _oMemo[$(this).attr('_k')]['label'] = $(this).attr('_l');
                $(this).find(".no").html("NO:" + j);
            }else {
                _oMemo[$(this).attr('_k')]['total'] += 1;  
                $(this).find(".no").html("NO:" +  j);
            }
            j++;
        });

        var sUl = "";
        for(var k in _oMemo){
            sUl += '<li><a _k="'+k+'" href="javascript:void(0)" >'+_oMemo[k]['label']+'('+_oMemo[k]['total']+')</a></li>';
        }
        $('div#tabs').html("<ul>"+sUl+"</ul><div  style=\"position:absolute;top:10px;right:20px;\" class='allinfoSwith'><a href='javascript:void(0)' >全部 展开/收起</a></div>");
        $('div#tabs li a').click(function(){
            var _showK = $(this).attr('_k');
            if(_showK == 'all'){
                var i = 1;
                $("div.block").each(function(){
                    $(this).find(".no").html("NO:" + i);
                    i++;                    
                });
                $('div.block').show();                
            }else{                
                $('div.block').hide();
                $('div.block[_k="'+_showK+'"]').show();
                var p = 1;
                $('div.block[_k="'+_showK+'"]').each(function(){
                    $(this).find('.no').html("NO:"+p);
                    p++;
                });
            }
        });
        $('div.block span.infoswitch a').click(function(){
            var _o = $(this).parents('div.block').find('div.info').eq(0);
            _o.toggle();
        });
        var allinfoSwithIndex = 0;
        $('div.allinfoSwith a').click(function(){
            allinfoSwithIndex%2==0 ? $('div.info').hide() : $('div.info').show();
            allinfoSwithIndex++;
        });
    });
    </script>
___html;
        return $baseHtml;
    }
    /**
     * 初使常量及判断
     */
    private static function _setBaseParam(){        
        /**支持html便捷浏览模式或纯txt查看,值html|txt*/
        defined('MINI_DEBUG_TYPE') or define('MINI_DEBUG_TYPE', 'html');
        /**调试模式,1可写,0不可写*/
        defined('MINI_DEBUG_FLAG') or define('MINI_DEBUG_FLAG', 1);
        /**jquery 地址*/
        defined('MINI_DEBUG_JSPAHT') or define('MINI_DEBUG_JSPAHT', 'http://cdn.bootcss.com/jquery/1.8.3/jquery.js');
        /**debug 可写的目录设置,结尾一定要加 / */
        defined('MINI_DEBUG_PATH') or define('MINI_DEBUG_PATH', __DIR__ . DIRECTORY_SEPARATOR);
        self::_setFileFixx();
    }
    /**
     * 设置文件后缀
     * @param type $cacheFile
     */
    private static function _setFileFixx() {
        $_default_name = '_log';
        if(stripos(self::$config['cacheFile'], 'debug') !== false) {
            $_default_name = '';
        }
        $baseName = pathinfo(self::$config['cacheFile'], PATHINFO_BASENAME);
        if(strpos($baseName, '.') !== false && self::$config['isSetFixx'] == true) {
            return;
        }
        switch (strtolower(MINI_DEBUG_TYPE)) {
            case 'txt':                
                self::$config['cacheFile'] .= $_default_name . '.txt';
                self::$config['isSetFixx'] = true;
                break;                    
            default:
                self::$config['cacheFile'] .= $_default_name . '.html';
                self::$config['isSetFixx'] = true;
                break;
        }
    }
}