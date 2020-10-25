<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/3/26 13:24
// +----------------------------------------------------------------------
// | TITLE: 业务基础类
// +----------------------------------------------------------------------
namespace app\first\controller;

use DawnApi\facade\ApiController;
use think\facade\Request;
use think\facade\Lang;
use Blocktrail\CryptoJSAES\CryptoJSAES;

class Base extends ApiController
{

    /**
     * 允许访问的请求类型
     * @var string
     */
    public $restMethodList = 'get|post|put|delete|patch|head|options';
    public $restActionList=['index','get','create','save','read','edit','update','delete','post'];
    //是否开启授权认证
    public  $apiAuth = true;

    public function __construct(Request $request = null)
    {
        ini_set('memory_limit', '-1');
        parent::__construct($request);
        $this->site = db('config')
            ->field('title,logo,company,keywords,description,conact,url,address')
            ->find();
        Lang::setAllowLangList(['zh-cn','en-us']);
    }
    /**
     * 获取密码
     * @param $pwd
     * @param int $type
     * @return bool|string
     */
    protected function get_password($pwd,$type=0)
    {
        return $type?substr($pwd, 10, 15):substr(md5($pwd), 10, 15);
    }
    /**
     * 检测验证码
     * @param string $verify
     * @param bool $clear
     * @return array
     */
    protected function check_verify($verify='', $clear=false)
    {
        $d = cookie($verify.'_session_code');
        $f = cookie('?'.$verify.'_session_code');
        if (!$f) {
            return ['status'=>0,'msg'=>lang('verify_expires')];
        }
        if ($verify!=$d) {
            return ['status'=>0,'msg'=>lang('error',[lang('verify')])];
        }else{
            if ($clear) {
                cookie($verify.'_session_code', null, time()-60*2);
            }
            return ['status'=>1,'msg'=>lang('success',[lang('verify')])];
        }
    }

    /**
     * 获取secret
     * @return string
     */
    protected function getSecret()
    {
        $random = new \Rych\Random\Random();
        $text = $random->getRandomString(32,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        //$secret =  CryptoJSAES::encrypt($text, config('api.passphrase'));
        return $text;
    }

    /**
     * 参数规则
     * @name 字段名称
     * @type 类型
     * @require 是否必须
     * @default 默认值
     * @desc 说明
     * @range 范围
     * @return array
     */
    final static function getRules() {
        return [];
    }
}