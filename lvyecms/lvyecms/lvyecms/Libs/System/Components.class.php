<?php

// +----------------------------------------------------------------------
// | LvyeCMS Components
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.lvyecms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 旅烨集团 <web@alvye.cn>
// +----------------------------------------------------------------------

namespace Libs\System;

class Components {

    static private $_components = array(
        'Url' => array(
            'class' => '\\Libs\\System\\Url',
            'path' => 'Libs.System.Url',
        ),
        'Cloud' => array(
            'class' => '\\Libs\\System\\Cloud',
            'path' => 'Libs.System.Cloud',
        ),
        'CloudDownload' => array(
            'class' => '\\Libs\\System\\CloudDownload',
            'path' => 'Libs.System.CloudDownload',
        ),
        'Html' => array(
            'class' => '\\Libs\\System\\Html',
            'path' => 'Libs.System.Html',
        ),
        'UploadFile' => array(
            'class' => '\\UploadFile',
        ),
        'Dir' => array(
            'class' => '\\Dir',
            'path' => 'Libs.Util.Dir',
        ),
        'Content' => array(
            'class' => '\\Libs\\System\\Content',
            'path' => 'Libs.System.Content',
        ),
        'ContentOutput' => array(
            'class' => '\\content_output',
        ),
    );

    public function __construct($_components = array()) {
        if (!empty($_components)) {
            $this->setComponents($_components);
        } else {
            $this->setComponents(C('components', NULL, array()));
        }
    }

    public function __get($name) {
        if (isset(self::$_components[$name])) {
            $components = self::$_components[$name];
            if (!empty($components['class'])) {
                $class = $components['class'];
                if ($components['path'] && !class_exists($class, false)) {
                    import($components['path'], PROJECT_PATH);
                }
                unset($components['class'], $components['path']);
                $this->$name = \Think\Think::instance($class);
                return $this->$name;
            }
        }
    }

    /**
     * 连接
     * @access public
     * @param array $_components  配置数组
     * @return void
     */
    static public function getInstance($_components = array()) {
        static $systemHandier;
        if (empty($systemHandier)) {
            $systemHandier = new Components($_components);
        }
        return $systemHandier;
    }

    /**
     * 设置$_components
     * @param type $_components
     */
    public function setComponents($_components = array()) {
        self::$_components = array_merge(self::$_components, $_components);
    }

}
