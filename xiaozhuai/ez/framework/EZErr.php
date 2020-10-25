<?php

/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/19
 * Time: 下午5:58
 */
class EZErr
{
    public static function err($statusCode, $msg){
        EZRouter()->controllerName = "EZErr";
        EZRouter()->action = "index";
        EZRouter()->controllerPath = __DIR__ . "/EZErrController.php";
        if(isset(EZGlobal()->ERR_VIEW_PATH)){
            EZRouter()->viewPath = EZGlobal()->ERR_VIEW_PATH;   //use custom err page
        }else{
            EZRouter()->viewPath = __DIR__ . "/EZErr.phtml";    //use default err page
            EZConfig()->VIEW_ENGINE = "php";                    //force reset view engine to php
        }
        EZGlobal()->errStatusCode = $statusCode;
        EZGlobal()->errMsg = $msg;
        EZRouter()->runController();
        exit;
    }

    public static function errException($statusCode, &$errException){
        EZRouter()->controllerName = "EZErr";
        EZRouter()->action = "index";
        EZRouter()->controllerPath = __DIR__ . "/EZErrController.php";
        if(isset(EZGlobal()->ERR_VIEW_PATH)){
            EZRouter()->viewPath = EZGlobal()->ERR_VIEW_PATH;   //use custom err page
        }else{
            EZRouter()->viewPath = __DIR__ . "/EZErr.phtml";    //use default err page
            EZConfig()->VIEW_ENGINE = "php";                    //force reset view engine to php
        }
        EZGlobal()->errStatusCode = $statusCode;
        EZGlobal()->errMsg = $errException->getMessage();
        EZGlobal()->errException = $errException;
        EZRouter()->runController();
        exit;
    }
}