TpWechat
================

本项目来源于[wechat-php-sdk项目](https://github.com/dodgepudding/wechat-php-sdk)方便在[Thinkphp框架](http://www.thinkphp.cn/)下开发微信应用,做了点命名空间的小改进.直接扔到ThinkPHP\Library下就可以开始用了.
class Wechat 无缓存设置公众号类    
class TpWechat thinkphp缓存公众号类    
class ErrCode 公众号返回码和信息类    
Class QyWechat 无缓存设置企业号类    
class TpqyWechat thinkphp缓存企业号类    
class QyerrCode 企业号返回码和信息类    

    <?php
    namespace Home\Controller;
    use Think\Controller;
    use Wechat\TpqyWechat;
    
    class DemoController extends Controller {    
        public function index(){
            $options = array(
                    'appid'=>'appidxxxxxxx', //填写高级调用功能的app id
                    'appsecret'=>'appsecretxxxxxxxxxx', //填写高级调用功能的密钥
                    'debug'=>TRUE, //调试开关
                    'logcallback'=>'logg', //调试输出方法，需要有一个string类型的参数
            );
            $tpqy = new TpqyWechat($options);
            $rt = $tpqy->checkAuth();
            dump($tpqy->errCode);
            dump($tpqy->errMsg); 
    }

