<?php

include_once "product.php";
include_once "story.php";
$app->get('/seo/index',function()use($app){
    $uti = $app->utility;
    $uti->openSEO('若简臻品');

    $cats = RjCategory::find(array('public=1'));
    $uti->SEODiv();
    $uti->SEOHtmlAppend('分类：');
    foreach($cats as $cat){
        $uti->SEOHtmlAppend('<h1>');
        $uti->SEOLink($cat->name,'/category/'.$cat->id);
        $uti->SEOHtmlAppend('</h1>');
    }
    $uti->SEODivEnd();

    $pros = RjProduct::find(array('public=:public: AND number>0','bind'=>array(
        'public'=>1
    )));
    $uti->SEODiv();
    $uti->SEOHtmlAppend('商品：');
    foreach($pros as $pro){
        $uti->SEODiv();
        $uti->SEODiv();
        $uti->SEOHtmlAppend('商品名称 ' . $pro->name);
        $uti->SEODivEnd();
        $uti->SEODiv();
        $uti->SEOHtmlAppend('价格 ￥' . $pro->price);
        $uti->SEODivEnd();
        $uti->SEODiv();
        $uti->SEOHtmlAppend('简介 ' . $pro->summary);
        $uti->SEODivEnd();
        $uti->SEODiv();
        $uti->SEOLink('详情','/product/'.$pro->id);
        $uti->SEODivEnd();
        $imgs = getProductImages($app,$pro->id);
        foreach($imgs as $img){
            $uti->SEOImg('若简臻品 '.$pro->name." ".$pro->summary,$img['url']);
            break;
        }
    }
    $uti->SEODivEnd();
    $uti->SEODiv();
    $uti->SEOHtmlAppend('故事：');
        $stos = RjStory::find(array('public = 1'));
        foreach($stos as $sto){
            $uti->SEODiv();
            $uti->SEOH1($sto->title);
            $uti->SEODivEnd();
            $uti->SEODiv();
            $uti->SEOHtmlAppend($sto->content);
            $uti->SEODivEnd();
            $uti->SEOLink($sto->title,'/story/'.$sto->id);
        }
    $uti->SEODivEnd();
});

$app->get('/seo/product/{id:[0-9]+}',function($id) use($app) {
    $pro = RjProduct::findFirst($id);
    $uti = $app->utility;
    $uti->openSEO('若简臻品 '.$pro->name);
    $uti->SEOLink('若简臻品','/index');
    $uti->SEODiv();
        $uti->SEODiv();
            $uti->SEOH1('商品名称：'. $pro->name);
        $uti->SEODivEnd();
        $uti->SEODiv();
            $uti->SEOHtmlAppend('价格：'.$pro->price);
        $uti->SEODivEnd();
        $uti->SEODiv();
            $uti->SEOHtmlAppend('详细信息：'.$pro->comment);
        $uti->SEODivEnd();
        $imgs = getProductImages($app,$pro->id);
        foreach($imgs as $img){
            $uti->SEOImg($pro->name,$img['url']);
        }
    $uti->SEODivEnd();
    $uti->SEODiv();
        $uti->SEOH1("相关故事：");
        $sts = $pro->stories;
        foreach($sts as $st){
            $uti->SEODiv();
            $uti->SEOLink($st->title,'/story/'.$st->id);
            $uti->SEODivEnd();
        }
    $uti->SEODivEnd();
});

$app->get('/seo/story/{id:[0-9]+}',function($id) use($app) {
    $uti = $app->utility;
    $sto = getStoryById($app,$id);
    $uti->openSEO('若简臻品 '. $sto['title']);
    $uti->SEOLink('若简臻品','/index');
    $imgs = getStoryImagesById($app,$sto['id']);
    $pros = getStoryProductsById($app,$sto['id']);
    $uti->SEODiv();
    $uti->SEODiv();
    $uti->SEOH1($sto['title']);
    $uti->SEODivEnd();
    $uti->SEODiv();
    $uti->SEOHtmlAppend($sto['content']);
    $uti->SEODivEnd();
    foreach($imgs as $img){
        $uti->SEOImg($sto['title'],$img['url']);
    }
    $uti->SEODivEnd();
    $uti->SEODiv();
    $uti->SEOH1("相关商品：");

    foreach($pros as $pro){
        $uti->SEODiv();
        $uti->SEOLink($pro['name'],'/product/'.$pro['id']   );
        $uti->SEODivEnd();
    }
    $uti->SEODivEnd();
});

$app->get('/seo/category/{id:[0-9]+}',function($id)use($app){
    $uti = $app->utility;
    $cat = getCategory($id);
    if($cat === false){
        $uti->openSEO('若简臻品');
        $uti->SEOHtmlAppend('没有这个分类');
        return;
    }
    $uti->openSEO('若简臻品 '. $cat->name);
    $uti->SEOLink('若简臻品','/index');
    $pros = RjProduct::find(array(
        'category_id=:cid: AND public=1',
        'bind' => array(
            'cid' => $id
        )
    ));
    $uti->SEODiv();
    foreach($pros as $pro){
        $uti->SEODiv();
            $uti->SEOHtmlAppend('<h1>');
                $uti->SEOLink($pro->name,'/product/'.$pro->id);
            $uti->SEOHtmlAppend('</h1>');
            $uti->SEOSpan();
                $uti->SEOHtmlAppend('价格： ￥'.$pro->price);
            $uti->SEOSpanEnd();
            $uti->SEOSpan();
                $uti->SEOHtmlAppend('简介： '.$pro->summary);
            $uti->SEOSpanEnd();
            $imgs=getProductImages($app,$pro->id);
            foreach($imgs as $img){
                $uti->SEOImg($pro->name,$img['url']);
            }
        $uti->SEODivEnd();
    }
    $uti->SEODivEnd();
});