<?php

/**
 * View
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;
use Madphp\View\Layout;
use Madphp\View\Compiler;
use Madphp\View\Util as ViewUtil;

class View
{
    public $viewFile;

    public $isLayoutFile;

    public $viewName;

    public $layout;

    public $compiler;

    public $data = array();

    public $isLayout = true;

    public $isCompiler = true;

    public $viewFolder;

    public $viewPath;
    
    public function __construct($viewName, $isLayoutFile = false)
    {
        if(!$viewName) {
            throw new \InvalidArgumentException("View name can not be empty!");
        } else {

            $this->viewName = $viewName;
            $this->viewPath = defined('VIEW_PATH') ? VIEW_PATH : dirname($_SERVER['SCRIPT_FILENAME']) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
            $this->viewFolder = basename($this->viewPath);
            $this->isLayoutFile = $isLayoutFile;
            if ($isLayoutFile === true) {
                $this->isLayout(false);
            }

            $this->layout = new Layout();
            $this->compiler = new Compiler();

            $viewFile = ViewUtil::getFilePath($this);
            if (!is_file($viewFile)) {
                throw new \UnexpectedValueException("View file does not exist!");
            }

            $this->viewFile = $viewFile;
        }
    }

    /**
     * 获取视图对象
     * @param null $viewName
     * @return View
     */
    public static function make($viewName = null, $isLayoutFile = false)
    {
        return new self($viewName, $isLayoutFile);
    }

    /**
     * 添加变量
     */
    public function with($key, $value = null)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * 获取输出内容
     */
    public function complete()
    {
        return ViewUtil::render($this);
    }

    /**
     * 输出内容
     */
    public function show()
    {
        $output = ViewUtil::render($this);
        try {
            echo $output;
//            Response::setBody($output);
//            $ret = Response::send();
        } catch (\Exception $e) {
            throw new \Exception("View output error!");
        }
        return true;
    }

    /**
     * 是否启用Layout
     */
    public function isLayout($isLayout, $object = null)
    {
        if (is_object($object) && ($object instanceof View)) {
            $object->isLayout = boolval($isLayout);
        } else {
            $this->isLayout = boolval($isLayout);
        }
    }

    /**
     * 设置布局文件
     */
    public function setLayout($layoutName = null, $object = null)
    {
        if (!is_object($object) or !($object instanceof View)) {
            $object = $this;
        }

        if ($object->isLayout) {
            $object->layout->setLayout(strval($layoutName));
        }
    }
    
    /**
     * 是否启用模板引擎
     */
    public function isCompiler($isCompiler, $object = null)
    {
        if (is_object($object) && ($object instanceof View)) {
            $object->isCompiler = boolval($isCompiler);
        } else {
            $this->isCompiler = boolval($isCompiler);
        }
    }

    /**
     * 设置模板引擎
     */
    public function setCompiler($compilerEngineName = null, $object = null)
    {
        if (!is_object($object) or !($object instanceof View)) {
            $object = $this;
        }

        if ($object->isCompiler) {
            $object->compiler->setCompiler(strval($compilerEngineName));
        }
    }

    /**
     * 用于加载局部模板文件
     * 获取渲染模板文件的内容
     */
    public static function fetch($template, $data = null)
    {
        $object = self::make($template);
        $object->isLayout(false);
        return ViewUtil::render($object, $data);
    }

    public function __call($method, $parameters)
    {
        if (starts_with($method, 'with')) {
            return $this->with(snake_case(substr($method, 4)), $parameters[0]);
        }

        throw new \BadMethodCallException("Function [$method] does not exist!");
    }
}