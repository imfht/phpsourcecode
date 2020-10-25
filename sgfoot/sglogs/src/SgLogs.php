<?php
/********************
* 使用方法:
* 自定义写入目录: define('SGLOGS_PATH', '/var/logs/');
* require 'SgLogs.php';
* sglogs('xxx', 'demo');
* 
*********************/
/**
 * 时光日志
 * User: sgfoot
 * Date: 2017/11/13
 * Time: 13:45
 */
if(!function_exists('sglogs')) {
    /**
     * @param $data mixed 任意数据类型
     * @param $flag string 标识
     * @param $isClear bool/string 清除数据,true/false/all
     */
    function sglogs($data, $flag, $isClear = false) {
        SgLogs::write($data, $flag, $isClear);
    }
    
}
class SgLogs
{
    /**
     * 配置文件
     * @var array
     */
    protected static $config
        = array(
            'title'          => 'SgLogs',//标题
            'jquery'         => 'https://cdn.bootcss.com/jquery/1.9.1/jquery.js',//第三方jquery
            'cache_filename' => 'cache.filename',//缓存文件名称
        );

    /**
     * 设置默认常量
     */
    protected static function setDefine()
    {
        /**定义用户名*/
        defined('SGLOGS_USER') or define('SGLOGS_USER', 'sglogs');
        /**定义密码*/
        defined('SGLOGS_PASS') or define('SGLOGS_PASS', 'sglogs');
        /**支持html便捷浏览模式或纯txt查看,值html|txt*/
        defined('SGLOGS_TYPE') or define('SGLOGS_TYPE', 'php');
        //主题,支持default,white,green,bgede(小说常用背景)
        defined('SGLOGS_THEME') or define('SGLOGS_THEME', 'default');
        /**调试模式,1可写,0不可写*/
        defined('SGLOGS_MODE') or define('SGLOGS_MODE', 1);
        //设置文件大小,单位m
        defined('SGLOGS_MAX') or define('SGLOGS_MAX', 4);//设置为0的话就不删除,
        /**debug 可写的目录设置,结尾一定要加 / */
        defined('SGLOGS_PATH') or define('SGLOGS_PATH', __DIR__ . DIRECTORY_SEPARATOR);
    }

    /**
     * 核心方法,外部调用
     * @param $data mixed 任意数据
     * @param $flag string 标识
     * @param bool $flush false/true 若为all表示清除所有文件
     * @param string $fileName 文件名称
     * @param string $title 文件的title
     * @return bool 成功与否
     */
    public static function write($data, $flag = 'None', $flush = false, $fileName = 'debug', $title = 'SgLogs')
    {
        self::$config['title'] = $title;
        if (strpos($fileName, '/') === 0) {
            $fileName = substr($fileName, 1);
        }
        $file = '';
        $html = self::bootstrap($file, $fileName, $flush);
        if (SGLOGS_TYPE == 'php') {
            $html .= self::htmlByBox($data, $flag);
        } else if (SGLOGS_TYPE == 'html') {
            $html .= self::htmlByBox($data, $flag);
        } else {
            $html .= self::txtByBox($data, $flag);
        }
        if (!SGLOGS_MODE) {
            return false;
        }
        return file_put_contents($file, $html, FILE_APPEND) ? true : false;
    }

    /**
     * 启动配置
     * @param $file string 返回文件
     * @param string $name 文件名称
     * @param string $flush 是否清理文件
     * @return string
     */
    protected static function bootstrap(&$file, $name, $flush)
    {
        self::setDefine();
        $file = SGLOGS_PATH . $name . '.' . SGLOGS_TYPE;
        $html = '';
        $file = str_replace('\\', '/', $file);
        $dir  = dirname($file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
            if (!is_dir($dir)) {
                trigger_error($dir . '目录不可写');
            }
        }
        $pathinfo       = pathinfo($file);
        $cache_filename = $pathinfo['dirname'] . '/' . self::$config['cache_filename'];
        $isCache        = false;
        if (is_file($cache_filename)) {
            $isCache   = true;
            $file_data = file_get_contents($cache_filename);
            $files     = explode(',', trim($file_data, ','));
            $file      = end($files);
        }
        if ($flush) {
            if (is_bool($flush)) {
                unlink($file);
            } elseif ($flush === 'all') {
                if (is_file($cache_filename)) {
                    $file_data = file_get_contents($cache_filename);
                    $files     = explode(',', trim($file_data, ','));
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                    unlink($cache_filename);
                    $file = SGLOGS_PATH . $name . '.' . SGLOGS_TYPE;
                }
                if (is_file($file)) {
                    unlink($file);
                }
            }
        } else {
            if (is_file($file) && SGLOGS_MAX > 0) {
                $fileSizeM = filesize($file) / pow(1024, 2);
                if ($fileSizeM > SGLOGS_MAX) {
                    if ($isCache) {
                        $count      = count($files);
                        $cache_file = $pathinfo['dirname'] . '/' . date('Y-m-d.') . $pathinfo['filename'] . '.' . $count . '.' . SGLOGS_TYPE . ',';
                        file_put_contents($cache_filename, $cache_file, FILE_APPEND);
                    } else {
                        file_put_contents($cache_filename, $file . ',', FILE_APPEND);
                    }
                }
            }
        }
        if (!is_file($file)) {
            if (strtolower(SGLOGS_TYPE) == 'html') {
                $html .= self::htmlByHeader();
                $html .= self::htmlByAsset();
                $html .= self::htmlByCss();
                $html .= self::htmlByScript();
                $html .= self::htmlByTabs();
            } else if (strtolower(SGLOGS_TYPE) == 'php') {
                $html .= self::phpByHeader();
                $html .= self::htmlByAsset();
                $html .= self::htmlByCss();
                $html .= self::htmlByScript();
                $html .= self::htmlByTabs();
            }
        }
        return $html;
    }

    /**
     * 获取ob_start
     * @param $data
     * @return string
     */
    protected static function getObCache($data)
    {
        ob_start();
        if (is_array($data))
            print_r($data);
        elseif (is_string($data))
            echo $data;
        else
            var_dump($data);
        $a = ob_get_contents();
        ob_end_clean();
        return $a;
    }


    /**
     * 头部html
     */
    protected static function htmlByHeader()
    {
        $html
            = <<<html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>%s</title>
html;
        return sprintf($html, self::$config['title']);
    }

    /**
     * 头部html
     */
    protected static function phpByHeader()
    {
        $php = <<<php
<?php
            \$user      = '%s';
            \$pass      = '%s';
            \$user_list = array();
            \$pass_list = array();
            if (is_array(\$user)) {
                \$user_list = array_merge(\$user_list, \$user);
            } else {
                array_push(\$user_list, \$user);
            }
            if (is_array(\$pass)) {
                \$pass_list = array_merge(\$user_list, \$pass);
            } else {
                array_push(\$pass_list, \$pass);
            }
            if (!isset(\$_SERVER['PHP_AUTH_USER']) || !isset(\$_SERVER['PHP_AUTH_PW']) ||
                !in_array(\$_SERVER['PHP_AUTH_USER'], \$user_list) || !in_array(\$_SERVER['PHP_AUTH_PW'], \$pass_list)) {
                Header("WWW-Authenticate: Basic realm=\"sglogs Login\"");
                Header("HTTP/1.0 401 Unauthorized");
                echo <<<EOB
                <html><body>
                <h1>拒绝访问!</h1>
                <big>请输入帐号和密码</big>
                </body></html>
EOB;
                exit;
            }
?>
php;
        $php = sprintf($php, SGLOGS_USER, SGLOGS_PASS);
        $html
             = <<<html
%s<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="http://www.sgfoot.com/favicon.png" rel="shortcut icon"/>
    <title>%s</title>
html;
        return sprintf($html, $php, self::$config['title']);
    }

    /**
     * 引用外部文件
     * @return string
     */
    protected static function htmlByAsset()
    {
        $html
            = <<<html
<script src="%s"></script>
html;
        return sprintf($html, self::$config['jquery']);
    }

    /**
     * css代码
     * @return string
     */
    protected static function htmlByCss()
    {
        $html
            = <<<html
<style type="text/css">
        body, th, td {
            font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
        }

        body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        .box {
            margin-top: 5px;
            border: solid 1px #5FB878;
            border-top-style: none;
            word-wrap: break-word;
            white-space : normal;
        }

        .box .header {
            margin-bottom: 10px;
            padding: 8px;
            line-height: 22px;
            border-left: 5px solid #009688;
            border-radius: 0 2px 2px 0;
            background-color: #f2f2f2;
            cursor: pointer;
        }
        xmp {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .box .header span {
            color: #1E9FFF;
        }

        .box .header span:nth-last-child(2) {
            font-size: 14px;
            font-weight: bold;
            color: #FF5722;
            display: inline-block;
            margin-left:100px;
            margin-right: 10px;
        }

        .box .header span:last-child {
            font-size: 14px;
            font-weight: bold;
            color: #2F4056;
        }
        .box .header:hover {
            background-color: #dddddd;
        }
        .box .body .default{
            display: block;
            margin: 10px 0;
            border: 1px solid #e2e2e2;
            border-left-width: 6px;
            background-color: #2F4056;
            color:#F0F0F0;
            font-family: Courier New;
            padding: 5px;
        }
        .box .body .white{
            display: block;
            margin: 10px 0;
            border: 1px solid #e2e2e2;
            border-left-width: 6px;
            background-color: #f2f2f2;
            color: #2F4056;
            font-family: Courier New;
            padding: 5px;
        }
        .box .body .green{
            display: block;
            margin: 10px 0;
            border: 1px solid #e2e2e2;
            border-left-width: 6px;
            background-color: #5FB878;
            color: #2F4056;
            font-family: Courier New;
            padding: 5px;
        }
        .box .body .bgede{
            display: block;
            margin: 10px 0;
            border: 1px solid #e2e2e2;
            border-left-width: 6px;
            background-color: #ede7da;
            color: #000;
            font-family: Courier New;
            padding: 5px;
        }
        pre{
            white-space:pre-wrap;
            white-space:-moz-pre-wrap;
            white-space:-pre-wrap;
            white-space:-o-pre-wrap;
            word-wrap:break-word;
        }
        p#back-to-top {
            position: fixed;
            bottom: 18px;
            right: 12px;
            display: none;
        }

        p#back-to-top a {
            text-align: center;
            text-decoration: none;
            color: #000;
            display: block;
            width: 50px;
            cursor: pointer;
            /*使用CSS3中的transition属性给跳转链接中的文字添加渐变效果*/
            -moz-transition: color1s;
            -webkit-transition: color1s;
            -o-transition: color1s;
        }

        p#back-to-top a:hover {
            color: #000011;
        }

        p#back-to-top a span {
            background-color: #01AAED;
            z-index: -100;
            border: 1px solid #cccccc;
            border-radius: 50%;
            display: block;
            height: 30px;
            width: 30px;
            margin-bottom: 5px;
            /*使用CSS3中的transition属性给<span>标签背景颜色添加渐变效果*/
            -moz-transition: background1s;
            -webkit-transition: background1s;
            -o-transition: background1s;
        }
    </style>
html;
        return $html;
    }

    /**
     * scripts核心代码
     * @return string
     */
    protected static function htmlByScript()
    {
        $html
            = <<<html
<script>
        $(function () {
            var all = {label: 'all', total: $("div.box").length};
            var _oMemo = {};
            var j = 1;
            $("div.box").each(function () {
                if (typeof _oMemo[$(this).attr('_k')] == 'undefined') {
                    _oMemo[$(this).attr('_k')] = {};
                }
                if (typeof _oMemo[$(this).attr('_k')]['total'] == 'undefined') {
                    _oMemo[$(this).attr('_k')]['total'] = 1;
                    _oMemo[$(this).attr('_k')]['label'] = $(this).attr('_l');
                    $(this).find(".no").html("NO:" + j);
                } else {
                    _oMemo[$(this).attr('_k')]['total'] += 1;
                    $(this).find(".no").html("NO:" + j);
                }
                j++;
            });
            var sUl = "";
            sUl += '<li><a _k="all" href="javascript:void(0)" >' + all.label + '(' + all.total + ')</a></li>';
            for (var k in _oMemo) {
                sUl += '<li><a _k="' + k + '" href="javascript:void(0)" >' + _oMemo[k]['label'] + '(' + _oMemo[k]['total'] + ')</a></li>';
            }
            $('div#tabs').html("<ol>" + sUl + "</ol><div  style=\"position:absolute;top:10px;right:20px;\" class='allinfoSwith'><a href='javascript:void(0)' >全部 展开/收起</a></div>");
            $('div#tabs li a').click(function () {
                var _showK = $(this).attr('_k');
                if (_showK == 'all') {
                    var i = 1;
                    $("div.box").each(function () {
                        $(this).find(".no").html("NO:" + i);
                        i++;
                    });
                    $('div.box').show();
                } else {
                    $('div.box').hide();
                    $('div.box[_k="' + _showK + '"]').show();
                    var p = 1;
                    $('div.box[_k="' + _showK + '"]').each(function () {
                        $(this).find('.no').html("NO:" + p);
                        p++;
                    });
                }
            });
            $('div.box .header').click(function () {
                var _o = $(this).parents('div.box').find('div.body').eq(0);
                _o.toggle();
            });
            var allinfoSwithIndex = 0;
            $('div.allinfoSwith a').click(function () {
                allinfoSwithIndex % 2 == 0 ? $('div.body').hide() : $('div.body').show();
                allinfoSwithIndex++;
            });
             //当滚动条的位置处于距顶部600像素以下时，跳转链接出现，否则消失
              $(window).scroll(function(){
                if ($(window).scrollTop()>100){
                    $("#back-to-top").fadeIn(500);
                }else{
                    $("#back-to-top").fadeOut(500);
                }
            });
            //当点击跳转链接后，回到页面顶部位置
            $("#back-to-top").click(function(){
                $('body,html').animate({scrollTop:0},500);
                return false;
            });
        });
    </script>
html;
        return $html;
    }

    /**
     * tabs导航div
     * @return string
     */
    protected static function htmlByTabs()
    {
        $html
            = <<<html
<p id="back-to-top"><a href="#top"><span class="moveUp"></span></a></p>
<div id="tabs">
</div>
html;
        return $html;
    }

    /**
     * 核心box
     * @param $data mixed 数据
     * @param $flag string 标识
     * @return string
     */
    protected static function phpByBox($data, $flag = 'default')
    {
        $html
                     = <<<html
<div class="box" _k="%s" _l="%s">
    <div class="header" title="点击打开或关闭">
        <span class="no"></span>
        <span>时间戳:</span>%s
        <span>时间:</span>%s
        <span>文件:</span>%s
        <span>参数:</span>%s
        <span>标识:</span><span>%s</span>
    </div>
    <div class="body">
    <xmp class="%s">
%s
    </xmp>
    </div>
</div>
html;
        $md5         = md5($flag);
        $time        = time();
        $date        = date('Y-m-d H:i:s', $time);
        $file        = $_SERVER["PHP_SELF"];//当前处理页面
        $queryParams = $_SERVER['QUERY_STRING'];
        $data        = self::getObCache($data);
        return sprintf($html, $md5, $flag, $time, $date, $file, $queryParams, $flag, SGLOGS_THEME, $data);
    }

    /**
     * 核心box
     * @param $data mixed 数据
     * @param $flag string 标识
     * @return string
     */
    protected static function htmlByBox($data, $flag = 'default')
    {
        $html
                     = <<<html
<div class="box" _k="%s" _l="%s">
    <div class="header" title="点击打开或关闭">
        <span class="no"></span>
        <span>时间戳:</span>%s
        <span>时间:</span>%s
        <span>文件:</span>%s
        <span>标识:</span><span>%s</span>
    </div>
    <div class="body">
    <xmp class="%s">
%s
    </xmp>
    </div>
</div>
html;
        $md5         = md5($flag);
        $time        = time();
        $date        = date('Y-m-d H:i:s', $time);
        $file        = $_SERVER["PHP_SELF"];//当前处理页面
        $queryParams = $_SERVER['QUERY_STRING'];
        $data        = self::getObCache($data);
        return sprintf($html, $md5, $flag, $time, $date, $file . $queryParams, $flag, SGLOGS_THEME, $data);
    }

    /**
     * 写文本日志
     * @param $data
     * @param string $flag
     * @return string
     */
    protected static function txtByBox($data, $flag = 'default')
    {
        $DebugFilePath = $_SERVER["PHP_SELF"];//当前处理页面
        $str           = '';
        $str           .= 'Memo:' . $flag;
        $str           .= ' Time:' . date('Y-m-d H:i:s');
        $str           .= ' File:' . $DebugFilePath;
        $str           .= PHP_EOL;
        ob_start();
        if (is_array($data))
            print_r($data);
        elseif (is_string($data))
            echo $data;
        else
            var_dump($data);
        $a = ob_get_contents();
        ob_end_clean();
        $str .= $a;
        $str .= PHP_EOL;
        return $str;
    }
}