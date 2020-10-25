<?php
namespace app\finance\controller;
use think\Controller;
use app\Server\Finance;
class Tool extends Controller
{
    public function index()
    {
        $this->loadScript([
            'auth'=>'','title'=>'财务工具 - Conero','bootstrap'=>true,'js'=>['tool/index']
        ]);
        return $this->fetch();
    }
}