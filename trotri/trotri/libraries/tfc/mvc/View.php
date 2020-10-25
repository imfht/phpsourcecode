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

use tfc\mvc\interfaces;
use tfc\ap\Singleton;
use tfc\ap\ErrorException;
use tfc\ap\InvalidArgumentException;

/**
 * View class file
 * 模板解析类，用于分离业务层和展现层
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: View.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc
 * @since 1.0
 */
class View implements interfaces\View
{
    /**
     * @var string 模板文件所在的目录
     */
    public $viewDirectory = '';

    /**
     * @var string 模板风格
     */
    public $skinName = 'default';

    /**
     * @var string 模板后缀
     */
    public $tplExtension = '.php';

    /**
     * @var string Js、Css文件的版本号
     */
    public $version = '1.0';

    /**
     * @var array 页面依次渲染的布局名
     */
    protected $_layoutNames = array();

    /**
     * @var instance of tfc\mvc\Html
     */
    protected $_html = null;

    /**
     * @var instance of tfc\mvc\UrlManager
     */
    protected $_urlManager = null;

    /**
     * @var array 用于寄存渲染模板的变量名和值
     */
    protected $_tplVars = array();

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\interfaces\View::getEngine()
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\interfaces\View::__get()
     */
    public function __get($key)
    {
        if (isset($this->_tplVars[$key])) {
            return $this->_tplVars[$key];
        }

        return null;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\interfaces\View::__set()
     */
    public function __set($key, $value)
    {
        if ($key != '') {
            $this->_tplVars[$key] = $value;
        }
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\interfaces\View::__isset()
     */
    public function __isset($key)
    {
        return isset($this->_tplVars[$key]);
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\interfaces\View::__unset()
     */
    public function __unset($key)
    {
        if (isset($this->_tplVars[$key])) {
            unset($this->_tplVars[$key]);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\interfaces\View::assign()
     */
    public function assign($key, $value = null)
    {
        if (is_object($key)) {
            $key = method_exists($key, 'toArray') ? $key->toArray() : (array) $key;
            if (!is_array($key)) {
                throw new InvalidArgumentException(sprintf(
                    'View assign the argument key be an object but can not convert to an array, received "%s".', gettype($key)
                ));
            }
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                if ($k != '') {
                    $this->_tplVars[$k] = $v;
                }
            }
        }
        elseif ($key != '') {
            $this->_tplVars[$key] = $value;
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\interfaces\View::fetch()
     */
    public function fetch($tplName, $display = false)
    {
        $tplPath = $this->getViewDirectory() . DIRECTORY_SEPARATOR . $tplName . $this->tplExtension;
        if (is_file($tplPath)) {
            if ($display) {
                include $tplPath;
            }
            else {
                ob_start();
                ob_implicit_flush(false);
                include $tplPath;
                return ob_get_clean();
            }
        }
        else {
            throw new ErrorException(sprintf(
                'View tpl file "%s" is not a valid file.', $tplPath
            ));
        }
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\interfaces\View::display()
     */
    public function display($tplName)
    {
        $this->fetch($tplName, true);
    }

    /**
     * 模板文件所在的目录，包括风格目录
     * @return string
     */
    public function getViewDirectory()
    {
        if ($this->skinName !== '') {
            return $this->viewDirectory . DIRECTORY_SEPARATOR . $this->skinName;
        }

        return $this->viewDirectory;
    }

    /**
     * 渲染模板文件，如果有布局文件，依次渲染布局文件
     * @param string $tplName
     * @param array $data
     * @return \tfc\mvc\View
     */
    public function render($tplName, array $data = array())
    {
        $this->assign($data);
        if ($this->getLayoutCount() === 0) {
            $this->display($tplName);
            return $this;
        }
        $this->assign('layoutContent', $this->fetch($tplName));
        while ($this->getLayoutCount() > 0) {
            if ($this->isLastLayout()) {
                $this->display($this->fetchLayoutName());
                break;
            }

            $this->assign('layoutContent', $this->fetch($this->fetchLayoutName()));
        }

        return $this;
    }

    /**
     * 创建并请求widget类
     * @param string $className
     * @param array $tplVars
     * @param array $params
     * @param boolean $captureOutput
     * @return \tfc\mvc\Widget|string
     */
    public function widget($className, array $tplVars = array(), array $params = array(), $captureOutput = false)
    {
        $widget = $this->createWidget($className, $tplVars, $params);
        if ($captureOutput) {
            ob_start();
            ob_implicit_flush(false);
            $widget->run();
            return ob_get_clean();
        }
        else {
            $widget->run();
            return $widget;
        }
    }

    /**
     * 创建widget类
     * @param string $className
     * @param array $tplVars
     * @param array $params
     * @return \tfc\mvc\Widget
     * @throws ErrorException 如果widget类不存在，抛出异常
     * @throws ErrorException 如果widget类不是tfc\mvc\Widget的子类，抛出异常
     */
    public function createWidget($className, array $tplVars = array(), array $params = array())
    {
        $widget = str_replace('.', '\\', $className);
        if (!class_exists($widget)) {
            throw new ErrorException(sprintf(
                'View is unable to find the requested widget "%s".', $widget
            ));
        }

        $instance = new $widget($this, $tplVars, $params);
        if (!$instance instanceof Widget) {
            throw new ErrorException(sprintf(
                'View Class "%s" is not instanceof tfc\mvc\Widget.', $widget
            ));
        }

        return $instance;
    }

    /**
     * 弹出第一个模板布局名，指针指向下一个布局名
     * @return string|null
     */
    public function fetchLayoutName()
    {
        if ($this->_layoutNames === array()) {
            return null;
        }

        return array_pop($this->_layoutNames);
    }

    /**
     * 添加一个模板布局名，按添加的顺序，依次渲染模板，直到所有的布局都被渲染
     * @param string $layoutName
     * @return \tfc\mvc\View
     */
    public function addLayoutName($layoutName)
    {
        if (($layoutName = (string) $layoutName) !== '') {
            $this->_layoutNames[] = $layoutName;
        }

        return $this;
    }

    /**
     * 获取是否是最后一个需要渲染的布局
     * @return boolean
     */
    public function isLastLayout()
    {
        return ($this->getLayoutCount() === 1);
    }

    /**
     * 获取需要渲染的布局个数
     * @return integer
     */
    public function getLayoutCount()
    {
        return count($this->_layoutNames);
    }

    /**
     * 获取页面辅助类
     * @return \tfc\mvc\Html
     */
    public function getHtml()
    {
        if ($this->_html === null) {
            $this->_html = Singleton::getInstance('tfc\\mvc\\Html');
        }

        return $this->_html;
    }

    /**
     * 获取URL管理类
     * @return \tfc\mvc\UrlManager
     */
    public function getUrlManager()
    {
        if ($this->_urlManager === null) {
            $this->_urlManager = Singleton::getInstance('tfc\\mvc\\UrlManager');
        }

        return $this->_urlManager;
    }
}
