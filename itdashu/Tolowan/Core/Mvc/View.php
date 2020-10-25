<?php
namespace Core\Mvc;

use Core\Config;
use Exception;
use Phalcon\Mvc\View as Pview;

class View extends Pview
{

    protected $_module = false;

    protected $_controllerName;

    protected $_currentModule = false;

    protected $_actionName;

    protected $_themeName;

    protected $_rquestParams;

    protected $_isAjax;

    public function __construct($options = null)
    {
        $this->setViewsDir(__DIR__ . '/../../');
        $this->registerEngines(array(
            ".twig" => 'volt',
        ));
        parent::__construct($options);
    }

    public function setModuleName($name)
    {
        $this->_module = $name;
    }

    public function init()
    {
        $this->_isAjax = $this->getDI()
            ->getRequest()
            ->isAjax();
        $this->_module = $this->getDI()
            ->getRouter()
            ->getModuleName();
        $this->_controllerName = $this->getDI()
            ->getRouter()
            ->getControllerName();
        $this->_actionName = $this->getDI()
            ->getRouter()
            ->getActionName();
        $this->_requestParams = $this->getDI()
            ->getRouter()
            ->getParams();
        $themes = Config::get('themes');
        if (isset($themes[$this->_controllerName])) {
            $this->_themeName = $themes[$this->_controllerName];
        } elseif (isset($themes['default'])) {
            $this->_themeName = $themes['default'];
        } else {
            $this->_themeName = 'Default';
        }
    }

    public function getContent()
    {
        global $di;
        $output = parent::getContent();
        $output .= $di->getShared('flashSession')->output();
        return $output;
    }

    public function getActiveTemplate($templates, $module = null)
    {
        if(is_string($templates)){
            $templates = [$templates];
        }
        foreach ($templates as $template) {
            if ($this->_isAjax === true) {
                if ($this->exists('Themes/' . $this->_themeName . '/templates/ajax/' . $template)) {
                    return 'Themes/' . $this->_themeName . '/templates/ajax/' . $template;
                }
            }
            //echo 'Themes/' . $this->_themeName . '/templates/' . $template.'<br />';
            if ($this->exists('Themes/' . $this->_themeName . '/templates/' . $template)) {
                return 'Themes/' . $this->_themeName . '/templates/' . $template;
            }
            if ($module) {
                if ($this->_isAjax === true) {
                    if ($this->exists('Modules/' . ucfirst($module) . '/templates/ajax/' . $template)) {
                        return 'Modules/' . ucfirst($module) . '/templates/ajax/' . $template;
                    }
                }
                if ($this->exists('Modules/' . ucfirst($module) . '/templates/' . $template)) {
                    return 'Modules/' . ucfirst($module) . '/templates/' . $template;
                }
            }
            //echo 'Modules/' . ucfirst($module) . '/templates/ajax/' . $template.'<br />';
            if ($this->_currentModule) {
                if ($this->_isAjax === true) {
                    if ($this->exists('Modules/' . ucfirst($this->_currentModule) . '/templates/ajax/' . $template)) {
                        return 'Modules/' . ucfirst($this->_currentModule) . '/templates/ajax/' . $template;
                    }
                }
                if ($this->exists('Modules/' . ucfirst($this->_currentModule) . '/templates/' . $template)) {
                    return 'Modules/' . ucfirst($this->_currentModule) . '/templates/' . $template;
                }
            }
            if ($this->_module) {
                if ($this->_isAjax === true) {
                    if ($this->exists('Modules/' . ucfirst($this->_module) . '/templates/ajax/' . $template)) {
                        return 'Modules/' . ucfirst($this->_module) . '/templates/ajax/' . $template;
                    }
                }
                if ($this->exists('Modules/' . ucfirst($this->_module) . '/templates/' . $template)) {
                    return 'Modules/' . ucfirst($this->_module) . '/templates/' . $template;
                }
            }
            if ($this->_isAjax === true) {
                if ($this->exists('Themes/Default/templates/ajax/' . $template)) {
                    return 'Themes/Default/templates/ajax/' . $template;
                }
            }
            if ($this->exists('Themes/Default/templates/' . $template)) {
                return 'Themes/Default/templates/' . $template;
            }
        }
        throw new Exception('模板文件不存在：' . $template);
    }

    public function setBaseTemplate($templates = ['page'])
    {
        if(is_string($templates)){
            $templates = [$templates];
        }
        $templates = $this->setTemplateName($templates);
        $template = $this->getActiveTemplate($templates);
        // echo $template.'<br>';
        $this->pick($template);
    }

    public function setTemplateName($name)
    {
        if (is_array($name)) {
            $defaultSchema = $name;
        } elseif (is_string($name)) {
            $defaultSchema = array(
                $name,
            );
        }
        $urlSchema = $defaultSchema;
        $endName = end($urlSchema);
        foreach ($this->_requestParams as $pk => $pv) {
            $endName = $endName . '-' . $pv;
            $urlSchema[] = $endName;
        }
        $urlSchema = array_reverse($urlSchema);
        return $urlSchema;
    }

    public function r($variables)
    {
        $themeFunction = Config::cache('themeFunction');
        // 直接返回字符串
        if (is_object($variables) && method_exists($variables, 'render')) {
            $variables = $variables->render();
        }
        if (is_string($variables)) {
            return $variables;
        }
        if (is_array($variables)) {
            // 返回模板render
            if (isset($variables['#templates']) && !empty($variables['#templates'])) {
                $variables['#templates'] = $this->setTemplateName($variables['#templates']);
                if (isset($variables['#module'])) {
                    $template = $this->getActiveTemplate($variables['#templates'], $variables['#module']);
                } else {
                    $template = $this->getActiveTemplate($variables['#templates']);
                }
                //echo $template.'<br />';
                return $this->partial($template, $variables);
            } elseif (isset($variables['#function']) && !empty($variables['#function'])) {
                // 返回函数渲染数据
                if (isset($themeFunction[$variables['#function']])) {
                    return call_user_func($variables['#function'], $variables);
                }
            } else {
                $output = '';
                foreach ($variables as $value) {
                    $output .= $this->r($value);
                }
                return $output;
            }
        }
        return '';
    }
}
