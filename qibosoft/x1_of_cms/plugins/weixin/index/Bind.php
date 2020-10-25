<?php
namespace plugins\weixin\index;

use app\common\controller\IndexBase;
use app\common\fun\Wxapp;

//绑定公众号
class Bind extends IndexBase
{
    public function index($url=''){
        if ($url!='' && !strstr($url,'http')) {
            $url = get_url($url);
        }
        $img = Wxapp::bind($url);
        if (preg_match('/\.png$/', $img)) {
            header("location:".$img);
            exit;
        }else{
            return $img;
        }
    }
}