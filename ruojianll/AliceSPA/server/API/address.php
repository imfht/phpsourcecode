<?php

include_once "common.php";

function checkAddressPermission_normal($app,$user){
    return hasPermission($user,PERMISSION_USER);
}

function modifyAddress($app,$user,$add){
    if(!(isset($add->province_id) && isset($add->city_id) && isset($add->county_id) && isset($add->detail) && isset($add->phone) && isset($add->name))){
        return false;
    }
    $res = RjAddress::findFirst(array('id=:id:','bind' => array(
        'id' => $add->id
    )));
    if($res === false){
        return false;
    }
    return $res->save(
        array(
            'user_id' => $user['id'],
            'province_id' => $add->province_id,
            'city_id' => $add->city_id,
            'county_id' => $add->county_id,
            'detail' => $add->detail,
            'phone' => $add->phone,
            'name'=> $add->name,
            'postcode' => $add->postcode,
            'public' => 1
        )
    );
}

function addAddress($app,$user,$add){
    if(!(isset($add->province_id) && isset($add->city_id) && isset($add->county_id) && isset($add->detail) && isset($add->phone) && isset($add->name))){
        return false;
    }
    $addt = new RjAddress();
    $addt->user_id = $user['id'];
    $addt->province_id = $add->province_id;
    $addt->city_id = $add->city_id;
    $addt->county_id = $add->county_id;
    $addt->detail = $add->detail;
    $addt->phone = $add->phone;
    $addt->name = $add->name;
    $addt->postcode = isset($add->postcode)?$add->postcode:'none';
    $addt->public = 1;
    return $addt->create();
}
function getAddress($app,$address_id,$showDelete = false){
    $con = 'id = '.$address_id;
    if(!$showDelete){
        $con = $con.' AND public = ' . 1;
    }
    $res = RjAddress::findFirst(array(
        $con
    ));
    if($res === false){
        return null;
    }
    return $res;
}
function deleteAddress($app,$user,$add,&$ret){
    $addt = RjAddress::findFirst(array(
        'id=:id: AND user_id=:uid:',
        'bind' => array(
            'id' => $add->id,
            'uid' => $user['id']
        )
    ));
    if($addt === false){
        return false;
    }
    return $addt->save(array('public'=>0));
}

$app->post('/address/add',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    $user = getCurrentUser($app);
    if(!checkAddressPermission_normal($app,$user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    if(!addAddress($app,$user,$data)) {
        $uti->addError(ERROR_EXECUTE_FAIL);
    }
    else{
        $uti->setSuccessTrue();
    }
});
function getAddresses($app,$id,$uid,$showDeleted = false){
    $config=array();
    $phql = "";
    if(!$showDeleted){
        $phql = $phql . "public=:public:";
        $config['public'] = 1;
    }

    if($id != null){
        $phql = $phql . " AND id=:id:";
        $config['id'] = $id;
    }
    if($uid != null){
        $phql = $phql . " AND user_id=:uid:";
        $config['uid'] = $uid;
    }
    return RjAddress::find(array($phql,'bind' => $config));
}
$app->get('/address/all',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAddressPermission_normal($app,$user)){
        $uti->setError(ERROR_NO_PERMISSION);
        return;
    }
    $res = getAddresses($app,null,$user['id']);
    $uti->setSuccessTrue();
    $t = array();
    foreach($res as $add){
        $t[] = $uti->getObjectMap($add);
    }
    $uti->setItem('addresses',$t);
});

$app->post('/address/delete',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    $user = getCurrentUser($app);
    if(!checkAddressPermission_normal($app,$user)){
        $uti->addError(ERROR_NO_PERMISSION);
    }
    if(deleteAddress($app,$user,$data,$ret)){
        $uti->setSuccessTrue();
    }
    else{
        $uti->addError(ERROR_EXECUTE_FAIL);
    }
});

$app->get('/address/id-{id:[0-9]+}',function($id)use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAddressPermission_normal($app,$user)){
        $uti->setError(ERROR_NO_PERMISSION);
    }
    $res = getAddresses($app,$id,$user['id']);
    $uti->setSuccessTrue();
    $t =(object)null;
    foreach($res as $add){
        $t = $uti->getMap($add);
        break;
    }
    $uti->setItem('address',$t);
});

$app->post('/address/modify',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    $user = getCurrentUser($app);
    if(!checkAddressPermission_normal($app,$user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    if(!modifyAddress($app,$user,$data)){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});