<?php

include_once "comment.php";
include_once "story.php";


function getProductById($app,$id,$withImage = false){
    $pro = RjProduct::findFirst(array('RjProduct.id = :id: AND public=1','bind'=>array(
        'id' => $id
    )));
    if($pro === false){
        return null;
    }
    $t = $pro->toArray();
    if($withImage){
        $images = $pro->images;
        foreach($images as $image){
            $t['images'][] = array(
                'upload_file_name'=>$image->upload_file_name,
                'url'=>getImageUrl($image->upload_file_name)
            );
        }
    }
    return $t;
}

function getProductsByIdArray($app,$ida,$isMap = true,$withImage = false){
    if(count($ida) == 0){
        return null;
    }
    $items = $app->modelsManager->createBuilder()
        ->from('RjProduct')
        ->columns('*')
        ->inWhere('id',$ida)
        ->andWhere('public',1)
        ->getQuery()
        ->execute();
    $res = array();
    foreach($items as $pro){
        $prot = $pro->toArray();

        if($withImage){
            $app->utility->addFiles2Array($prot,'images',$pro->images,'getImageUrl');
        }
        if($isMap){
            $res[$pro->id] = $prot;
        }
        else{
            $res[] = $prot;
        }
    }
    return $res;
}

function checkProductsPrice($app,$array){
    $ids = array();
    foreach($array as $pro){
        $ids[]=$pro['id'];
    }
    $pros = getProductsByIdArray($app,$ids);
    foreach($array as $pro){
        if($pros[$pro['id']]['price'] != $pro['price']){
            return false;
        }
    }
    return true;
}

function getProductImages($app,$pid){
    $pros = RjProductImage::find(array('product_id = :id:','bind'=>array(
        'id' => $pid
    )));
    $t = array();
    foreach($pros as $pro){
        $t[] = array(
            'upload_file_name' => $pro->upload_file_name,
            'url' => getImageUrl($pro->upload_file_name)
        );
    }
    return $t;
}

function isProductExist($app,$pid){
    $res = RjProduct::findFirst(array('id = :pid:','bind'=>array(
        'pid' => $pid
    )));
    return $res===false?false:true;
}

function getProductNumber($pid){
    $pro = RjProduct::findFirst(array('id=:pid:','bind' => array(
        'pid' => $pid
    )));
    return $pro ===false?false:$pro->number;
}

function reduceProductNumber($pid,$num){
    $pro = RjProduct::findFirst(array('id=:pid:','bind' => array(
        'pid' => $pid
    )));
    if($pro ===false){
        return false;
    }

    if(!($pro->number >= $num)){

        return false;
    }
    return $pro->save(array('number'=>$pro->number-$num));
}

function increaseProductNumber($pid,$num){
    $pro = RjProduct::findFirst(array('id=:pid:','bind' => array(
        'pid' => $pid
    )));
    if($pro ===false){
        return false;
    }
    return $pro->save(array('number'=>$pro->number+$num));
}

//api
$app->get('/product/all',function() use($app) {
    $uti = $app->utility;
    $uti->setSuccessTrue();
    $pros = RjProduct::find(array('public=:public:','bind'=>array(
            'public'=>1
        )));
    $data = $uti->getDBResultArrays($pros);
    $uti->setItem('products',$data);
});
//api
$app->get('/product/id-{id:[0-9]+}',function($id) use($app) {
    $uti = $app->utility;
    $uti->setSuccessTrue();
    $uti->setItem('product',getProductById($app,$id));
});
$app->get('/product/id-{id:[0-9]+}_image',function($id) use($app) {
    $uti = $app->utility;
    $uti->setSuccessTrue();
    $data = array();
    $data = getProductById($app,$id);
    $data['images'] = getProductImages($app,$id);
    $comments = getComments($app,$id);
    $data['comments'] = $comments;
    $user = getCurrentUser($app);
    if($user != null){
        $cm = getDBCommentMap($app,$user['id'],$id);
        if($cm!=false){
            $ufl = $cm->uploadFileLimit;
            if($ufl != false){
                $data['allowMakeComment'] = $cm===false?false:true;
                $data['upload_file_limit_id'] = $ufl->id;
            }

        }
    }
    $uti->setItem('product',$data);
});


//api
$app->get('/product/id-{id:[0-9]+}/image/all',function($id) use($app) {
    $uti = $app->utility;
    $uti->setSuccessTrue();
    $uti->setItem('images',getProductImages($app,$id));
});
//api

$app->get('/product/all_image',function() use($app) {
    $uti = $app->utility;
    $uti->setSuccessTrue();
    $pros = RjProduct::find(array(
        'public=:public:',
        'bind' => array(
            'public' => 1
        )
    ));
    $data = array();
    foreach($pros as $pro){
        $t = $pro->toArray();
        $app->utility->addFiles2Array($t,'images',$pro->images,'getImageUrl');
        $data[] = $t;
    }
    $uti->setItem('products',$data);
});
$app->post('/product/story/all',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    if(!isset($data->id)||!isset($data->hasContent)){
        $uti->addError(ERROR_JSON_HALFBAKED);
        return;
    }

    $res = RjStoryProduct::find(array('product_id=:pid:','bind'=>array(
        'pid' => $data->id
    )));
    $uti->setSuccessTrue();
    $t = array();
    foreach($res as $story){
        $t[] = $story->story_id;
    }
    $uti->setItem('stories',getStoriesByIdArray($app,$t,false,$data->hasContent,true));
});
