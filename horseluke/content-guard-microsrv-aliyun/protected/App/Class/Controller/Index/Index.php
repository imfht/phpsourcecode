<?php

namespace Controller\Index;

use SCH60\Kernel\BaseController;

class Index extends BaseController{
    
    protected $layout = "layout_default";
    
    public function actionIndex(){
        return $this->render("", array('title' => '欢迎', 'parentTitle' => '后台欢迎页'));
    }
    
}