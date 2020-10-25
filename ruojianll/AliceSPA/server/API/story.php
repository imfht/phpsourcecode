<?php

include_once "common.php";
include_once "product.php";

function getStoryImagesById($app,$sid){
    $res = RjStoryImage::find(array('story_id=:sid:','bind'=>array(
        'sid' => $sid
    )));
    $t = array();
    foreach($res as $file){
        $t[] = array(
            'upload_file_name' => $file->upload_file_name,
            'url' => getImageUrl($file->upload_file_name)
        );
    }
    return $t;
}
function getStoryProductsById($app,$sid){
    $res = RjStoryProduct::find(array('story_id=:sid:','bind' => array(
        'sid' => $sid
    )));
    $t = array();
    foreach($res as $pid){
        $t[] = $pid->product_id;
    }

    return getProductsByIdArray($app,$t,false,true);
}
function getStoriesByIdArray($app,$ida,$isMap,$hasContent,$withImage = false){
    if(count($ida) == 0){
        return null;
    }
    $columns=array();
    $columns[]='id';
    $columns[]='title';
    if($hasContent){
        $columns[]='content';
    }
    $items = $app->modelsManager->createBuilder()
        ->from('RjStory')
        ->inWhere('id',$ida)
        ->andWhere('public',1)
        ->getQuery()
        ->execute();
    $res = array();
    foreach($items as $story){
        $storyt = $story->toArray();
        if($withImage) {
            $app->utility->addFiles2Array($storyt,'images',$story->images,'getImageUrl');
        }
        if($isMap){
            $res[$story->id] = $storyt;
        }
        else{
            $res[] = $storyt;
        }
    }
    return $res;
}
function getStoryById($app,$id){
    $res = RjStory::findFirst(array('id=:id: AND public=:public:','bind' => array(
        'id' => $id,
        'public' => 1
    )));
    if($res === false){
        return null;
    }
    return $res->toArray();
}
$app->post('/story/getImages',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    if(!isset($data->id)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $uti->setSuccessTrue();
    $uti->setItem('images',getStoryImagesById($app,$data->id));
});
$app->post('/story/getProducts',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    if(!isset($data->story_id)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $uti->setSuccessTrue();
    $uti->setItem('products',getStoryProductsById($app,$data->story_id));
});

$app->post('/story/get',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    if(!isset($data->id)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }
    $story = getStoryById($app,$data->id);
    if($story == null){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }
    $uti->setSuccessTrue();
    $story['products'] = getStoryProductsById($app,$data->id);
    $story['images'] = getStoryImagesById($app,$data->id);
    $uti->setItem('story',$story);
});
