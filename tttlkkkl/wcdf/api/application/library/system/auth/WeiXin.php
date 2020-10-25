<?php
/**
 * 微信授权类
 * Date: 16-10-8
 * Time: 下午8:51
 * author :李华 yehong0000@163.com
 */
namespace system\auth;

use log\Log;
use tool\Http;
class WeiXin
{
    public $aOption = array();
    public $CID     = null;
    public $AID     = null;
    public $wHelper = null;
    public $agentid = null;
    public $access_token = null;
    public $secrect;
    public $corpid;
    protected static $Obj;

    /**
     * WorkWeiXin constructor.
     * @param $cid 企业标识
     * @param $aid 应用标识
     */
    public function __construct($cid,$aid)
    {
        $this->CID=$cid;
        $this->AID=$aid;
    }

    /**
     * 获取实例
     * @param $cid
     * @param $aid
     * @return WeiXin
     */
    static public function getInstance($cid,$aid)
    {
        $key=implode('_',func_get_args());
        if(!self::$Obj[$key]){
            self::$Obj[$key]=new self($cid,$aid);
        }
        return self::$Obj[$key];
    }

    public function getSecrect()
    {
        if(!$this->secrect){
            if($this->AID){
                //TODO
            }else{
                $this->secrect=Base::getCompanyInfo(null)['corpsecret'];
            }
        }
        return $this->secrect;
    }
    /**
     * 获取access_token
     */
    public function getAccessToken()
    {
        $corpid=Base::getCompanyInfo($this->CID)['corpid'];
        $secrect=$this->getSecrect();
        if(cache($corpid)){
            return cache($corpid);
        }
        $url='https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$corpid.'&corpsecret='.$secrect;
        $data=json_decode(Http::get($url,array(),$header),true);
        if($data['errcode']==0) {
            cache($corpid, $data['access_token'], 7100);
            return $data['access_token'];
        }else{
            Log::emergency('授权信息获取失败！返回：'.json_encode($data));
            throw new \Exception('授权信息获取失败!',$data['errcode']);
        }
    }
}