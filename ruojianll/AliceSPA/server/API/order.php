<?php

include_once "common.php";
include_once "product.php";
include_once "comment.php";
include_once "address.php";
function checkOrderPermission_normal($user){
    return hasPermission($user,PERMISSION_USER);
}

function generateOrderId($app){
    return getCurrentUser($app)['id'].(microtime(true) *10000);
}
function getOrders($state,$app,$withProducts = false){
    $arg = array();
    $con = "history = 0";
    if($state != "all"){
        $con = $con . " AND state = :state:";
        $arg['state'] = $state;
    }
    $res = RjOrder::find(array($con,'bind' => $arg));
    $ret = array();
    if($withProducts){
        foreach($res as $order){
            $t = $order->toArray();
            $items = $order->orderItems;
            foreach($items as $item){
                $ti = $item->toArray();
                $ti['product'] = $item->product->toArray();
                $t['items'][]=$ti;
            }
            $ret[] = $t;
        }
    }
    return $ret;
}
function orderIsExisted($oid,$app){
    $res = RjOrder::findFirst(array('id=:id:','bind' => array('id'=>$oid)));
    return $res===false?false:true;
}

function getOrderItems($app,$order_id){
    $res = RjOrderItem::find(array('order_id=:oid:','bind'=>array('oid'=>$order_id)));
    return $app->utility->getDBResultArrays($res);
}

$app->post('/order/add',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    $user = getCurrentUser($app);
    if(!checkOrderPermission_normal($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    if(!(isset($data->address_id))){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }

    //检查价格是否变动
    $array = array();
    foreach($data->items as $item){
        $array[] = array(
            'id' => $item->product_id,
            'price' => $item->price
        );
    }
    if(!checkProductsPrice($app,$array)){
        $uti->addError(ERROR_PRODUCT_PRICE_CHANGED);
        return;
    }
    //--检查价格是否变动


    $res = RjAddress::findFirst(array('user_id=:uid: AND id=:id: AND public=:public:','bind'=>array(
        'uid' => $user['id'],
        'id' => $data->address_id,
        'public' => 1
    )));
    if($res === false){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }
    $total = 0;
    foreach($data->items as $item){
        $total += $item->number * $item->price;
    }
    $app->db->begin();
    $order_id = generateOrderId($app);
    $order = new RjOrder();
        $order->id = $order_id;
        $order->state = ORDER_STATE_UNPAID;
        $order->user_id = $user['id'];
        $order->operator_id = $user['id'];
        $order->history = 0;
        $order->public = 1;
        $order->date = getMysqlDateTimeNow();
        $order->address_id = $data->address_id;
        $order->from_id = 'first';
        $order->expect_pay = $total;
    if(!$order->create()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
    foreach($data->items as $item){
        if(reduceProductNumber($item->product_id,$item->number)){
            $i = new RjOrderItem();
            $i->product_id = $item->product_id;
            $i->order_id = $order_id;
            $i->number = $item->number;
            $i->message = isset($item->message)?$item->message:null;
            $i->price = $item->price;
            if(!$i->create()){
                $uti->setSuccessFalse();
                $uti->addError(ERROR_EXECUTE_FAIL);
                $app->db->rollback();
                return;
            }
        }else{
            $uti->setSuccessFalse();

            $uti->addError(ERROR_PRODUCT_NOT_ENOUGH);
            $uti->setItem('productName',getProductById($app,$item->product_id)['name']);
            $app->db->rollback();
            return;
        }

    }
    $app->db->commit();
    $uti->setItem('order_id',$order_id);
});

$app->post('/order/delete',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    $data = getPostJsonObject();
    if(!checkOrderPermission_normal($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    if(!isset($data->id)){
        $uti->addError(INVILID_JSON_RES);
        return;
    }
    $res = RjOrder::findFirst(array('id=:id: AND user_id=:uid:','bind' => array(
        'id' => $data->id,
        'uid' => $user['id']
    )));

    $items = $res->orderItems;
    $app->db->begin();
    foreach($items as $item){
        if(!increaseProductNumber($item->product_id,$item->number)){
            $uti->addError(ERROR_EXECUTE_FAIL);
            return;
        };
    }

    if(!$res->save(array('public' => 0))){
        $uti->addError(ERROR_EXECUTE_FAIL);
        $app->db->rollback();
        return;
    }
    $app->db->commit();
    $uti->setSuccessTrue();
});

$app->get('/order/all',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkOrderPermission_normal($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $orders = RjOrder::find(array('public = 1 AND history = 0 AND user_id = :uid:','bind' => array(
        'uid' => $user['id']
        ),
        'order' => 'date DESC'));
    $rorders = array();
    $pids = array();

    foreach($orders as $order){
        $t = $order->toArray();
        $t['address'] = $order->address->toArray();
        $items = $order->orderItems;
        foreach($items as $item){
            $t['items'][] = $item->toArray();
            $pids[] = $item->product_id;
        }
        $rorders[] = $t;
    }
    $pros = getProductsByIdArray($app,$pids,true,true);
    foreach($rorders as &$order){
        if(isset($order['items'])){

            foreach($order['items'] as &$item){
                $pro = isset($pros[$item['product_id']])?$pros[$item['product_id']]:null;
                if($pro == null){
                    continue;
                }
                $item['product'] = $pro;
            }
        }
    }
    $uti->setItem('orders',$rorders);
    $uti->setSuccessTrue();
});

function setReceived($app,$order_id,$user_id,$operator_id){
    $con = "id=:oid:";
    $config = array();
    $config['oid'] = $order_id;
    if($user_id != null){
        $con = $con . " AND user_id=:uid:";
        $config['uid'] = $user_id;
    }
    $res = RjOrder::findFirst(array($con,'bind'=>$config));
    if($res === false){
        return false;
    }
    if(!$res->save(array('state'=>ORDER_STATE_RECEIVED))){
        return false;
    }
    $orderItems = $res->orderItems;
    foreach($orderItems as $item){
        $pro = $item->product;
        if($pro == false || $pro==null){
            return false;
        }
        if(!$pro->save(array('sold_number'=>$pro->sold_number+$item->number))){
            return false;
        }
    }
    if(!insertHistory($app,'ORDER',$order_id,'Set RECEIVED',$operator_id)){
        return false;
    };
    return true;
}

$app->post('/order/setReceived',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkOrderPermission_normal($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    $app->db->begin();
    $res = setReceived($app,$data->id,$user['id'],$user['id']);
    if($res){
        $uti->setSuccessTrue();
        $orderItems = getOrderItems($app,$data->id);
        foreach($orderItems as $item){
            if(!addCommentMap($app,$user['id'],$item['product_id'])){
                $uti->addError(ERROR_EXECUTE_FAIL);
                $app->db->rollback();
                return;
            };

        }
    }
    else{
        $app->db->rollback();
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $app->db->commit();
});

$app->post('/order/pay_t',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkOrderPermission_normal($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    $res = RjOrder::findFirst(array('id=:id: AND user_id=:uid: AND state=:state:','bind' => array(
        'id' => $data->id,
        'uid' => $user['id'],
        'state' => ORDER_STATE_UNPAID
    )));
    if($res === false){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }
    if(!$res->save(array('trad_id'=>$data->trad_id,'state'=>ORDER_STATE_UNSENT))){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    insertHistory($app,'ORDER',$data->id,'Set Pay trad_id:'.$data->trad_id,$user['id']);
    $uti->setSuccessTrue();
});
//$app->get('/order/test',function(){
//   echo microtime(true);
//});