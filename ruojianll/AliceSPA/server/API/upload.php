<?php

require_once "common.php";

//api
function checkUploadPermission_normal($user){
    return hasPermission($user,PERMISSION_USER);
}
$app->post('/upload/image',function() use($app,$config){

    $uti = $app->utility;
    $user = getCurrentUser($app);

    if(!checkUploadPermission_normal($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }

    $res = $app->UFM->upload($app,APP_TYPE_IMAGE,$user['id'],$config->application->imgUrl,!isManager($user));

    if(isset($res)){
        $uti->setSuccessTrue();
        $uti->setItem('files',$res);
    }
    else{
        $uti->addError(ERROR_EXECUTE_FAIL);
    }
});