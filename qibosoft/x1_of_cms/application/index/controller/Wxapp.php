<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use app\common\fun\Wxapp AS WxappFun;
use app\common\model\Shorturl AS ShorturlModel;

class Wxapp extends IndexBase
{
    /**
     * 接收小程序识别出来的二维码代码定向到指定网址
     * @param string $scan_str 小程序二维码中包含的特定代码
     * @param string $token 用户登录信息
     */
    public function scan($scan_str='fdsa',$token=''){
        $url = ShorturlModel::getUrl($scan_str);
        header("location:$url");
        exit;
    }
    
    /**
     * 生成小程序二维码
     * @param string $url
     */
    public function img($url=''){
        if ($url=='') {
            $this->error('地址不能为空');
        }
        $imgurl = WxappFun::wxapp_codeimg($url,$this->user['uid']);
        header("location:$imgurl");
        exit;
    }
}

?>