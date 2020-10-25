<?php
namespace app\admin\controller;

use think\Request;

class IndexController extends AdminBaseController
{
    public function index()
    {
        return $this->fetch();
    }

    public function console()
    {
        exec('ps aux | grep jtimer',$status);
        $status = array_filter($status,function($val){
            if(strpos($val,'grep') !== false){
                return false;
            }
            return true;
        });
        $result = empty($status) ? '进程未启动，请使用 php think jtimer start 启动' : implode("<br>",$status);
        $this->assign('status',$result);
        return $this->fetch();
    }


}
