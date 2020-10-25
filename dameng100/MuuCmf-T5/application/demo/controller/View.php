<?php
namespace app\demo\controller;

use think\Db;
use think\Controller;
use app\common\controller\Common;

class View extends Common
{
	/**
     * 空方法直接调用方法模板
     * @return [type] [description]
     */
    public function _empty(){     
    	//获取方法名称
        $action = request()->action();

        return $this->fetch($action);
	}

	public function remote_tab()
	{
		echo 'remote_tab';
	}

	public function iframe_tab()
	{
		echo 'iframe_tab';
	}
}