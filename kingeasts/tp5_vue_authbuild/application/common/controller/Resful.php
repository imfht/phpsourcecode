<?php
// +----------------------------------------------------------------------
// | hr
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


namespace app\common\controller;

use think\App;
use think\Container;
use think\Request;

class Resful
{
    /**
     * @var Request $request
     */
    protected $request;

    /**
     * @var App
     */
    protected $app;

    public function __construct()
    {
        $this->app = Container::get('app');
        $this->request = Container::get('request');
    }


    /**
     * 快捷调用验证器
     * @param array $data 验证数据
     * @param mixed $rule 验证规则
     * @param string $scene 验证场景
     * @param array $msg 错误消息
     * @return array|bool
     */
    protected function validate($data, $rule, $scene = '', $msg = [])
    {
        if (is_array($rule)) {
            $valid = $this->app->validate();
            $valid->rule($rule);
        } else {
            $valid = $this->app->validate($rule);
        }
        if ($scene) {
            $valid->scene($scene);
        }
        $valid->message($msg);

        if ($valid->check($data)) {
            return true;
        } else {
            return $valid->getError();
        }
    }


    /**
     * 返回错误接口数据
     * @param string $msg
     * @param int $errCode
     * @return \think\response\Json
     */
    protected function error($msg, $errCode = 200)
    {
        $result = ['error'=>$msg];
        return json($result, $errCode);
    }

    /**
     * 返回成功接口数据
     * @param array $data
     * @param integer $code
     * @return \think\response\Json
     */
    protected function success($data = [], $code = 200)
    {
        $result = ['code'=>1];
        if (!empty($data)) {
            $result = array_merge($result, $data);
        }
        return json($result, $code);
    }



}