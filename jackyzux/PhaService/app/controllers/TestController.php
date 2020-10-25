<?php

class TestController extends ControllerBase
{
    public function initialize(){
        $this->view->setRenderLevel(Phalcon\MVC\View::LEVEL_NO_RENDER);
    }//end

    public function indexAction()
    {
        echo 'HELLO'.PHP_EOL;
    }

}

