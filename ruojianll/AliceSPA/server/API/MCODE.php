<?php

include_once "common.php";
include_once "ICODE.php";
$app->post('/MCODE/generate',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    $res = array();
    $res = isICODERequestCorrect($app,$data,$res);
    if(!$res){
        $uti->addError(ERROR_ICODE_WRONG);
        return;
    }
    $res = $app->MCODE->sendACode($data->mobilephone,58);
    if($res != true){
        $uti->addError(ERROR_MCODE_SEND_FAIL);
        return;
    }
    $app->session->set('MCODE_mobilephone',$data->mobilephone);
    $uti->setSuccessTrue();
});
$app->post('/MCODE/check',function()use($app){
    $uti = $app->utility;
    $uti->setSuccessTrue();
    $data = getPostJsonObject();
    $uti->setItem('correct',$app->MCODE->isCorrect($data->MCODE));
});