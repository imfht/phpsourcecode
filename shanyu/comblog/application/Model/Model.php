<?php
namespace App\Model;

use Kernel\Db;

abstract class Model
{
    public function __construct()
    {
        // 初始化
        $this->_initialize();
    }
    public function _initialize(){
        
    }
    public function db()
    {
        return Db::instance();
    }
    public function close()
    {
        return Db::instance()->close();
    }
    public function sqls()
    {
        return Db::instance()->getSqls();
    }
}