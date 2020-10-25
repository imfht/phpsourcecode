<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\mvc;

use tfc\ap\Ap;

/**
 * Html class file
 * 页面辅助类，帮助创建HTML Element
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Html.php 1 2013-04-14 20:00:06Z huan.song $
 * @package tfc.mvc
 * @since 1.0
 */
class Html
{
    /**
     * @var string 页面编码方式
     */
    protected $_encoding;

    /**
     * 构造方法：初始化页面编码方式
     * @param string $encoding
     */
    public function __construct($encoding = null)
    {
        if ($encoding === null) {
            $encoding = Ap::getEncoding();
        }

        $this->setEncoding($encoding);
    }

    /**
     * 获取表单元素：<input type="text" />
     * @param string $name
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function text($name, $value = '', $attributes = array())
    {
        return $this->input('text', $name, $value, $attributes);
    }

    /**
     * 获取表单元素：<input type="email" />
     * @param string $name
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function email($name, $value = '', $attributes = array())
    {
        return $this->input('email', $name, $value, $attributes);
    }

    /**
     * 获取表单元素：<input type="password" />
     * @param string $name
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function password($name, $value = '', $attributes = array())
    {
        return $this->input('password', $name, $value, $attributes);
    }

    /**
     * 获取表单元素：<input type="hidden" />
     * @param string $name
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function hidden($name, $value, $attributes = array())
    {
        return $this->input('hidden', $name, $value, $attributes);
    }

    /**
     * 获取表单元素：<input type="radio" />
     * @param string $name
     * @param mixed $value
     * @param boolean $checked
     * @param array $attributes
     * @return string
     */
    public function radio($name, $value, $checked = false, $attributes = array())
    {
        if ($checked) {
            $attributes['checked'] = 'checked';
        }

        return $this->input('radio', $name, $value, $attributes);
    }

    /**
     * 获取表单元素：<input type="checkbox" />
     * @param string $name
     * @param mixed $value
     * @param boolean $checked
     * @param array $attributes
     * @return string
     */
    public function checkbox($name, $value, $checked = false, $attributes = array())
    {
        if ($checked) {
            $attributes['checked'] = 'checked';
        }

        return $this->input('checkbox', $name, $value, $attributes);
    }

    /**
     * 获取表单元素：<input type="file" />
     * @param string $name
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function file($name, $value = '', $attributes = array())
    {
        return $this->input('file', $name, $value, $attributes);
    }

    /**
     * 获取表单元素：<input type="submit" />
     * @param mixed $value
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public function submit($value, $name = 'submit', $attributes = array())
    {
        return $this->input('submit', $name, $value, $attributes);
    }

    /**
     * 获取表单元素：<input type="reset" />
     * @param mixed $value
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public function reset($value, $name = 'reset', $attributes = array())
    {
        return $this->input('reset', $name, $value, $attributes);
    }

    /**
     * 获取表单元素：<input type="image" src="" />
     * @param string $src
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public function image($src, $name = 'image', $attributes = array())
    {
        $attributes['src'] = $src;
        return $this->input('image', $name, '', $attributes);
    }

    /**
     * 获取表单元素：<input type="button" />
     * @param mixed $value
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public function button($value, $name = 'button', $attributes = array())
    {
        return $this->input('button', $name, $value, $attributes);
    }

    /**
     * 获取表单元素：<textarea></textarea>
     * @param string $name
     * @param string $content
     * @param array $attributes
     * @param boolean $encode
     * @return string
     */
    public function textarea($name, $content = '', $attributes = array(), $encode = true)
    {
        $attributes['name'] = $name;
        if ($encode) {
            $content = $this->encode($content);
        }

        return $this->tag('textarea', $attributes, $content, true);
    }

    /**
     * 获取多个表单元素：<option></option>\n<option></option>\n...\n<option></option>
     * @param array $data
     * @param mixed $selectedValue
     * @param array $attributes
     * @return string
     */
    public function options(array $data, $selectedValue, $attributes = array())
    {
        $html = '';
        foreach ($data as $value => $prompt) {
            $selected = (($value == $selectedValue) ? true : false);
            $html .= $this->option($prompt, $value, $selected, $attributes);
        }

        return $html;
    }

    /**
     * 获取表单元素：<option></option>
     * @param string $prompt
     * @param mixed $value
     * @param boolean $selected
     * @param array $attributes
     * @return string
     */
    public function option($prompt, $value, $selected = false, $attributes = array())
    {
        if ($selected) {
            $attributes['selected'] = 'selected';
        }

        $attributes['value'] = $value;
        return $this->tag('option', $attributes, $prompt, true);
    }

    /**
     * 获取表单元素：<input />
     * @param string $type
     * @param string $name
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function input($type, $name, $value = '', $attributes = array())
    {
        $attributes['type'] = $type;
        $attributes['name'] = $name;
        $attributes['value'] = $value;
        return $this->tag('input', $attributes, false, true);
    }

    /**
     * 获取CSS文件标签
     * @param string $href
     * @param string $media
     * @param string $pad
     * @return string
     */
    public function cssFile($href, $media = '', $pad = "\n")
    {
        return $this->link('stylesheet', 'text/css', $href, ($media !== '' ? $media : null), array()) . $pad;
    }

    /**
     * 获取JavaScript文件标签
     * @param string $src
     * @param string $pad
     * @return string
     */
    public function jsFile($src, $pad = "\n")
    {
        return '<script type="text/javascript" src="' . $this->encode($src) . '"></script>' . $pad;
    }

    /**
     * 获取Css代码
     * @param string $text
     * @param string $media
     * @return string
     */
    public function css($text, $media = '')
    {
        if ($media !== '') {
            $media = ' media="' . $media . '"';
        }

        return "<style type=\"text/css\"{$media}>\n/*<![CDATA[*/\n{$text}\n/*]]>*/\n</style>";
    }

    /**
     * 获取JavaScript代码
     * @param string $text
     * @return string
     */
    public function js($text)
    {
        return "<script type=\"text/javascript\">\n/*<![CDATA[*/\n{$text}\n/*]]>*/\n</script>";
    }

    /**
     * 获取img标签：<img src="" />
     * @param string $src
     * @param string $alt
     * @param array $attributes
     * @return string
     */
    public function img($src, $alt = '', $attributes = array())
    {
        $attributes['src'] = $src;
        if ($alt !== '') {
            $attributes['alt'] = $alt;
        }

        return $this->tag('img', $attributes, false, true);
    }

    /**
     * 获取链接标签：<a href=""></a>
     * @param string $content
     * @param string $href
     * @param array $attributes
     * @return string
     */
    public function a($content, $href = '#', $attributes = array())
    {
        if ($href !== '') {
            $attributes['href'] = $href;
        }

        return $this->tag('a', $attributes, $content, true);
    }

    /**
     * 获取Select开始标签：<select>
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public function openSelect($name, $attributes = array())
    {
        $attributes['name'] = $name;
        return $this->openTag('select', $attributes);
    }

    /**
     * 获取Select结束标签：</select>
     * @return string
     */
    public function closeSelect()
    {
        return $this->closeTag('select');
    }

    /**
     * 获取Form开始标签：<form action="">
     * @param string $action
     * @param string $method
     * @param array $attributes
     * @return string
     */
    public function openForm($action = '#', $method = 'post', $attributes = array())
    {
        $attributes['action'] = $action;
        $attributes['method'] = $method;
        return $this->openTag('form', $attributes);
    }

    /**
     * 获取二进制Form开始标签：<form action="" enctype="multipart/form-data">
     * @param string $action
     * @param array $attributes
     * @return string
     */
    public function openFormMultipart($action = '#', $attributes = array())
    {
        $attributes['enctype'] = 'multipart/form-data';
        return $this->openForm($action, 'post', $attributes);
    }

    /**
     * 获取Form结束标签：</form>
     * @return string
     */
    public function closeForm()
    {
        return $this->closeTag('form');
    }

    /**
     * 获取Fieldset开始标签：<fieldset>
     * @param array $attributes
     * @return string
     */
    public function openFieldset($attributes = array())
    {
        return $this->openTag('fieldset', $attributes);
    }

    /**
     * 获取Fieldset结束标签：</fieldset>
     * @return string
     */
    public function closeFieldset()
    {
        return $this->closeTag('fieldset');
    }

    /**
     * 获取legend标签：<legend></legend>
     * @param string $content
     * @param array $attributes
     * @return string
     */
    public function legend($content, $attributes = array())
    {
        return $this->tag('legend', $attributes, $content, true);
    }

    /**
     * 获取meta标签：<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     * @return string
     */
    public function contentType()
    {
        return $this->meta('text/html; charset=' . $this->getEncoding(), 'Content-Type');
    }

    /**
     * 获取meta标签：<meta name="" http-equiv="" content="" />
     * @param string $content
     * @param string $name
     * @param string $httpEquiv
     * @param array $attributes
     * @param string $pad
     * @return string
     */
    public function meta($content, $httpEquiv = '', $name = '', $attributes = array(), $pad = "\n")
    {
        if ($name !== '') {
            $attributes['name'] = $name;
        }

        if ($httpEquiv !== '') {
            $attributes['http-equiv'] = $httpEquiv;
        }

        $attributes['content'] = $content;
        return $this->tag('meta', $attributes, false, true) . $pad;
    }

    /**
     * 获取link标签：<link rel="" type="" href="" />
     * @param string $relation
     * @param string $type
     * @param string $href
     * @param string $media
     * @param array $attributes
     * @param string $pad
     * @return string
     */
    public function link($relation = null, $type = null, $href = null, $media = null, $attributes = array())
    {
        if ($relation !== null) {
            $attributes['rel'] = $relation;
        }

        if ($type !== null) {
            $attributes['type'] = $type;
        }

        if ($href !== null) {
            $attributes['href'] = $href;
        }

        if ($media !== null) {
            $attributes['media'] = $media;
        }

        return $this->tag('link', $attributes, false, true);
    }

    /**
     * 通过标签名，获取Html标签
     * @param string $name
     * @param array $attributes
     * @param mixed $content
     * @param boolean $closeTag
     * @return string
     */
    public function tag($name, $attributes = array(), $content = false, $closeTag = true)
    {
        $html = '<' . $name . $this->parseAttributes($attributes);
        if ($content === false && $closeTag) {
            return $html . ' />';
        }

        if ($content !== false) {
            $html .= '>' . $content;
        }

        return $closeTag ? $html . $this->closeTag($name) : $html;
    }

    /**
     * 通过标签名，获取Html开始标签
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public function openTag($name, $attributes = array())
    {
        return '<' . $name . $this->parseAttributes($attributes) . '>';
    }

    /**
     * 通过标签名，获取Html结束标签
     * @param string $name
     * @return string
     */
    public function closeTag($name)
    {
        return '</' . $name . '>';
    }

    /**
     * 拼接Html标签中的属性
     * @param array $attributes
     * @param boolean $encode
     * @return string
     */
    public function parseAttributes(array $attributes, $encode = true)
    {
        if (!$attributes) {
            return '';
        }

        $html = '';
        foreach ($attributes as $name => $value) {
            if (is_numeric($name)) {
                $html .= ' ' . $value;
                continue;
            }

            if ($name == 'value' && $encode) {
                $value = $this->encode($value);
            }

            $html .= ' ' . $name . '="' . $value . '"';
        }

        return $html;
    }

    /**
     * 对页面展示内容编码
     * @param mixed $param
     * @return string
     */
    public function encode($param)
    {
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = $this->encode($value);
            }
        }
        else {
            $param = htmlspecialchars($param, ENT_QUOTES, $this->getEncoding());
        }

        return $param;
    }

    /**
     * 对页面展示内容解码
     * @param mixed $value
     * @return string
     */
    public function decode($value)
    {
        return htmlspecialchars_decode($value, ENT_QUOTES, $this->getEncoding());
    }

    /**
     * 获取页面编码方式
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * 设置页面编码方式
     * @param string $encoding
     * @return \tfc\mvc\Html
     */
    public function setEncoding($encoding)
    {
        $this->_encoding = (string) $encoding;
        return $this;
    }

    /**
     * 忽略文本中被解析的字符数据
     * @param string $text
     * @return string
     */
    public function cdata($text)
    {
        return '<![CDATA[' . $text . ']]>';
    }
}
