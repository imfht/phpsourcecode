<?php

include_once "common.php";
include_once "product.php";

function checkCartPermission($app,$user){
    return hasPermission($user,PERMISSION_USER);
}

function isCartItemExist($app,$uid,$pid){
    return RjCart::findFirst(array(
        'RjCart.user_id = :uid: AND RjCart.product_id = :pid:',
        'bind' => array(
            'uid' => $uid,
            'pid' => $pid
        )
    ))===false?false:true;
}
function getCarts($uid){
    return RjCart::find(array(
        'user_id = :uid:',
        'bind' => array(
            'uid' => $uid
        )
    ));
}
$app->get('/cart/all',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkCartPermission($app,$user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $items = getCarts($user['id']);
    $uti->setSuccessTrue();
    $t = array();
    foreach($items as $item){
        $a = $item->toArray();
        $p = $item->product;
        $a['product'] = $p->toArray();
        $uti->addFiles2Array($a['product'],'images',$p->images,'getImageUrl');
        $t[] = $a;
    }
    $uti->setItem('cart',$t);
});


$app->post('/cart/delete',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    $user = getCurrentUser($app);
    if(!checkCartPermission($app,$user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $app->db->begin();
    $res = RjCart::find(array(
        'RjCart.user_id = :uid: AND RjCart.product_id = :pid:',
        'bind' => array(
            'uid' => $user['id'],
            'pid' => $data->product_id
        )));
    foreach($res as $c){
        if(!$c->delete()){
            $app->db->rollback();
            return;
        };
    }
    $app->db->commit();
    $uti->setSuccessTrue();
});

$app->get('/cart/delete_all',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkCartPermission($app,$user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $phql = "DELETE FROM RjCart WHERE RjCart.user_id = :uid:";
    $state = $app->modelsManager->executeQuery($phql,array(
        'uid' => $user['id'],
    ));
    if(!$state->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});

$app->post('/cart/add',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    $user = getCurrentUser($app);
    if(!checkCartPermission($app,$user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    if(isCartItemExist($app,$user['id'],$data->product_id)){
        $uti->addError(ERROR_HAS_CURRENT_RECORD);
        return;
    }

    $pro = getProductById($app,$data->product_id);
    if($pro == null){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }

    if($pro['price'] != $data->price){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }

    $t = new RjCart();
    $t->user_id = $user['id'];
    $t->product_id = $data->product_id;
    $t->number = $data->number;
    $t->price = $data->price;
    if(!$t->create()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});

$app->post('/cart/setNumber',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    $user = getCurrentUser($app);
    if(!checkCartPermission($app,$user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    if(!isCartItemExist($app,$user['id'],$data->product_id)){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }

    if(!isset($data->number) || $data->number <= 0){
        $uti->addError(ERROR_NUMBER_INVILID);
        return;
    }
    $t = RjCart::findFirst(array(
        'user_id=:uid: AND product_id=:pid:',
        'bind' => array(
            'uid' => $user['id'],
            'pid' => $data->product_id,
        )
    ));
    if($t===false){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }

    $number = getProductNumber($t->product_id);
    if($number === false){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }
    if($number < $data->number) {
        $uti->addError(ERROR_PRODUCT_NOT_ENOUGH);
        return;
    }

    if(!$t->save(array('number'=>$data->number))){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});