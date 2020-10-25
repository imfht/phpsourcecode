<?php
namespace WxSDK\core\common;
use WxSDK\resource\ErrCode;
class Ret{
    public $errCode;
    public $errMsg;
    public $data;
    public $response;
    /**
     * 初始化
     * @param string $response 访问的返回值
     * @param array $ret 自定义的值,如果不为空，则$response失效
     */
    function __construct(string $response, array $ret = NULL, int $errcode = null, string $errmsg = '', $data = NULL) {
        if($ret){
            $this->errCode = isset($ret['errcode']) ? $ret['errcode']:1;
            $this->errMsg = isset($ret['errmsg']) ? $ret['errmsg']:'未知提示';
            $this->data = isset($ret['data'])?$ret['data']:'';
        }else{
            if($errcode === NULL){
                $this->response = $response;
                $this->transRet($response);                                
            }else{
                $this->errCode = $errcode;
                $this->errMsg = $errmsg;
                $this->data = $data;
            }
        }
    }
    /**
     * wxErrCode
     * 解析微信返回的错误码/正确信息
     * @param String $response 微信返回的字符串
     * @return Ret 解析后的结果
     */
    private function transRet(string $response) {
        $res = json_decode ( $response, true);
        if (!isset($res['errcode']) || $res ['errcode'] === 0 || empty ( $res ['errcode'] )) {
            $this->errCode = 0;
            $this->errMsg = "成功";
            $this->data = $res;
        } else {
            $meaning = isset(ErrCode::$errCode[$res ['errcode']]) ? ErrCode::$errCode[$res ['errcode']]:NULL;
            $meaning = $meaning ? $meaning : $res ['errmsg'];
            $msg = "错误代码：" . $res ['errcode'] . "。意为：" . $meaning;
            $this->errCode = $res['errcode'];
            $this->data = $res;
            $this->errMsg = $msg;
        }
    }
    public function getCode() {
        return $this->errCode;
    }
    public function getMsg() {
        return $this->errMsg;
    }
    public function getData() {
        return $this->data;
    }
    public function ok() {
        return $this->errCode == 0;
    }
}