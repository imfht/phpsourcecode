<?php


$app->get('/message/test',function()use($app){
});

//$app->get('/message/overage',function()use($app){
//    $res = $app->Message->overage();
//    $res = json_encode($res);
//    echo '<script>document.write(\''.$res.'\')</script>';
//});
//$app->get('/message/check/{code:[0-9]+}',function($code)use($app){
//    if($app->MCODE->isCorrect($code)){
//        echo '1';
//    }else{
//        echo '2';
//    }
//});
//$app->get('/message/time',function(){
//   echo time();
//});