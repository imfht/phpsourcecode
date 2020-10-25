<?php

namespace Controller\Index;

use SCH60\Kernel\BaseController;

class Ajax extends BaseController{
    
    public function actionPing(){
        return $this->response->json(true);
    }
    
}