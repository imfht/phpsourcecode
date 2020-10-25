<?php
namespace app\demo\controller;

use think\Db;
use think\Controller;
use app\common\controller\Common;

class Control extends Common
{
	public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 空方法直接调用方法模板
     * @return [type] [description]
     */
    public function _empty(){     
    	//获取方法名称
        $action = request()->action();

        return $this->fetch($action);
	}
}