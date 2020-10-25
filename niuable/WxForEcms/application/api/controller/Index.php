<?php
namespace app\api\controller;

use app\api\service\AutoReply;
use think\Controller;
class Index extends Controller{
    public function index(){
        $reply = new AutoReply();
        $reply->run();
    }
}
