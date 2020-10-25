<?php
namespace app\index\controller;

use app\common\controller\IndexBase;

//小工具
class Tool extends IndexBase
{
    public function index()
    {
    }
    
    public function md5($pwd=''){
        if (empty($this->admin)) {
            $this->error('你没权限');
        }elseif ($pwd==''){
            $this->error('pwd参数不能为空!');
        }
        echo mymd5($pwd);
    }
    
}

