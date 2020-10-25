<?php
namespace App\Controller;

use Kernel\View;
use Kernel\Loader;

abstract class Controller
{
    protected $SEO=[];
    public function __construct()
    {
        // 控制器初始化
        $this->_initialize();
    }
    public function _initialize()
    {
        $SEO_TITLE = getenv('SEO_TITLE');
        $SEO_KEYWORDS = getenv('SEO_KEYWORDS');
        $SEO_DESCRIPTION = getenv('SEO_DESCRIPTION');
        $_scripts=$_styles = '';
        $this->assign(compact('SEO_TITLE','SEO_KEYWORDS','SEO_DESCRIPTION','_scripts','_styles'));
    }

    public function assign($name='',$value=[])
    {
        return View::instance()->assign($name,$value);
    }
    public function fetch($template)
    {
        return View::instance()->fetch($template);
    }

    public function success($message,$redirect='javascript:history.back(-1);',$wait=3)
    {
        return $this->jump(1,$message,$redirect,$wait);
    }
    public function error($message,$redirect='javascript:history.back(-1);',$wait=3)
    {
        return $this->jump(0,$message,$redirect,$wait);
    }
    protected function jump($status,$message,$redirect,$wait)
    {
        return View::instance()->assign('status',$status)->assign('message',$message)->assign('redirect',$redirect)->assign('wait',$wait)->fetch('public_jump');
    }
    

}