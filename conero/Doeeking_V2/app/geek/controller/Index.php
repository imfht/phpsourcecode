<?php
namespace app\geek\controller;
use think\Controller;
class Index extends Controller
{
    public function index()
    {
        geek_navBar($this->view,$this);
        $this->loadScript([
            'title'=>'Conero-技术交流','css'=>['index/index'],'js'=>['index/index'],'bootstrap'=>true
        ]);
        return $this->fetch();
    }
}
