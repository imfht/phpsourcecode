<?php

/**
 * 当有未捕获的异常, 则控制流会流到这里
 */
class ErrorController extends \Our\Controller_Abstract {

    public function init() {
        \Yaf\Dispatcher::getInstance()->disableView();
    }

    public function errorAction($exception) {
        if ($exception->getCode() > 100000) {
            //这里可以捕获到应用内抛出的异常
            echo $exception->getCode();
            echo $exception->getMessage();
            return;
        }
        switch ($exception->getCode()) {
            case 404://404
            case 515:
            case 516:
            case 517:
                //输出404
                header(\Our\Common::getHttpStatusCode(404));
                echo '404';
                exit();
                break;
            default :
                break;
        }
        throw $exception;
    }

}
