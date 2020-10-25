<?php

namespace controller\api;

use onefox\Controller;

class Token extends Controller  {
    
    public function indexAction(){
        $param = $this->get('test');
        if (!$param) {
            $this->json(self::CODE_FAIL, 'error');
        }
        $this->json(self::CODE_SUCCESS, 'ok', ['test'=>$param]);
    }
    
}

