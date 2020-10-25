<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use app\admin\builder\AdminListBuilder;
use app\admin\builder\AdminSortBuilder;
use app\admin\builder\AdminConfigBuilder;


class Appcloud extends Admin
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index(){

    	$this->error('应用商店将采用接口调用云端数据，该版暂未提供');
    	$this->setTitle('应用商店');
        return $this->fetch();
    }
} 