<?php

$AppTypes = array();

//common
function getCurrentUser($app){
    $session = $app->session;
    if($session->has('user_id')){
        $user = array();
        $user['id'] = $session->get('user_id');
        $user['name'] = $session->get('user_name');
        $user['mobilephone'] = $session->get('user_mobilephone');
        $user['e_mail'] = $session->get('user_e_mail');
        $user['permission'] = $session->get('user_permission');
        return $user;
    }
    return null;
}
//common
function getImageUrl($name){
    global $config;
    return $config->application->domain .'/' . $config->application->imgUrl . $name;
}
//common
function getPostJsonObject(){
    return json_decode(file_get_contents('php://input', 'r'));
}

function hasPermission($user,$mask){
    if(!isset($user)){
        return false;
    }
    return ($user['permission'] & $mask) == $mask;
}

function isManager($user){
    if(!isset($user)){
        return false;
    }
    return hasPermission($user,PERMISSION_ADMIN) || hasPermission($user,PERMISSION_PRODUCT_MANAGER)|| hasPermission($user,PERMISSION_CATEGORY_MANAGER)|| hasPermission($user,PERMISSION_ORDER_MANAGER)|| hasPermission($user,PERMISSION_STORY_MANAGER);
}

date_default_timezone_set("Asia/Shanghai");
function getMysqlDateTimeNow(){
    return date('Y-m-d H:i:s');
}

function insertHistory($app,$type,$object_id,$content,$operator_id,$date = null){
    if($date == null){
        $date = getMysqlDateTimeNow();
    }
    $phql = "INSERT INTO RjHistory(type,object_id,content,operator_id,date) VALUES(:type:,:oid:,:content:,:opeid:,:date:)";
    $res = $app->modelsManager->executeQuery($phql,array(
        'type' => $type,
        'oid' => $object_id,
        'content' => $content,
        'opeid' => $operator_id,
        'date' => $date
    ));
    return $res->success();
}
function generateCommonId($app){
    return getCurrentUser($app)['id'].(microtime(true)*10000).rand(1000,9999);
}
function cors($app)
{
    global $config;
        $status = 200;
        $description = 'OK';
        $response = $app->response;

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $description;
        $response->setRawHeader($status_header);
        $response->setStatusCode($status, $description);
        $response->setHeader('Access-Control-Allow-Origin', $config->application->clientDomain);
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        $response->setHeader('Access-Control-Allow-Methods','GET,PUT,POST,DELETE,OPTIONS');
        $response->setHeader('Access-Control-Allow-Credentials','true');
        $response->sendHeaders();
}
$app->get('/test',function() use($app) {
   //phpinfo();
    echo hasPermission(getCurrentUser($app),PERMISSION_USER)?'true':'false';
    echo hasPermission(getCurrentUser($app),PERMISSION_PRODUCT_MANAGER)?'true':'false';
    echo hasPermission(getCurrentUser($app),PERMISSION_ADMIN)?'true':'false';
});

$app->options('/{a:.*}',function($a)use($app)
{

});
