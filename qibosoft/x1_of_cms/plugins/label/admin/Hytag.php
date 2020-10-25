<?php
namespace plugins\label\admin;

use app\index\model\Labelhy AS Model;

class Hytag extends Index
{
    protected $hytag = true;
	
	protected function _initialize()
    {
		parent::_initialize();
		
		$this->model = new Model();
	}
	


}
