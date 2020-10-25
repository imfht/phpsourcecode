<?php

namespace app\exwechat\controller;

use youwen\exwechat\api\media\media;
use think\Controller;
/**
 * 微信交互控制器
 * @author baiyouwen <youwen21@yeah.net>
 */
class xxx extends Controller
{

    public $str = '{"openid":"o5dxUt4XMyD4R9jLrkeKhhFnMYKA","access_token":"-YLFNA86yLXcSYy3Orf2V6dEH1rx04UD4ooUIYA0DKA7sRPsUYNSqzEI1LDxQg-0VdRhlMuzWzDV-1KQgQnWfEX6af4u-NTHPEIWcFNnA-E","expires_in":7200,"refresh_token":"0hXiW343ST0v9pkMZ-f-vR7dAiUkK3t6dX31SzbWvwOgM86mrCLfuGsuLPy9IgcMR6LffDR2nKIphecUtcXG5oeOJkt3FGePChG5jio-Vk8","scope":"snsapi_base,snsapi_userinfo,"}';

    public $user = '{"openid":"o5dxUt4XMyD4R9jLrkeKhhFnMYKA","nickname":"x","sex":1,"language":"zh_CN","city":"x","province":"x","country":"x","headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/iaC5GjotEAsUD1TUib5w2bUR8sM
ZBKia87FKElrcbcMA4ckRWvl2bXibYqHcibyRsvzVg9QL14sVxt8icOsPeqMDPhFLibMMJ4SkUB7\/0","privilege":[],"unionid":"om7D5tyA66ndf22bwBut-yRrPONw"}';

    // 上传临时素材
    public function test()
    {
        // echo '<pre>';
        // print_r( urldecode($this->user) );
        // exit('</pre>');
        // $info = json_decode($this->user,true);
        // echo '<pre>';
        // print_r( $info );
        // exit('</pre>');
        $info['openid'] = 'x';
        $info['nickname'] = 'x';
        $info['sex'] = '1';
        $info['language'] = 'x';
        $info['city'] = 'x';
        $info['province'] = 'x';
        $info['country'] = 'x';
        $info['headimgurl'] = 'x';
        $info['privilege'] = 'x';
        $info['unionid'] = 'x';
        $this->_saveUserInfo($info);
    }

    private function _saveUserInfo($userinfo)
    {
        // $ret = db('oauth_userinfo')->insert($userinfo);
        $sql = 'REPLACE INTO think_oauth_userinfo SET ';
        $str = '';
        foreach ($userinfo as $key => $value) {
            if(is_array($value)){
                $value = json_encode($value);
            }
            $str .= " `$key`='$value',";
        }
        $str = rtrim($str, ',');
        $sql .= $str;
        echo '<pre>';
        print_r( $sql );
        exit('</pre>');
        $ret = db('oauth_userinfo')->execute($sql);
        return $ret;
    }

    public function ip()
    {
        $ip = '101.226.103.28';
        if($this->_in_ips($ip)){
            echo '<pre>';
            print_r( 'yes' );
            exit('</pre>');
        }
        exit('no');
    }

    private function _in_ips($ip)
    {
        $num = strrpos($ip, '.');
        $prefix = substr($ip, 0, strrpos($ip, '.'));
        $postfix = substr($ip, strrpos($ip, '.')+1);
        foreach ($this->ips_list as $value) {
            if($prefix == substr($value, 0, $num)){
                $arr = explode('/', substr($value, $num+1));
                sort($arr, SORT_NUMERIC);
                if( $arr[1] > $postfix && $postfix > $arr[0] || $arr[1]==$postfix || $arr[0] == $postfix){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return false;
    }

    private $ips_list =[
        90 => '101.226.103.0/25',
        91 => '101.226.233.128/25',
        92 => '58.247.206.128/25',
        93 => '182.254.86.128/25',
        95 => '103.7.30.64/26',
        96 => '58.251.80.32/27',
        97 => '183.3.234.32/27',
        98 => '121.51.130.64/27'
    ];
}
