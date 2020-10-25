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
namespace Tang\Web;
class Parameters
{
	protected $module;
	protected $controller;
	protected $action;
	protected $viewType;
	protected $type;
	public function __construct($module,$controller,$action,$type='web',$viewType = 'html')
	{
		$this->module = $module;
		$this->controller = $controller;
		$this->action = $action;
		$this->viewType = $viewType;
		$this->type = $type;
	}
	public function setViewType($viewType)
	{
		$this->viewType = $viewType;
	}
	public function __get($name)
	{
		return $this->{$name};
	}
}