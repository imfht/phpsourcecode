<?php

function getCategories($app){
    $cates = RjCategory::find("public=1");
    $data = array();
    foreach($cates as $cate){
        $t = array(
            'id' => $cate->id,
            'name' => $cate->name
        );
        if($cate->upload_file_name!=null){
            $t['image'] = array(
                'upload_file_name' => $cate->upload_file_name,
                'url' => getImageUrl($cate->upload_file_name)
            );
        }
        $data[] = $t;
    };
    return $data;
}
function getCategory($id){
    return RjCategory::findFirst(array('id=:id:','bind'=>array(
        'id' => $id
    )));
}
function getCategoryImage($app,$cid){
    $c = RjCategory::findFirst(array(
        'id=:cid:',
        'bind' => array(
            'cid' => $cid
        )
    ));
    if($c === false){
        return null;
    }
    if($c->upload_file_name == null) {
        return null;
    }
    return array(
        'upload_file_name' => $c->upload_file_name,
        'url' => getImageUrl($c->upload_file_name)
    );
}
//api

$app->get('/category/all',function() use($app) {
    $uti = $app->utility;
    $uti->setSuccessTrue();
    $uti->setItem('categories',getCategories($app));
});
$app->post('/category/image',function()use($app){
    $uti = $app->utility;
    $data = getPostJsonObject();
    $image = getCategoryImage($app,$data->id);
    $uti->setSuccessTrue();
    $uti->setItem('image',$image);
});
$app->get('/category/product/category_id-{id:[0-9]+}_limit-{limit:[0-9]+}',function($id,$limit) use($app) {
    $uti = $app->utility;
    $uti->setSuccessTrue();
    $arr = array('category_id = :id: AND public=1 AND number>0','bind'=>array(
        'id' => $id
    ));
    $arr['limit'] = $limit;
    if($limit <=0 || $limit>100){
        $arr['limit'] = 10;
    }
    $pros = RjProduct::find($arr);
    $data = $uti->getDBResultArrays($pros);
    foreach($data as &$pro){
        $pro['images'] = getProductImages($app,$pro['id']);
    }
    $uti->setItem('products',$data);
});
$app->get('/category/id-{id:[0-9]+}',function($id)use($app){
   $uti = $app->utility;

    $cat = RjCategory::findFirst(array('public=1 AND id=:id:','bind' => array(
        'id' => $id,
    )));
    if($cat == false){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }
    $cat = $cat->toArray();

    $cat['image'] = array(
        'name' => $cat['upload_file_name'],
        'url' => getImageUrl($cat['upload_file_name'])
    );


    $uti->setItem('category',$cat);
    $uti->setSuccessTrue();
});