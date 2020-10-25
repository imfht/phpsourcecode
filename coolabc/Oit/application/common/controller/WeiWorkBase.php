<?php
namespace app\common\controller;

use app\common\api\Common;
use app\common\api\Para;
use app\common\api\RBAC;
use think\Controller;
use think\Db;
use think\Log;
use think\Request;
use think\Config;
use think\Session;
use think\WeChat;

/**
 * Class WeiWorkBase
 * @package app\common\controller
 */
class WeiWorkBase extends Controller {

    const OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
    const OAUTH_AUTHORIZE_URL = '/authorize?';

    /**
     * 控制器初始化
     */
    public function _initialize() {
        // todo::将缓存使用redis,客户端每次访问时,都重置失效时间
        Para::system_const_variable();
        if (ACTION_NAME == 'api') {
            // 放置在最前面,使企业微信api验证时通过
            return;
        }
        if (!is_weixin()) {
            exit(lang("暂时只支持在微信浏览器中使用"));
        }

        $config = Config::get('work_wechat');
        $this->agent = WeChat::agent(CONTROLLER_NAME);

        $user_info_weiwork = session('user_info_weiwork');
        // 页面访问
        if (IS_AJAX) {
            // 有缓存用户信息并且未过期,或者获取页面片段
            if (!empty($user_info_weiwork) || IS_GET) {
                return;
            }
            if (IS_POST) {
                // 当客户端通过ajax, post方式，提交或获取数据的时候，
                // 服务器会判断session缓存是否还存在，如果不存在，就返回给客户端提示
                // 客户端重新打开url主页，获取用户信息并缓存,
                // 约定，easyui 使用get方式 获取页面片段，使用post 传递数据
                $this->error(lang('登陆缓存超时,请关闭重新登陆或打开页面'), url('index'));
            }
        }
        // 初次访问
        if (empty($user_info_weiwork)) {
            $code = input("get.code");
            if (empty($code)) {
                $this->redirect($this->getOauthRedirect(url("index", "", "", true), $config['corp_id'], $config[CONTROLLER_NAME]['agent_id']));
            }

            // 根据code获取成员信息并缓存cookie与session信息
            $OAuth = $this->agent->OAuth;
            $user_info = $OAuth->getUserInfo($_GET['code']);

            $contacts = WeChat::agent('contacts');
            $user = $contacts->user;
            $user_info_weiwork = $user->get($user_info['UserId']);
            session('user_info_weiwork', $user_info_weiwork);  // 企业微信中通讯录中的资料
        }
        // JS-SDK 配置
        $JSApi = $this->agent->JSApi;

        // 页面共同数据
        $this->assign('jsapi', $JSApi->sign());
        $this->assign('user_info_weiwork', json_encode($user_info_weiwork));
    }

    /**
     * 扩展支持模板风格
     * @param string $template
     * @param array  $vars
     * @param array  $replace
     * @param array  $config
     * @return string
     */
    protected function fetch($template = '', $vars = [], $replace = [], $config = []) {
        // pc 或 mobile
        if (is_mobile()) {
            $view_type = 'mobile';
        } else {
            $view_type = 'pc';
        }

        $this->view->config('view_path', APP_PATH . request()->module() . '/view/' . $view_type . '/');
        return $this->view->fetch($template, $vars, $replace, $config);
    }

    /**
     * oauth 授权跳转接口
     * @param string $callback 回调URI
     * @return string
     */
    public function getOauthRedirect($callback, $corp_id = '', $agent_id, $state = '', $scope = 'snsapi_userinfo') {
        return self::OAUTH_PREFIX . self::OAUTH_AUTHORIZE_URL . 'appid=' . $corp_id . '&redirect_uri=' . urlencode($callback) . '&response_type=code&scope=' . $scope . '&agentid=' . $agent_id . '&state=' . $state . '#wechat_redirect';
    }

}
