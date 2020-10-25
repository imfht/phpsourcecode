<?php
namespace app\common\model;

use WxSDK\core\common\IApp;
use WxSDK\core\model\AppModel;
use WxSDK\core\common\Ret;
use WxSDK\resource\Config;
use WxSDK\core\utils\Tool;
use app\common\model\WxWx;

class WxApp implements IApp
{
    private $model;
    function __construct(int $id){
        $WxWx = new WxWx();
        $r = $WxWx->get($id);
        $this->model = new AppModel($r['app_id'], $r['app_secret'], $r['access_token']
            , $r['token_expire'], $r['token'], $r['encoding_aes_key'], $id);
    }
    public function saveAccessToken()
    {
        //更新数据库中的access_token 相关信息
        $WxWx = new WxWx();
        $WxWx->update([
            'access_token'=>$this->model->accessToken,
            'access_token_time'=>time(),
            'token_expire'=>$this->model->tokenExpire
        ],['id'=>$this->model->id]);
    }

    public function getId()
    {
        return $this->getModel()->id;
    }

    public function getAccessToken()
    {
        $isExpire = $this->getModel()->isExpire();
        if(!$isExpire){
            $d = [
                'errcode'=>0,
                'errmsg'=>'成功',
                'data'=>$this->getModel()->accessToken
            ];
            return new Ret('',$d);
        }
        
        $url = str_replace("APPID", $this->getModel()->getAppId(), Config::$app_get_access_token);
        $url = str_replace("APPSECRET", $this->getModel()->getAppSecret(), $url);
        $ret = Tool::doCurl($url);
        if($ret->ok()){
            $data = $ret->getData();
            $model = $this->getModel();
            $model->setToken($data["access_token"]);
            $model->setTokenExpire(time() + $data["expires_in"]-5);
            $this->saveAccessToken();
            $d = [
                'errcode'=>0,
                'errmsg'=>'成功',
                'data'=>$data["access_token"],
            ];
            return new Ret("", $d);
        }else{
            return $ret;
        }

    }
    public function getModel()
    {
        return $this->model;
    }

}

