<?php
namespace Home\Controller;
use Think\Controller;
class IconController extends Controller {
    public function download(){
        //输出一张测试图
        // $file = 'http://my.illustrations.com/Uploads/image/2019-06-01/5cf21926d01b4.svg';
        $src = I('post.src');
        $color = I('post.color');
        $type = I('post.type');

        $file = svg($src);
        var_dump($file);exit;
    }
}