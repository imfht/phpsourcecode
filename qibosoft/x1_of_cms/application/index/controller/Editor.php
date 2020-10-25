<?php
namespace app\index\controller;

use app\common\controller\IndexBase;

class Editor extends IndexBase
{
    public function index(){
        return $this->fetch();
    }
}

?>