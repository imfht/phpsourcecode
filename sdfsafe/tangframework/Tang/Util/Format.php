<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Util;
/**
 * Class Format
 * 格式化类
 * @package Tang\Util
 */
class Format
{
    /**
     * 在指定的预定义字符前添加反斜杠
     * @param $data 数据
     * @param bool $isHtmlFormat 是否将特殊字符转为html实体
     * @return array|string
     */
    public static function addslashes(&$data,$isHtmlFormat = false)
    {
        if(is_array($data))
        {
            foreach ($data as $key=>$value)
            {
                $data[$key] = static::addslashes($value,$isHtmlFormat);
            }
        }else
        {
            $data = addslashes($data);
            if($isHtmlFormat)
            {
                $data = htmlspecialchars($data);
            }
        }
        return $data;
    }

    /**
     * 移除XSS
     * @param $string 字符串
     * @return string
     */
    public static function removeXss($string)
    {
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);
        $tags = array('javascript','vbscript', 'expression', 'applet','meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base','onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        for ($i = 0; $i < sizeof($tags); $i++)
        {
            $pattern = '/';
            for ($j = 0; $j < strlen($tags[$i]); $j++)
            {
                if ($j > 0)
                {
                    $pattern .= '(';
                    $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                    $pattern .= '|(&#0([9][10][13]);?)?';
                    $pattern .= ')?';
                }
                $pattern .= $tags[$i][$j];
            }
            $pattern .= '/i';
            $string = preg_replace($pattern, '', $string);
        }
        return $string;
    }

    /**
     * 日期格式化
     * @param $format
     * @param int $timestamp
     * @return string
     */
    public static function date($format,$timestamp = null)
    {
        $timestamp = (int)$timestamp;
        if($timestamp < 0)
        {
            return '';
        }
        return date($format,$timestamp);
    }

    /**
     * 函数删除由 addslashes() 函数添加的反斜杠
     * @param $data
     * @return string
     */
    public static function stripslashes(&$data)
    {
        if(is_array($data))
        {
            foreach ($data as $key=>$value)
            {
                $Arr[$key] = static::stripslashes($value);
            }
        }else
        {
            $data = stripslashes($data);
        }
        return $data;
    }

    /**
     * 把预定义的字符转换为 HTML 实体
     * @param $data
     * @return array|string
     */
    public static function htmlSpecialchars(&$data)
    {
        if(is_array($data))
        {
            foreach ($data as $key=>$value)
            {
                $data[$key] = static::htmlSpecialchars($value);
            }
        }else
        {
            $data = htmlspecialchars($data);
        }
        return $data;
    }

    /**
     * 把预定义的HTML 实体转换为字符
     * @param $data
     * @return array|string
     */
    public static function htmlspecialcharsDecode(&$data)
    {
        if(is_array($data))
        {
            foreach ($data as $key=>$value)
            {
                $data[$key] = static::htmlspecialcharsDecode($value);
            }
        }else
        {
            $data = htmlspecialchars_decode($data);
        }
        return $data;
    }

    /**
     * 金钱格式化
     * @param $money 金额
     * @param int $decimal 小数点位数
     * @return string
     */
    public static function money($money,$decimal = 2)
    {
        return sprintf('%0.'.$decimal.'f',$money);
    }
}