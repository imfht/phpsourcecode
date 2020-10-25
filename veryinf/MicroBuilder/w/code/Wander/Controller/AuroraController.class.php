<?php
/**
 * 欢迎及宣传页面
 */
namespace Wander\Controller;
use Think\Controller;
class AuroraController extends Controller {
    public function indexAction(){
        $this->display('Wander/aurora');
    }
}