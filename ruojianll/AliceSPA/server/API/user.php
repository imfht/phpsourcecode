<?php

include_once "ICODE.php";
function getUserNameById($uid){
    $user = RjUser::findFirst(array('id=:id:','bind'=>array(
        'id' => $uid
    )));
    if($user == false){
        return null;
    }
    return $user->name;
}
function setUserSession($app,$id,$name,$mobilephone,$e_mail,$permission){
    $app->session->set('user_id' , $id);
    $app->session->set('user_name' , $name);
    $app->session->set('user_mobilephone' , $mobilephone);
    $app->session->set('user_e_mail' , $e_mail);
    $app->session->set('user_permission' , $permission);
}
//api
$app->post('/user/isExist',function()use($app){
    $uti = $app->utility;
    $info = getPostJsonObject();
    if(!isset($info)){
        $uti->addError(ERROR_JSON_INVILID);
        return;
    }
    if(!isset($info->field) || !isset($info->value)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $str = '=:tag:';
    if($info->field=='name'){
        $str='name'.$str;
    }
    if($info->field=='mobilephone'){
        $str='mobilephone'.$str;
    }
    $res = RjUser::findFirst(array($str,'bind'=>array(
        'tag' => $info->value
    )));
    $uti->setItem('isExist',$res==false?false:true);
    $uti->setSuccessTrue();

});
$app->post('/user/register',function() use($app) {
    $uti = $app->utility;
    $info = getPostJsonObject();
    if(!isset($info)){
        $uti->addError(ERROR_JSON_INVILID);
        return;
    }

    if(!isset($info->password) || !isset($info->mobilephone) || !isset($info->MCODE)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    if(!$app->session->has('MCODE_mobilephone')||$app->session->get('MCODE_mobilephone')!=$info->mobilephone){
        $uti->addError(ERROR_NO_MCODE_PHONE_IN_SERVER);
        return;
    }
    if(!isset($info->e_mail)){
        $info->e_mail = 'notset';
    }
    if(!$app->MCODE->isCorrect($info->MCODE,true)){
        $uti->addError(ERROR_MCODE_WRONG);
        return;
    }

    $info->e_mail = isset($info->e_mail)?$info->e_mail:null;
    $phql = "INSERT INTO RjUser (name,password,mobilephone,e_mail,permission,create_date) VALUES (:name:,:password:,:mobilephone:,:e_mail:,:permission:,:cd:)";
    $status = $app->modelsManager->executeQuery($phql,array(
        'name' => $info->name,
        'password' => $info->password,
        'mobilephone' => $info->mobilephone,
        'e_mail' => $info->e_mail,
        'permission' => PERMISSION_USER,
        'cd' => getMysqlDateTimeNow()
    ));

    if($status->success() == true){
        $uti->setSuccessTrue();
        $phql = "SELECT RjUser.id FROM RjUser WHERE RjUser.name = :name:";
        $ids = $app->modelsManager->executeQuery($phql,array(
            'name' => $info->name
        ));
        foreach($ids as $id){
            setUserSession($app,$id->id,$info->name,$info->mobilephone,$info->e_mail,PERMISSION_USER);
            break;
        }
    }
    else{
        foreach($status->getMessages() as $msg){
             echo $msg->getMessage();
        }
        $uti->setSuccessFalse();
        $uti->addError(ERROR_EXECUTE_FAIL);
    }
});
//api
$app->post('/user/login',function() use($app) {
    $uti = $app->utility;
    $info = getPostJsonObject();
    if(!isset($info)){
        $uti->addError(ERROR_JSON_INVILID);
        return;
    }
    $res =  array();
    $res = isICODERequestCorrect($app,$info,$res);
    if(!$res){
        $uti->addError(ERROR_ICODE_WRONG);
        return;
    };

    $phql = "SELECT * FROM RjUser WHERE RjUser.password = :password: AND ";
    $key = null;
    $value = null;
    if(isset($info->name)){
        $phql = $phql . "RjUser.name = :name:";
        $key = "name";
        $value = $info->name;
    }
    else if(isset($info->mobilephone)){
        $phql = $phql . "RjUser.mobilephone = :mobilephone:";
        $key = "mobilephone";
        $value = $info->mobilephone;
    }
    else if(isset($info->e_mail)){
        $phql = $phql . "RjUser.e_mail = :e_mail:";
        $key = "e_mail";
        $value = $info->e_mail;
    }
    else{
        $res['success'] = false;
        echo json_encode($res);
        return;
    }

    $users = $app->modelsManager->executeQuery($phql,array(
        'password' => $info->password,
        $key => $value
    ));


    $data = array();
    foreach($users as $user){
        $data['name'] = $user->name;
        $data['mobilephone'] = $user->mobilephone;
        $data['e_mail'] = $user->e_mail;
        $uti->setSuccessTrue();
        $uti->setItem('data',$data);
        setUserSession($app,$user->id,$user->name,$user->mobilephone,$user->e_mail,$user->permission);
        $uti->setSuccessTrue();
        return;
    }
    $uti->addError(ERROR_USER_CAN_NOT_LOGIN);
});
//api
$app->get('/user/login-state',function() use($app) {
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if($user != null){
        $uti->setItem('isLoggedIn',true);
        $uti->setItem('name',$user['name']);
        $uti->setItem('mobilephone',$user['mobilephone']);
        $uti->setItem('e_mail',$user['e_mail']);
    }
    else{
        $uti->setItem('isLoggedIn',false);
    }
    $uti->setSuccessTrue();
});
//api
$app->get('/user/logout',function() use($app) {
    $app->session->destroy();
    $app->utility->setSuccessTrue();
});