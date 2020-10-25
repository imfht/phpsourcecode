<?php

include_once "common.php";
include_once "order.php";
include_once "alipay/alipay.php";
function checkAdminPermissionAccess($user){
    return hasPermission($user,PERMISSION_ORDER_MANAGER) || hasPermission($user,PERMISSION_PRODUCT_MANAGER) || hasPermission($user,PERMISSION_STORY_MANAGER)|| hasPermission($user,PERMISSION_CATEGORY_MANAGER)|| hasPermission($user,PERMISSION_MARKETING_MANAGER)|| hasPermission($user,PERMISSION_USER_MANAGER) || hasPermission($user,PERMISSION_ADMIN);
}
function checkAdminPermissionOrder($user){
    return hasPermission($user,PERMISSION_ORDER_MANAGER) ||hasPermission($user,PERMISSION_ADMIN);
}
function checkAdminPermissionAddress($user){
    return hasPermission($user,PERMISSION_ORDER_MANAGER) ||hasPermission($user,PERMISSION_ADMIN);
}
function checkAdminPermissionProduct($user){
    return hasPermission($user,PERMISSION_PRODUCT_MANAGER) || hasPermission($user,PERMISSION_ADMIN);
}
function checkAdminPermissionStory($user){
    return hasPermission($user,PERMISSION_STORY_MANAGER) || hasPermission($user,PERMISSION_ADMIN);
}
function checkAdminPermissionCategory($user){
    return hasPermission($user,PERMISSION_CATEGORY_MANAGER) || hasPermission($user,PERMISSION_ADMIN);
}

function checkAdminPermissionMarketing($user){
    return hasPermission($user,PERMISSION_MARKETING_MANAGER) || hasPermission($user,PERMISSION_ADMIN);
}

function checkAdminPermissionUser($user){
    return hasPermission($user,PERMISSION_USER_MANAGER) || hasPermission($user,PERMISSION_ADMIN);
}

$app->post('/admin/hasPermission/access',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    $uti->setSuccessTrue();
    $uti->setItem('hasPermission',checkAdminPermissionAccess($user));
});
$app->post('/admin/hasPermission/order',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    $uti->setSuccessTrue();
    $uti->setItem('hasPermission',checkAdminPermissionOrder($user));
});
$app->post('/admin/hasPermission/story',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    $uti->setSuccessTrue();
    $uti->setItem('hasPermission',checkAdminPermissionStory($user));
});
$app->post('/admin/hasPermission/product',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    $uti->setSuccessTrue();
    $uti->setItem('hasPermission',checkAdminPermissionProduct($user));
});
$app->post('/admin/hasPermission/category',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    $uti->setSuccessTrue();
    $uti->setItem('hasPermission',checkAdminPermissionCategory($user));
});
$app->post('/admin/hasPermission/marketing',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    $uti->setSuccessTrue();
    $uti->setItem('hasPermission',checkAdminPermissionMarketing($user));
});

$app->post('/admin/hasPermission/user',function()use($app){
   $uti = $app->utility;
    $user = getCurrentUser($app);
    $uti->setSuccessTrue();
    $uti->setItem('hasPermission',checkAdminPermissionUser($user));
});

$app->post('/admin/order/all',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionOrder($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $uti->setSuccessTrue();
    $uti->setItem('orders',getOrders('all',$app,true));
});
$app->post('/admin/address/get',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionAddress($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    $res = getAddresses($app,$data->id,null);
    foreach($res as $add){
        $uti->setSuccessTrue();
        $uti->setItem('address',$add);
        return;
    }
    $uti->addError(ERROR_NO_CURRENT_RECORD);
});
$app->post('/admin/order/setSend',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionOrder($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();

    if(!orderIsExisted($data->id,$app)){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }
    $app->db->begin();
    $order = RjOrder::findFirst(array('id=:id:','bind'=>array(
        'id'=>$data->id
    )));

    if($order === false){

        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $date = getMysqlDateTimeNow();
    if(!$order->save(array(
        'pid' => $data->post_id,
        'pcom' => $data->post_company,
        'oid' => $user['id'],
        'state' => ORDER_STATE_SENDING,
        'date' => $date,
        'id' => $data->id
    ))){
        $app->db->rollback();

        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $phql = "INSERT INTO RjHistory(type,object_id,content,operator_id,date) VALUES(:type:,:oid:,:content:,:opeid:,:date:)";
    $res = $app->modelsManager->executeQuery($phql,array(
       'type' => HISTORY_TYPE_ORDER,
        'oid' => $data->id,
        'content' => 'post_id: ' . $data->post_id . " , post_company: " . $data->post_company,
        'opeid' => $user['id'],
        'date' => $date
    ));
    if(!$res->success()){
        $app->db->rollback();

        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    if(setSendAlipay($order->trad_id,$data->post_company,$data->post_id)==='F'){
        $app->db->rollback();

        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    };
    $app->db->commit();
    $uti->setSuccessTrue();
});

$app->post('/admin/product/edit',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionProduct($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }

    $data = getPostJsonObject();
    if(!isset($data->category_id)||!isset($data->name)||!isset($data->number)||!isset($data->price)||!isset($data->old_price)||!isset($data->comment)||!isset($data->summary)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }

    $phql = "UPDATE RjProduct SET category_id=:cid:, name=:name:,number=:number:,price=:price:,old_price=:old_price:,comment=:comment:,summary=:summary: WHERE id=:id:";
    $res = $app->modelsManager->executeQuery($phql,array(
        'id' => $data->id,
        'cid' => $data->category_id,
        'name' => $data->name,
        'number' => $data->number,
        'price' => $data->price,
        'old_price' => $data->old_price,
        'comment' => $data->comment,
        'summary' => $data->summary
    ));
    if(!$res->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});
$app->post('/admin/product/setCategory',function()use($app) {
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionProduct($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }

    $data = getPostJsonObject();
    if (!isset($data->product_id) || !isset($data->category_id) ) {
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $phql = "UPDATE RjProduct SET category_id = :cid: WHERE id=:pid:";
    $res = $app->modelsManager->executeQuery($phql,array(
        'cid' => $data->category_id,
        'pid' => $data->product_id
    ));
    if(!$res->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});
$app->post('/admin/product/addImages',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionProduct($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    if(!isset($data->product_id)||!isset($data->upload_file_names)){
        $app->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $app->db->begin();
    foreach($data->upload_file_names as $upload_file_name){

        if(null == $app->UFM->increaseRefrenceCount($app,$upload_file_name)){
            $uti->addError(ERROR_EXECUTE_FAIL);
            $app->db->rollback();
            return;
        };
        $phql = "INSERT INTO RjProductImage(product_id,upload_file_name) VALUES(:pid:,:ufn:)";
        $res = $app->modelsManager->executeQuery($phql,array(
            'pid' => $data->product_id,
            'ufn' => $upload_file_name
        ));
        if(!$res->success()){
            $uti->addError(ERROR_EXECUTE_FAIL);
            $app->db->rollback();
            return;
        }
    }
    $app->db->commit();
    $uti->setSuccessTrue();
});
$app->post('/admin/product/removeImage',function()use($app) {
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionProduct($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    if(!isset($data->product_id) || !isset($data->upload_file_name)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $app->db->begin();
    if(!$app->UFM->reduceRefrenceCount($app,$data->upload_file_name)){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $phql = 'DELETE FROM RjProductImage WHERE product_id=:pid: AND upload_file_name=:ufn:';
    if(!
        $app->modelsManager->executeQuery($phql,array(
            'pid' => $data->product_id,
            'ufn' => $data->upload_file_name
        ))->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        $app->db->rollback();
        return;
    }
    $app->db->commit();
    $uti->setSuccessTrue();
});
$app->post('/admin/product/add',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionProduct($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }

    $data = getPostJsonObject();
    if(!isset($data->category_id)||!isset($data->name)||!isset($data->number)||!isset($data->price)||!isset($data->old_price)||!isset($data->comment)||!isset($data->summary)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $phql = "INSERT INTO RjProduct(category_id,number,name,price,old_price,comment,public,summary,create_date,sold_number) VALUES(:cid:,:number:,:name:,:price:,:old_price:,:comment:,:public:,:summary:,:cd:,:sold_number:)";
    $res = $app->modelsManager->executeQuery($phql,array(
        'cid' => $data->category_id,
        'name' => $data->name,
        'number' => $data->number,
        'price' => $data->price,
        'old_price' => $data->old_price,
        'comment' => $data->comment,
        'public' => 1,
        'summary' => $data->summary,
        'cd' => getMysqlDateTimeNow(),
        'sold_number' => 0
    ));
    if(!$res->success()){
        foreach($res->getMessages() as $msg){
            $uti->addError($msg->getMessage());
        }
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});
$app->post('/admin/product/remove',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionProduct($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }

    $data = getPostJsonObject();
    if(!isset($data->id)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $phql = "UPDATE RjProduct SET public=:public: WHERE id=:id:";
    $res = $app->modelsManager->executeQuery($phql,array(
       'public' => 0,
        'id' => $data->id
    ));
    if(!$res->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});


$app->post('/admin/story/add',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionStory($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    if(!isset($data->title)||!isset($data->content)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $id = generateCommonId($app);
    $phql = "INSERT INTO RjStory (id,title,content,creator_id,public) VALUES(:id:,:title:,:content:,:creator_id:,:public:)";
    $res = $app->modelsManager->executeQuery($phql,array(
        'id' => $id,
        'title' => $data->title,
        'content' => $data->content,
        'creator_id' => $user['id'],
        'public' => 1
    ));
    if(!$res->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});
$app->post('/admin/story/edit',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionStory($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    if(!isset($data->id)||!isset($data->title)||!isset($data->content)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $phql = "UPDATE RjStory SET title=:title: , content=:content: WHERE id=:id:";
    $res = $app->modelsManager->executeQuery($phql,array(
        'title' => $data->title,
        'content' => $data->content,
        'id' => $data->id
    ));
    if(!$res->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});
$app->post('/admin/story/addImages',function()use($app) {
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionStory($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    if(!isset($data->story_id)||!isset($data->upload_file_names)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $app->db->begin();
    foreach($data->upload_file_names as $upload_file_name){
        if(null == $app->UFM->increaseRefrenceCount($app,$upload_file_name)){
            $uti->addError(ERROR_EXECUTE_FAIL);
            $app->db->rollback();
            return;
        };

        $phql = "INSERT INTO RjStoryImage(story_id,upload_file_name) VALUES(:sid:,:ufn:)";
        $res = $app->modelsManager->executeQuery($phql,array(
            'sid' => $data->story_id,
            'ufn' => $upload_file_name
        ));
        if(!$res->success()){
            $uti->addError(ERROR_EXECUTE_FAIL);
            $app->db->rollback();
            return;
        }
    }
    $app->db->commit();
    $uti->setSuccessTrue();
});
$app->post('/admin/story/removeImage',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionStory($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }

    $data = getPostJsonObject();
    if(!isset($data->story_id) || !isset($data->upload_file_name)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    if(!$app->UFM->reduceRefrenceCount($app,$data->upload_file_name)){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $phql = 'DELETE FROM RjStoryImage WHERE story_id=:sid: AND upload_file_name=:ufn:';
    if(!
    $app->modelsManager->executeQuery($phql,array(
        'sid' => $data->story_id,
        'ufn' => $data->upload_file_name
    ))->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});
$app->post('/admin/story/addProduct',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionStory($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    if(!isset($data->story_id) || !isset($data->product_id)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $phql = "INSERT INTO RjStoryProduct(story_id,product_id) VALUES (:sid:,:pid:)";
    $res = $app->modelsManager->executeQuery($phql,array(
        'sid' => $data->story_id,
        'pid' => $data->product_id
    ));
    if(!$res->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});
$app->post('/admin/story/removeProduct',function()use($app) {
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionStory($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    if (!isset($data->story_id) || !isset($data->product_id)) {
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $phql = "DELETE FROM RjStoryProduct WHERE story_id=:sid: AND product_id=:pid:";
    $res = $app->modelsManager->executeQuery($phql,array(
        'sid' => $data->story_id,
        'pid' => $data->product_id
    ));
    if(!$res->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();
});
$app->post('/admin/story/remove',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionStory($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    if (!isset($data->id)) {
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $phql = "UPDATE RjStory SET public=:public: WHERE id=:id:";
    $res = $app->modelsManager->executeQuery($phql,array(
        'public' => 0,
        'id' => $data->id
    ));
    if(!$res->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        return;
    }
    $uti->setSuccessTrue();

});
$app->post('/admin/story/all',function()use($app) {
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionStory($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $phql = "SELECT * FROM RjStory WHERE public=:public:";
    $res = $app->modelsManager->executeQuery($phql,array(
        'public' => 1
    ));
    $uti->setSuccessTrue();
    $t = array();
    foreach($res as $story){
        $t[] = $uti->getObjectMap($story);
    }
    $uti->setItem('stories',$t);
});

$app->post('/admin/category/edit',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionCategory($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    if(!isset($data->id) || !isset($data->name)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $phql = "UPDATE RjCategory SET name=:name: WHERE id=:id:";
    $res = $app->modelsManager->executeQuery($phql,array(
       'name' => $data->name,
        'id' => $data->id
    ));
    if(!$res->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        $uti->setSuccessFalse();
    }
    $uti->setSuccessTrue();
});

$app->post('/admin/category/add',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionCategory($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    if(!isset($data->name)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $phql = "INSERT INTO RjCategory(name,public) VALUES (:name:,:public:)";
    $res = $app->modelsManager->executeQuery($phql,array(
        'name' => $data->name,
        'public' => 1
    ));
    if(!$res->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
    }
    else
    {
        $uti->setSuccessTrue();  
    }

});
$app->post('/admin/category/remove',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionCategory($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    if(!isset($data->id)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $phql = "UPDATE RjCategory SET public=:public: WHERE id=:id:";
    $res = $app->modelsManager->executeQuery($phql,array(
        'id' => $data->id,
        'public' => 0
    ));
    if(!$res->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
    }
    $uti->setSuccessTrue();
});

$app->post('/admin/category/setImage',function()use($app) {
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionCategory($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    if(!isset($data->category_id)||!isset($data->upload_file_name)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $image = getCategoryImage($app,$data->category_id);
    $app->db->begin();
    if($image != null){
        if(!$app->UFM->reduceRefrenceCount($app,$image['upload_file_name'])){
            $uti->addError(ERROR_EXECUTE_FAIL);
            $app->db->rollback();
            return;
        };
    }
    $upload_file_name = $data->upload_file_name;
    if(null == $app->UFM->increaseRefrenceCount($app,$upload_file_name)){
        $uti->addError(ERROR_EXECUTE_FAIL);
        $app->db->rollback();
        return;
    };

    $phql = "UPDATE RjCategory SET upload_file_name=:ufn: WHERE id=:id:";
    $res = $app->modelsManager->executeQuery($phql,array(
        'id' => $data->category_id,
        'ufn' => $upload_file_name
    ));
    if(!$res->success()){
        $app->UFM->reduceRefrenceCount($app,$upload_file_name);
        $uti->addError(ERROR_EXECUTE_FAIL);
        $app->db->rollback();
        return;
    }
    $app->db->commit();
    $uti->setSuccessTrue();
});
$app->post('/admin/category/removeImage',function()use($app) {
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionProduct($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }

    $data = getPostJsonObject();
    if(!isset($data->id)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $image = getCategoryImage($app,$data->id);
    if($image == null){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }
    $app->db->begin();
    if(!$app->UFM->reduceRefrenceCount($app,$image['upload_file_name'])){
        $uti->addError(ERROR_EXECUTE_FAIL);
        $app->db->rollback();
        return;
    }
    $phql = 'UPDATE RjCategory SET upload_file_name=:ufn: WHERE id=:id:';
    if(!
    $app->modelsManager->executeQuery($phql,array(
        'id' => $data->id,
        'ufn' => null
    ))->success()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        $app->db->rollback();
        return;
    }
    $app->db->commit();
    $uti->setSuccessTrue();
});

$app->post('/admin/marketing/banner/add',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionMarketing($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $ban = new RjBanner();
    $columns = $uti->getDBColumns($ban,array('id','upload_file_name'));
    $json = $uti->getPostData($columns);
    if($json === false){
        return;
    }
    if($uti->addDBRecord($ban,$columns,$json)){
        $uti->setSuccessTrue();
    };
});
$app->post('/admin/marketing/banner/remove',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionMarketing($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $json = $uti->getPostData(array('id'));
    if($json == false){
        return;
    }
    $ban = RjBanner::findFirst(array('id=:id:','bind' => array(
        'id' => $json->id
    )));
    if($ban == false){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }
    $app->db->begin();
    if($ban->upload_file_name!=null){
        if(false == $app->UFM->reduceRefrenceCount($app,$ban->upload_file_name)){
            $uti->addError(ERROR_EXECUTE_FAIL);
            $app->db->rollback();
            return;
        }
    }
    if(!$ban->delete()){
        $uti->addError(ERROR_EXECUTE_FAIL);
        $app->db->rollback();
        return;
    }
    $app->db->commit();
    $uti->setSuccessTrue();
});
$app->post('/admin/marketing/banner/edit',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if (!checkAdminPermissionMarketing($user)) {
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $columns = $uti->getDBColumns(new RjBanner(),array('upload_file_name'));
    $json = $uti->getPostData($columns);
    if($json === false){
        return;
    }
    $ban = RjBanner::findFirst(array('id=:id:','bind'=>array(
        'id' => $json->id
    )));
    if($ban == false){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }
    if($uti->editDBRecord($ban,$columns,$json)){
        $uti->setSuccessTrue();
    };
});



$app->post('/admin/marketing/banner/setImage',function()use($app){
   $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionMarketing($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $json = $uti->getPostData(array('id','upload_file_name'));
    if($json == false){
        return;
    }
    $ban = RjBanner::findFirst(array('id=:id:','bind'=>array(
        'id' => $json->id
    )));
    if(false == $ban){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }
    $app->db->begin();
    if(!$app->UFM->increaseRefrenceCount($app,$json->upload_file_name)){
        $uti->addError(ERROR_EXECUTE_FAIL);
        $app->db->rollback();
        return;
    }
    if(!$uti->editDBRecord($ban,array('upload_file_name'),$json)){
        $app->db->rollback();
        return;
    };
    $app->db->commit();
    $uti->setSuccessTrue();
});
$app->post('/admin/marketing/banner/removeImage',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionMarketing($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $json = $uti->getPostData(array('id'));
    if($json == false){
        return;
    }
    $ban = RjBanner::findFirst(array('id=:id:','bind'=>array(
        'id' => $json->id
    )));
    if(false == $ban){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }
    $app->db->begin();
    if($ban->upload_file_name != null){
        if(false == $app->UFM->reduceRefrenceCount($app,$ban->upload_file_name)){
            $uti->addError(ERROR_EXECUTE_FAIL);
            $app->db->rollback();
            return;
        };
    }
   if(!$ban->save(array('upload_file_name'=>null))){
       $uti->addERROR(ERROR_EXECUTE_FAIL);
       $app->db->rollback();
       return;
   }
    $app->db->commit();
    $uti->setSuccessTrue();
});

$app->get('/admin/user/count',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkAdminPermissionUser($user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $phql = 'SELECT COUNT(*) as c FROM RjUser';
    $res = $app->modelsManager->executeQuery($phql);
    foreach($res as $i){
        $uti->setItem('count',$i->c);
    }
    $uti->setSuccessTrue();
});

