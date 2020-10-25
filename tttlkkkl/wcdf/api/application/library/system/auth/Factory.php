<?php

/**
 * 模块接口列表
 * Date: 16-10-24
 * Time: 上午4:16
 * author :李华 yehong0000@163.com
 */

namespace system\auth;

use log\Log;
use system\auth\OAuth;
use Yaf\Registry;
use system\auth\Auth;

class Factory
{
    public static function __callStatic($name, $arguments)
    {
        throw new \Exception('Bad Request',400);
    }

    /**
     * 获取授权信息
     *
     * @param $id
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getAuth()
    {
        $result = Auth::getInstance()->checkLogin();
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        session('login', 0);
        return [
            'result'  => $result,
            'user'    => user(),
            'authUrl' => $result ? '' : $redirect_uri = Registry::get('config')->domain->api . '/system/login/login'
        ];
    }

    /**
     * 退出登录
     *
     * @return bool
     * @throws \Exception
     */
    public static function deleteAuth()
    {
        session_unset();
        session_destroy();
        return true;
    }

    /**
     * 手机授权信息
     *
     * @return array
     */
    public static function getOAuth()
    {
        $result = OAuth::getInstance()->checkLogin();
        return [
            'result' => $result,
            'user'   => user()
        ];
    }

    /**
     * 尝试授更新手机端权信息
     *
     * @param $data
     *
     * @return array
     */
    public static function putOAuth($data)
    {
        $corp = isset($data['corp']) ? $data['corp'] : '';
        $app = isset($data['app']) ? intval($data['app']) : 0;
        if (!$corp || $app <= 0) {
            throw new \Exception('参数缺失', 4001);
        }
        if (!ctype_alnum($corp)) {
            throw new \Exception('错误的企业标识', 4003);
        }
        $result = OAuth::getInstance()->checkLogin();
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        session('corp', $corp, 'oauth');
        session('app', $app, 'oauth');
        return [
            'result'  => $result,
            'user'    => user(),
            'authUrl' => $result ? '' : OAuth::getInstance()->getOAuthUrl($corp, $app),
            'jumpUrl' => $result ? OAuth::getInstance()->getJumpUrl() : ''
        ];
    }

    /**
     * 授权
     *
     * @param $data
     *
     * @throws \Exception
     */
    public static function postOAuth($data)
    {
        $code = isset($data['code']) ? $data['code'] : '';
        $state = isset($data['state']) ? $data['state'] : '';
        if (!$code || !$state) {
            throw new \Exception('参数缺失', 4006);
        }
        if (!ctype_alnum($state)) {
            throw new \Exception('非法的参数state', 4005);
        }
        return OAuth::getInstance()->oAuth($code, $state);
    }
}