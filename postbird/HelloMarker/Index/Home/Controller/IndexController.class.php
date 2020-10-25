<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        // header("Location:http://www.tsingwa.com/hellomarkertest/marker.php");
        header("Location:http://localhost/hellomarkertest/marker.php");
        // header("Location:http://10.202.205.16/hellomarker/marker.php");
    }
}