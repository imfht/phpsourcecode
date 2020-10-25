<?php
namespace plugins\marketing\index;

use app\common\controller\IndexBase;


class Index extends IndexBase
{
    public function index(){
        return $this->fetch();
    }
}
