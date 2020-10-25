<?php

namespace ControllerIntro\Index;

use SCH60\Kernel\BaseController;

class Index extends BaseController{
    
    protected $layout = "layout_default";
    
    public function actionIndex(){
        return $this->render("", array('title' => '作品介绍：基于阿里云安全接口的SDK及内容安全检测微服务', 'parentTitle' => '参赛说明'));
    }
    
}