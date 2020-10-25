<?php

/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/19
 * Time: 下午7:28
 */
class EZErrController extends EZController
{
    public function index(){
        switch (EZGlobal()->errStatusCode){
            case 403:
                header('HTTP/1.1 403 Forbidden');
                break;
            case 404:
                header('HTTP/1.1 404 Not Found');
                break;
            case 500:
            default:
                header('HTTP/1.1 500 Internal Server Error');
                break;
        }
        try {
            if(isset(EZGlobal()->errException)){
                throw EZGlobal()->errException;
            }else{
                throw new Exception(EZGlobal()->errMsg);
            }
        } catch (Exception $e) {
            $this->getView()->errStatusCode = EZGlobal()->errStatusCode;
            $this->getView()->errMsg = str_replace("\n", "<br>", (EZGlobal()->errMsg . "\n" . $e->getTraceAsString()));
            $this->getView()->render();
        }
    }
}