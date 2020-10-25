<?php
namespace app\index\controller;

use app\common\controller\IndexBase;


class Msg extends IndexBase
{
    public function index()
    {
        if (in_weixin()&&empty($this->user)) {
            $this->error('请先登录!');
        }
		return $this->fetch('index');
    }
    
    public function layim()
    {
        return $this->fetch('layim');
    }
    
    
}

