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
namespace Tang\Web\View;
use Tang\Manager\Manager;
use Tang\Web\View\Templates\Html;
use Tang\Web\View\Templates\Xml;
use Tang\Web\View\Templates\Json;
use Tang\Web\View\Templates\Yaml;

class TemplateManager extends Manager
{
	protected $driverNames = 'json|xml|yaml|';
    public function createHtmlDriver()
    {
        return new Html();
    }
    public function createJsonDriver()
    {
        return new Json();
    }
    public function createYamlDriver()
    {
        return new Yaml();
    }
    public function createXmlDriver()
    {
        return new Xml();
    }

    protected function createDriver($name)
    {
        if(strpos($this->driverNames,$name) === false)
        {
            $name = 'html';
        }
        return parent::createDriver($name);
    }
    protected function getIntreface()
    {
        return '\Tang\Web\View\Template\ITemplate';
    }
}