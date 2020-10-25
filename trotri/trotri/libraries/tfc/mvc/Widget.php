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
use tfc\ap\ErrorException;
use tfc\ap\InvalidArgumentException;

/**
 * Widget abstract class file
 * 页面装饰基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Widget.php 1 2013-04-16 20:00:06Z huan.song $
 * @package tfc.mvc
 * @since 1.0
 */
abstract class Widget implements interfaces\View
{
    /**
     * @var instance of tfc\mvc\View
     */
    protected $_view = null;

    /**
     * @var array 用于寄存渲染模板的变量名和值
     */
    protected $_tplVars = array();

    /**
     * @var array 用于模板处理的参数，可以寄存CSS名、JS名和模板文件名
     */
    protected $_params = array();

    /**
     * @var string 页面装饰模板所在的目录
     */
    protected $_widgetDirectory;

    /**
     * @var string 当没有指定CSS名、JS名和模板文件名时，默认的名称
     */
    protected $_defaultName = 'default';

    /**
     * 构造方法：初始化页面解析类、渲染模板的变量名和值、用于模板处理的参数
     * @param \tfc\mvc\View $view
     * @param array $tplVars
     * @param array $params
     */
    public function __construct(View $view, array $tplVars = array(), array $params = array())
    {
        $this->_view = $view;
        $this->_tplVars = $tplVars;
        $this->_params = $params;
        $this->_init();
    }

    /**
     * 子类构造方法：子类调用此方法作为构造方法，避免重写父类构造方法
     */
    protected function _init()
    {
    }

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
                    'Widget assign the argument key be an object but can not convert to an array, received "%s".', gettype($key)
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
    public function fetch($tplName = null, $display = false)
    {
        if ($tplName === null) {
            $tplName = $this->getTplName();
        }
        $tplPath = $this->getWidgetDirectory() . DIRECTORY_SEPARATOR . $tplName;
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
                'Widget tpl file "%s" is not a valid file.', $tplPath
            ));
        }
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\interfaces\View::display()
     */
    public function display($tplName = null)
    {
        if ($tplName === null) {
            $tplName = $this->getTplName();
        }

        $this->fetch($tplName, true);
    }

    /**
     * 获取页面解析类
     * @return \tfc\mvc\View
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * 获取页面辅助类
     * @return \tfc\mvc\Html
     */
    public function getHtml()
    {
        return $this->getView()->getHtml();
    }

    /**
     * 获取URL管理类
     * @return \tfc\mvc\UrlManager
     */
    public function getUrlManager()
    {
        return $this->getView()->getUrlManager();
    }

    /**
     * 获取JavaScript文件名，默认文件名：default.js
     * @return string
     */
    public function getJsName()
    {
        $jsName = isset($this->_params['jsName']) ? $this->_params['jsName'] : $this->_defaultName;
        return $jsName . '.js';
    }

    /**
     * 获取Css文件名，默认文件名：default.css
     * @return string
     */
    public function getCssName()
    {
        $cssName = isset($this->_params['cssName']) ? $this->_params['cssName'] : $this->_defaultName;
        return $cssName . '.css';
    }

    /**
     * 获取模板文件名，默认文件名：default.php
     * @return string
     */
    public function getTplName()
    {
        $tplName = isset($this->_params['tplName']) ? $this->_params['tplName'] : $this->_defaultName;
        return $tplName . $this->getView()->tplExtension;
    }

    /**
     * 获取页面装饰模板所在的目录，默认目录：{viewDirectory}/{className}
     * @return string
     * @throws ErrorException 如果页面装饰模板所在的目录不存在，抛出异常
     */
    public function getWidgetDirectory()
    {
        if ($this->_widgetDirectory !== null) {
            return $this->_widgetDirectory;
        }

        $className = str_replace('\\', DS, strstr(strtolower(get_class($this)), 'widgets' . '\\'));
        $this->_widgetDirectory = $this->getView()->viewDirectory . DIRECTORY_SEPARATOR . $this->getView()->skinName . DIRECTORY_SEPARATOR . $className;
        if (is_dir($this->_widgetDirectory)) {
            return $this->_widgetDirectory;
        }
        else {
            throw new ErrorException(sprintf(
                'View widgets directory "%s" is not a valid directory.', $this->_widgetDirectory
            ));
        }
    }

    /**
     * 魔术方法：执行Widget类，输出内容
     * @return string
     */
    public function __toString()
    {
        return $this->run();
    }

    /**
     * 执行Widget类，输出内容
     * @return void
     */
    abstract protected function run();
}
