<?php

function isICODERequestCorrect($app,$obj , &$res , $clear = true){
    if(isset($obj->ICODE)){
        if($app->ICODE->check($obj->ICODE)){
            return true;
        };
    }
    return false;
}

//api
$app->get('/ICODE/generate',function() use($app) {
    $app->ICODE->show();session_start();
});
//api
$app->post('/ICODE/check',function() use($app) {
    $uti = $app->utility;
    $info = getPostJsonObject();
    if(!isset($info)){
        $uti->addError(ERROR_JSON_INVILID);
        return;
    }
    $uti->setSuccessTrue();
    $uti->setItem('correct',$app->ICODE->check($info->ICODE,false));

});


