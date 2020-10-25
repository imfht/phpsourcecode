<?php


include_once 'constants.php';
include_once 'common.php';
include_once 'ICODE.php';
include_once 'product.php';
include_once 'category.php';
include_once 'user.php';
include_once 'upload.php';
include_once "cart.php";
include_once "order.php";
include_once "address.php";
include_once "admin.php";
include_once "MCODE.php";
include_once "message.php";
include_once "story.php";
include_once "SEO.php";
include_once "marketing.php";
include_once "alipay/alipay.php";
include_once "test.php";
include_once "survey.php";

$app->before(function()use($app){

	cors($app);
   $app->utility;
});

$app->finish(function()use($app){
   $app->utility->finish();
});
