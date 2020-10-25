<?php

namespace Apphook;

use SCH60\Kernel\App;
use Common\AppCustomHelper;
use SCH60\Kernel\KernelHelper;

class InitControllerIntro{
    
    public function run(){
        $hookCommon = KernelHelper::getInstance("Apphook\Common");
        $hookCommon->disableSearchRobot();
        $hookCommon->check_post_referer();
    }
    
}