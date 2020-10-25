<?php
namespace Core;

use Core\Config;
use Phalcon\Exception;
use Phalcon\Translate\Adapter\NativeArray;

class Translate extends NativeArray
{
    protected $default = false;
    protected $languages;
    protected $language = false;
    protected $settings;
    protected $bestLanguage = false;

    public function __construct($options = false)
    {
        if (!is_array($options)) {
            $options = array();
        }
        if (!isset($options['content']) || !is_array($options['content'])) {
            $options['content'] = array();
        }
        $this->settings = Config::get('translate');
        $this->languages = $this->settings['translate_language'];
        $this->default = $this->settings['default'];
        parent::__construct($options);
    }

    public function addTranslate($content)
    {
        if (is_string($content)) {
            $content = $this->get($content);
        }
        if (!$content || !is_array($content)) {
            return false;
        }
        if (!is_array($this->_translate)) {
            $this->_translate = array();
        }
        $this->_translate = array_merge($this->_translate, $content);
    }

    public function query($index, $placeholders = null)
    {
        if (!$this->_translate) {
            $this->initTranslate();
        }
        return parent::query($index, $placeholders);
    }

    protected function initTranslate()
    {
        $content = $this->get($this->getBestLanguage());
        $this->addTranslate($content);
        global $di;
        $moduleName = $di->getShared('router')->getModuleName();
        $this->addTranslate($moduleName . '.' . $this->getBestLanguage());
        $this->addTranslate('m'.$moduleName . '.' . $this->getBestLanguage());
        $controllerName = $di->getShared('router')->getControllerName();
        $themes = Config::get('themes');
        if (isset($themes[$controllerName])) {
            $this->addTranslate('themes.' . $themes[$controllerName] . '.' . $this->getBestLanguage());
        }
        //$this->addTranslate('themes.Default.'.$this->getBestLanguage());
    }

    public function get($name)
    {
        $nameInfo = explode('.', $name);
        switch (count($nameInfo)) {
            case 1:
                $file = __DIR__ . '/Translate/' . $this->getBestLanguage() . '.php';
                break;
            case 2:
                $file = MODULES_DIR . $nameInfo[0] . '/translate/' . $nameInfo[1] . '.php';
                break;
            case 3:
                if ($name[0] == 't') {
                    $file = THEMES_DIR . $nameInfo[1] . '/translate/' . $nameInfo[2] . '.php';
                } else {
                    $file = ROOT_DIR . 'web/' . WEB_CODE . '/modules/' . $nameInfo[1] . '/translate/' . $nameInfo[2] . '.php';
                }
                break;
        }
        if (file_exists($file)) {
            include $file;
            if (isset($settings) && is_array($settings)) {
                return $settings;
            }
        }
    }

    public function getLanguage()
    {
        if ($this->language) {
            return $this->language;
        }
        $language = false;
        switch ($this->settings['translate_type']) {
            case 1:
                $serverNameInfo = explode('.', $_SERVER['SERVER_NAME']);
                $language = $serverNameInfo[0];
            case 2:
                global $di;
                $routerParams = $di->getShared('router')->getParams();
                if (isset($routerParams['language'])) {
                    $language = $routerParams['language'];
                }
                break;
            case 3:
                global $di;
                if ($di->getShared('request')->has('language')) {
                    $language = $di->getShared('request')->getQuery('language');
                }
                break;
            case 4:
                global $di;
                if ($di->getShared('cookies')->has('remember-me')) {
                    // 获取cookie
                    $rememberMe = $di->getShared('cookies')->get('remember-me');
                    // 获取cookie的值
                    $language = $rememberMe->getValue();
                }
                break;
        }
        if ($language) {
            $language = substr($language, 0, 2);
            if (isset($this->languages[$language])) {
                $this->language = $language;
                return $this->language;
            }
        }
        return false;
    }

    public function getBestLanguage()
    {
        if ($this->getLanguage()) {
            return $this->language;
        }
        if ($this->bestLanguage) {
            return $this->bestLanguage;
        }
        global $di;
        $language = $di->getShared('request')->getBestLanguage();
        $language = substr($language, 0, 1);
        if (!isset($this->languages[$language])) {
            $language = $this->default;
        }
        $this->bestLanguage = $language;
        return $language;
    }

    public function getDefault()
    {
        return $this->default;
    }
}
