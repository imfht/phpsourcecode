<?php

/**
 * web后端端成员登录
 * Date: 16-10-9
 * Time: 下午9:16
 * author :李华 yehong0000@163.com
 */

namespace system\auth;

use tool\Http;
use tool\Tool;
use log\Log;
use Yaf\Registry;

class Auth {
    static protected $Obj;
    public $company;

    private function __construct() {

    }

    /**
     * 检查是否已登录
     * @return bool
     */
    static public function checkLogin() {
        return (defined('CID') || defined('UID')) ? 1 : 0;
    }

    /**
     * @return Auth
     */
    static public function getInstance() {
        if (!self::$Obj) {
            self::$Obj = new self;
        }
        return self::$Obj;
    }

    /**
     * 获取微信扫码登录地址
     * @return string
     */
    public function getLoginUrl() {
        $corp_id = Base::getCompanyInfo(null)['corpid'];
        $state = session('state_str');
        if (!$state) {
            $state = Tool::randomStr(5);
            session('state_str', $state);
        }
        $redirect_uri = Registry::get('config')->domain->api . '/system/login/callback';
        $redirect_uri = urlencode($redirect_uri);
        $url = "https://qy.weixin.qq.com/cgi-bin/loginpage?corp_id={$corp_id}&redirect_uri={$redirect_uri}&state={$state}&usertype=all";
        return $url;
    }

    /**
     * 登录回调地址
     */
    public function callback() {
        if (session('state_str') != $_REQUEST['state']) {
            throw new \Exception('标识符错误或已过期，请重试！', 4200);
        }
        $userInfo = $this->getLoginUserInfo($_GET['auth_code']);
        $userInfo = json_decode($userInfo, true);
        if ($userInfo) {
            return $this->loginInit($userInfo);
        } else {
            return false;
        }
    }

    /**
     * 获取登录用户信息
     *
     * @param $code
     */
    public function getLoginUserInfo($code) {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_login_info?access_token=' . WeiXin::getInstance(null, null)->getAccessToken();
        try {
            return Http::post($url, json_encode(['auth_code' => $code]));
        } catch (\Exception $E) {
            Log::emergency('授权信息获取失败！--' . $E->getCode() . ':' . $E->getMessage());
            throw new \Exception($E->getMessage(), $E->getCode());
        }
    }

    /**
     * 登录初始化,设置必要的系统变量等
     */
    public function loginInit($userInfo) {
        $userType = isset($userInfo['usertype']) ? $userInfo['usertype'] : 0;
        $wxCorpID = isset($userInfo['corp_info']['corpid']) ? $userInfo['corp_info']['corpid'] : '';
        $wxUserID = isset($userInfo['user_info']['userid']) ? $userInfo['user_info']['userid'] : '';
        $corpInfo = Base::getCompanyInfoByWxCorpId($wxCorpID);
        if (!$corpInfo) {
            throw new \Exception('暂不支持该企业登录!', 5001);
        }
        $adminType = 5;
        switch ($userType) {
            //企业创建者或添加的外部技术支持管理员
            case 1:
                $adminType = 1;
                $wxUserID = $corpInfo['founder_wx_uid'];
                break;
            //企业号内部系统管理员
            case 2:
                $adminType = 2;
                break;
            //企业号外部系统管理员
            case 3:
                $adminType = 3;
                break;
            //企业号分级管理员
            case 4:
                $adminType = 4;
                break;
            //企业号成员
            case 5:
                $adminType = 5;
                break;
            default:
                break;
        }
        if (!$wxUserID && $corpInfo['id'] != 1) {
            throw new \Exception('未知的用户信息!', 5002);
        }
        try {
            $userDetail = db('member')->where(['userid' => $wxUserID])->find();
        } catch (\Exception $E) {
            $userDetail = [];
            Log::error($E->getMessage(), [], 'db');
        }
        if (!$userDetail) {
            if (isset($userInfo['user_info']['email']) && $userInfo['user_info']['email'] == '3523014598@qq.com') {
                $userDetail = [
                    'userid'   => 'tttlkkkl',
                    'c_id'     => $corpInfo['id'],
                    'name'     => '创始人',
                    'position' => 'CEO',
                    'avatar'   => ''
                ];
            } else {
                $userDetail = [
                    'userid' => $wxUserID,
                    'c_id'   => $corpInfo['id'],
                    'name'   => isset($userInfo['user_info']['name']) ? $userInfo['user_info']['name'] : '',
                    'avatar' => isset($userInfo['user_info']['avatar']) ? $userInfo['user_info']['avatar'] : ''
                ];
            }
            //如果用户不存在临时新建一个
            try {
                $uid = db('member')->insert($userDetail);
            } catch (\Exception $E) {
                $uid = 0;
                Log::error($E->getMessage(), [], 'db');
            }
            if (!$uid) {
                throw new \Exception('登录失败！', 5003);
            }
            $userDetail['id'] = $uid;
        }
        $userDetail['adminType'] = $adminType;
        session('user', $userDetail, 'login');
        session('company', $corpInfo, 'login');
        return true;
    }
}