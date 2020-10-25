<?php

namespace WxSDK;

use WxSDK\core\common\IApp;
use WxSDK\core\model\AppModel;
use WxSDK\resource\Config;
use WxSDK\core\utils\Tool;
use WxSDK\core\common\Ret;

class WxApp implements IApp
{
    private $model;
    function __construct(AppModel $model = NULL){
        if(!$model){
            $filePath = $this->getFilePath("");
            if (file_exists($filePath)) {
                $data = file_get_contents($filePath);
            } else {
                return NULL;
            }
            $temp = json_decode($data);
            $appId = $temp->appId ? $temp->appId : null;
            $appSecret = $temp->appSecret ? $temp->appSecret : null;
            $token = $temp->token ? $temp->token : null;
            $tokenExpire = $temp->tokenExpire ? $temp->tokenExpire : null;
            $accessToken = $temp->accessToken ? $temp->accessToken : null;
            $encodingAesKey = $temp->encodingAesKey ? $temp->encodingAesKey : null;
            $appModel = new AppModel($appId, $appSecret, $accessToken, $tokenExpire, $token, $encodingAesKey,"");
            $this->model = $appModel;
        }else{
            $this->model = $model;
        }
    }

    /**
     * {@inheritDoc}
     * @see \WxSDK\core\common\IApp::getApp()
     */
    public function getModel()
    {
        return $this->model;
    }

    public function getFilePath($id)
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . "wx" . $id;
    }
    public function saveAccessToken()
    {
        $model = $this->model;
        $id = $model->id ? $model->id : '';
        $filename = $this->getFilePath($id);
        file_put_contents($filename, json_encode($model));
        $this->model = $model;
    }

    public function getId()
    {
        return $this->getModel()->id;
    }

    public function getAccessToken()
    {
        if($this->getModel()->isExpire()){
            $url = str_replace("APPID", $this->getModel()->getAppId(), Config::$app_get_access_token);
            $url = str_replace("APPSECRET", $this->getModel()->getAppSecret(), $url);
            $ret = Tool::doCurl($url);
            if($ret->ok()){
                $data = $ret->getData();
                $this->model->setToken($data["access_token"]);
                $this->model->setTokenExpire(time() + $data["expires_in"]-5);
                $this->saveAccessToken();
                $d = [
                    'errcode'=>0,
                    'msg'=>'成功',
                    'data'=>$data["access_token"],
                ];
                return new Ret("", $d);
            }else{
                return $ret;
            }
        }else{
            $d = [
                'errcode'=>0,
                'msg'=>'成功',
                'data'=>$this->getModel()->accessToken
            ];
            return new Ret('',$d);
        }
    }

}