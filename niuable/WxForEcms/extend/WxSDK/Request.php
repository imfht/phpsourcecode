<?php
namespace WxSDK;
use WxSDK\core\model\Model;
use WxSDK\core\utils\Tool;
use WxSDK\core\common\IApp;
use WxSDK\core\common\IApiUrl;

class Request{
    private $model;
    private $url;
    private $App;
    /**
     * 
     * @param IApp $App App获取示例
     * @param Model $model post参数模型
     * @param IApiUrl $url api链接构造器
     */
    function __construct(IApp $App, Model $model, IApiUrl $url) {
        $this->model = $model;
        $this->url = $url;
        $this->App = $App;
    }
    /**
     * 
     * @return \WxSDK\core\common\Ret
     */
    public function run(){
        $ret = $this->App->getAccessToken();
        if($ret->ok()){
            $accessToken = $ret->getData();
            return Tool::doCurl($this->url->getUrl($accessToken), $this->model->getPostData(), $this->model->hasMedia());
        }else{
            return $ret;
        }
    }
}