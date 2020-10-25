<?php
namespace Home\Controller;

use TopSDK\Api\TopApi;
class ItemController extends HomeController {

    function __construct() {
        parent::__construct();
    }

    public function index() {
       
    }
    public function getTbkItem(){
        $taobao = new TopApi(C('APP_KEY'), C('APP_SECRET'));
        $result = $taobao->getItem("女装");
    }
    
}