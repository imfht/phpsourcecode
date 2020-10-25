<?php

/**
 * 会员移动控制器
 */

namespace app\member\mobile;

class MemberMobile extends \app\base\mobile\SiteMobile {

    protected $userInfo = [];
    protected $action = '';
    protected $noLogin = false;
    protected $topMenu = '';
    protected $memberInfo = [];
    protected $userCss = [];
    protected $userJs = [];
    protected $tplInfo = [];

    public function __construct() {
        parent::__construct();
        $this->initLogin();
        $this->action = request('get', 'action');
        if ($this->action) {
            $this->assign('action', $this->action);
        }
        $this->memberInfo = target('member/MemberInfo')->getConfig();
        $this->assign('memberInfo', $this->memberInfo);
    }

    /**
     * 初始化登录状态
     */
    protected function initLogin() {
        $userInfo = $this->getLogin();
        if(!$userInfo) {
            $uid = request('get', 'uid', 0, 'intval');
            $token = request('get', 'token');
            if ($uid && $token) {
                $login = [
                    'uid' => $uid,
                    'token' => $token
                ];
                \dux\Dux::cookie()->set('user_login', $login);
                if (!isAjax()) {
                    $this->redirect(url('', array_merge(request('get'), ['uid' => '', 'token' => ''])));
                } else {
                    $this->error('登录成功，请刷新页面!', url('', ['uid' => '', 'token' => '']));
                }
            }
        }
        if (!$this->noLogin && !$userInfo) {
            if (!isAjax()) {
                $this->redirect(url('member/Login/index', ['action' => URL]));
            } else {
                $this->error('您尚未登录,请先登录进行操作!', url('member/Login/index'), 401);
            }
        }
        $this->userInfo = $userInfo;
        define('USER_ID', $userInfo['user_id']);
        $this->assign('userInfo', $this->userInfo);
    }

    /**
     * 获取登录信息
     * @return bool
     */
    protected function getLogin() {
        $login = \dux\Dux::cookie()->get('user_login');
        if (empty($login)) {
            return false;
        }
        if (!target('member/MemberUser')->checkUser($login['uid'], $login['token'])) {
            return false;
        }
        $info = target('member/MemberUser')->getUser($login['uid']);
        if(!$info) {
            $this->error(target('member/MemberUser')->getError());
        }
        return $info;
    }

    /**
     * 系统模板输出
     * @param string $tpl
     * @param bool $header
     * @param array $headNav
     */
    protected function memberDisplay($tpl = '', $header = true) {
        $theme = $this->siteConfig['tpl_name'] . '_mobile';

        $actionName = ACTION_NAME == 'index' ? '' : '_' . ACTION_NAME;
        $userTpl =  strtolower( $tpl ? $tpl : MODULE_NAME . $actionName);
        $tplDir = 'theme/' . $theme . '/' . strtolower(APP_NAME) . '_';
        if (is_file(ROOT_PATH . $tplDir . $userTpl . '.html')) {
            $this->mobileDisplay($userTpl);
            exit;
        }

        $systemFile = ROOT_URL . '/public/member/css/style_mobile.css';

        $cssDir = 'public/' . APP_NAME . '/css/style_mobile.css';
        if (is_file(ROOT_PATH . $cssDir)) {
            $this->userCss[] = ROOT_URL . '/' . $cssDir;
        }

        if(!in_array($systemFile, $this->userCss)) {
            $this->userCss[] = $systemFile;
        }

        $cssDir = 'theme/' . $theme . '/css/member.css';
        if (is_file(ROOT_PATH . $cssDir)) {
            $this->userCss[] = ROOT_URL . '/' . $cssDir;
        }

        $jsDir = 'public/' . APP_NAME . '/js_mobile/lib.js';
        if (is_file(ROOT_PATH . $jsDir)) {
            $this->userJs[] = ROOT_URL . '/' . $jsDir;
        }

        $this->layout = 'app/member/view/' . LAYER_NAME . '/common/common';
        if (!empty($tpl)) {
            $tpl = 'app/' . APP_NAME . '/view/' . LAYER_NAME . '/' . strtolower(MODULE_NAME) . '/' . strtolower($tpl);
        }

        $this->assign('header', $header);
        $this->assign('sysPublic', $this->publicUrl);
        $this->assign('site', $this->siteConfig);
        $this->assign('userCss', $this->userCss);
        $this->assign('userJs', $this->userJs);
        $this->assign('tplInfo', $this->tplInfo);
        $this->display($tpl);
        exit;
    }

    /**
     * 其他模板输出
     */
    protected function otherDisplay($tpl = '', $autoDir = true) {
        $this->layout = 'app/member/view/' . LAYER_NAME . '/common/other';
        if (!empty($tpl) && $autoDir) {
            $tpl = 'app/' . APP_NAME . '/view/' . LAYER_NAME . '/' . strtolower(MODULE_NAME) . '/' . strtolower($tpl);
        }

        $cssDir = 'public/' . APP_NAME . '/css/style_mobile.css';
        if (is_file(ROOT_PATH . $cssDir)) {
            $this->userCss[] = ROOT_URL . '/' . $cssDir;
        }

        $systemFile = ROOT_URL . '/public/member/css/style_mobile.css';

        $theme = $this->siteConfig['tpl_name'] . '_mobile';
        $cssDir = 'theme/' . $theme . '/css/member.css';
        if (is_file(ROOT_PATH . $cssDir)) {
            $this->userCss[] = ROOT_URL . '/' . $cssDir;
        }

        if(!in_array($systemFile, $this->userCss)) {
            $this->userCss[] = $systemFile;
        }

        $jsDir = 'public/' . APP_NAME . '/js_mobile/lib.js';
        if (is_file(ROOT_PATH . $jsDir)) {
            $this->userJs[] = ROOT_URL . '/' . $jsDir;
        }
        $this->assign('sysPublic', $this->publicUrl);
        $this->assign('site', $this->siteConfig);
        $this->assign('userCss', $this->userCss);
        $this->assign('userJs', $this->userJs);
        $this->assign('tplInfo', $this->tplInfo);
        $this->display($tpl);
        exit;
    }

    public function getToolsMenu() {
        $list = hook('service', 'menu', 'tools');
        $menuList = [];
        foreach ((array)$list as $value) {
            $menuList = array_merge_recursive((array)$menuList, (array)$value);
        }
        $menuList = array_sort($menuList, 'order', 'asc', true);
        foreach ($menuList as $app => $appList) {
            $menuList[$app]['menu'] = array_sort($appList['menu'], 'order', 'asc');
        }
    }

    public function setTpl($name, $value) {
        $this->tplInfo[$name] = $value;
    }


    /**
     * 图片验证码
     */
    public function getImgCode() {
        return new \dux\lib\Vcode(90, 37, 4, '', 'code');
    }
	
	public function getCode() {
        $userName = request('post', 'username');
        $imgcode = request('post', 'imgcode');
		
        target('member/Register', 'middle')->setParams([
            'username' => $userName,
            'imgcode' => $imgcode
        ])->getCode()->export(function () {
            $this->success('验证码已发送,请注意查收!');
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}