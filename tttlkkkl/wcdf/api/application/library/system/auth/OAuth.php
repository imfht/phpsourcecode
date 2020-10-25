<?php
/**
 * 手机OAuth
 * Date: 17-5-6
 * Time: 下午1:24
 * author :李华 yehong0000@163.com
 */

namespace system\auth;

use Yaf\Registry;
use tool\Http;
use log\Log;
use tool\Tool;

class OAuth
{
    static protected $Obj;

    private function __construct()
    {

    }

    /**
     * 检查是否已登录
     * @return bool
     */
    public static function checkLogin()
    {
        return (defined('CID') || defined('UID')) ? 1 : 0;
    }

    /**
     * @return OAuth
     */
    static public function getInstance()
    {
        if (!self::$Obj) {
            self::$Obj = new self;
        }
        return self::$Obj;
    }

    /**
     * 获取授权地址
     *
     * @param $corp 企业标识
     * @param $app  应用标识
     *
     * @return string
     */
    public function getOAuthUrl($corp, $app)
    {
        $corpInfo = Base::getCompanyInfoByWxCorpId($corp);
        if (!$corpInfo) {
            throw new \Exception('系统暂不支持该企业', 4002);
        }
        $state = session('state_str');
        if (!$state) {
            $state = Tool::randomStr(5);
            session('state_str', $state);
        }
        $redirect_uri = Registry::get('config')->domain->root . '/mobile';
        $redirect_uri = urlencode($redirect_uri);
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize';
        $url .= "?appid={$corpInfo['corpid']}&redirect_uri={$redirect_uri}&response_type=code";
        $url .= "&scope=snsapi_base&state={$state}#wechat_redirect";
        return $url;
    }

    /**
     * 执行授权
     *
     * @param $code
     * @param $state
     */
    public function oAuth($code, $state)
    {
        if ($this->checkLogin()) {
            return $this->getJumpUrl();
        }
        if (session('state_str') !== $state) {
            throw new \Exception('请求过期，请重新操作', 4100);
        }
        $wxCorpID = session('corp', '', 'oauth');
        if (!$wxCorpID) {
            throw new \Exception('企业信息丢失', 4102);
        }

        $userInfo = json_decode($this->getUserByCode($code), true);
        $wxUserID = isset($userInfo['UserId']) ? $userInfo['UserId'] : '';

        $deviceId = isset($userInfo['DeviceId']) ? $userInfo['DeviceId'] : '';
        if (!$wxUserID) {
            throw new \Exception('未知的用户信息', 4101);
        }
        $userDetail = db('member')->where(['userid' => $wxUserID])->find();
        if (!$userDetail) {
            throw new \Exception('用户信息未同步，无法创建会话', 40103);
        }
        $corpInfo = Base::getCompanyInfoByWxCorpId($wxCorpID);
        $userDetail['adminType'] = 5;
        $userDetail['deviceId'] = $deviceId;
        session('user', $userDetail, 'login');
        session('company', $corpInfo, 'login');
        return $this->getJumpUrl();
    }

    /**
     * 获取登录用户信息
     *
     * @param $code
     */
    public function getUserByCode($code)
    {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo';
        try {
            return Http::get($url, [
                'access_token' => WeiXin::getInstance(null, null)->getAccessToken(),
                'code'         => $code
            ]);
        } catch (\Exception $E) {
            Log::emergency('OAuth授权信息获取失败!' . $E->getCode() . ':' . $E->getMessage());
            throw new \Exception($E->getMessage(), $E->getCode());
        }
    }

    /**
     * 获取登录成功后的跳转地址
     *
     * @return string
     */
    public function getJumpUrl()
    {
        $app = session('app', '', 'oauth');
        $app = $app ?: 1;
        //应用地址映射
        $appMapping = [
            '',
            'work'
        ];
        return Registry::get('config')->domain->root .'/mobile/'. ($appMapping[$app] ?: '/work');
    }
}