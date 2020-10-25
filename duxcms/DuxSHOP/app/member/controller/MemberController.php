<?php

/**
 * 会员移动控制器
 */

namespace app\member\controller;

class MemberController extends \app\base\controller\SiteController {

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
                    $this->redirect(url('', ['uid' => '', 'token' => '']));
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
     */
    protected function memberDisplay($tpl = '', $sideBar = true) {

        $theme = $this->siteConfig['tpl_name'];

        $actionName = ACTION_NAME == 'index' ? '' : '_' . ACTION_NAME;
        $userTpl =  strtolower( $tpl ? $tpl : MODULE_NAME . $actionName);
        $tplDir = 'theme/' . $theme . '/' . strtolower(APP_NAME) . '_';
        if (is_file(ROOT_PATH . $tplDir . $userTpl . '.html')) {
            $this->mobileDisplay($userTpl);
            exit;
        }
        $cssDir = 'theme/' . $theme . '/css/member.css';
        if (is_file(ROOT_PATH . $cssDir)) {
            $this->userCss[] = ROOT_URL . '/' . $cssDir;
        }

        $cssDir = 'public/' . APP_NAME . '/css/style.css';
        if (is_file(ROOT_PATH . $cssDir)) {
            $this->userCss[] = ROOT_URL . '/' . $cssDir;
        }

        $jsDir = 'public/' . APP_NAME . '/js/lib.js';
        if (is_file(ROOT_PATH . $jsDir)) {
            $this->userJs[] = ROOT_URL . '/' . $jsDir;
        }

        $this->layout = 'app/member/view/' . LAYER_NAME . '/common/common';
        if (!empty($tpl)) {
            $tpl = 'app/' . APP_NAME . '/view/' . LAYER_NAME . '/' . strtolower(MODULE_NAME) . '/' . strtolower($tpl);
        }
        $data = [];
        $hookList = hook('service', 'menu', 'MemberHead', [$this->userInfo]);
        foreach ((array)$hookList as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }
        $data = array_sort($data, 'sort','DESC');
        $menu = $this->getMenu();
        $this->assign('headMenu', $data);
        $this->assign('nav', $menu['menu']);
        $this->assign('curNav', $menu['cur']);
        $this->assign('sideBar', $sideBar);
        $this->assign('sysPublic', $this->publicUrl);
        $this->assign('site', $this->siteConfig);
        $this->assign('userCss', $this->userCss);
        $this->assign('userJs', $this->userJs);

        $this->display($tpl);
    }

    /**
     * 其他模板输出
     */
    protected function otherDisplay($tpl = '', $autoDir = true) {
        $this->layout = 'app/member/view/' . LAYER_NAME . '/common/other';
        if (!empty($tpl) && $autoDir) {
            $tpl = 'app/' . APP_NAME . '/view/' . LAYER_NAME . '/' . strtolower(MODULE_NAME) . '/' . strtolower($tpl);
        }
        $theme = $this->siteConfig['tpl_name'];
        $cssDir = 'theme/' . $theme . '/css/member.css';
        if (is_file(ROOT_PATH . $cssDir)) {
            $this->userCss[] = ROOT_URL . '/' . $cssDir;
        }

        $cssDir = 'public/' . APP_NAME . '/css/style.css';
        if (is_file(ROOT_PATH . $cssDir)) {
            $this->userCss[] = ROOT_URL . '/' . $cssDir;
        }

        $jsDir = 'public/' . APP_NAME . '/js/lib.js';
        if (is_file(ROOT_PATH . $jsDir)) {
            $this->userJs[] = ROOT_URL . '/' . $jsDir;
        }

        $this->assign('sysPublic', $this->publicUrl);
        $this->assign('site', $this->siteConfig);
        $this->assign('userCss', $this->userCss);
        $this->assign('userJs', $this->userJs);
        $this->display($tpl);
        exit;
    }

    /**
     * 弹出模板输出
     */
    protected function dialogDisplay($tpl = '', $autoDir = true) {
        $this->layout = 'app/member/view/' . LAYER_NAME . '/common/dialog';
        if (!empty($tpl) && $autoDir) {
            $tpl = 'app/' . APP_NAME . '/view/' . LAYER_NAME . '/' . strtolower(MODULE_NAME) . '/' . strtolower($tpl);
        }
        $theme = $this->siteConfig['tpl_name'];
        $cssDir = 'theme/' . $theme . '/css/member.css';
        if (is_file(ROOT_PATH . $cssDir)) {
            $this->userCss[] = ROOT_URL . '/' . $cssDir;
        }

        $cssDir = 'public/' . APP_NAME . '/css/style.css';
        if (is_file(ROOT_PATH . $cssDir)) {
            $this->userCss[] = ROOT_URL . '/' . $cssDir;
        }

        $jsDir = 'public/' . APP_NAME . '/js/lib.js';
        if (is_file(ROOT_PATH . $jsDir)) {
            $this->userJs[] = ROOT_URL . '/' . $jsDir;
        }

        $this->assign('header', true);
        $this->assign('sysPublic', $this->publicUrl);
        $this->assign('site', $this->siteConfig);
        $this->assign('userCss', $this->userCss);
        $this->assign('userJs', $this->userJs);
        $this->display($tpl);
        exit;
    }

    public function setTpl($name, $value) {
        $this->tplInfo[$name] = $value;
    }

    /**
     * 获取菜单
     * @return array|mixed
     */
    private function getMenu() {
        $list = hook('service', 'menu', 'member');
        $curUrl = strtolower('/' . APP_NAME . '/' . MODULE_NAME . '/');
        $data = [];
        foreach ((array)$list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }
        $data = array_sort($data, 'order', 'asc', true);
        $curNav = [];
        foreach ($data as $app => $appList) {
            $data[$app]['menu'] = array_sort($appList['menu'], 'order', 'asc');
            if (empty($data[$app]['url'])) {
                $data[$app]['url'] = $data[$app]['menu'][0]['url'];
            }
        }

        return ['menu' => $data, 'cur' => $curNav];
    }

    /**
     * 图片验证码
     */
    public function getImgCode() {
        return new \dux\lib\Vcode(90, 37, 4, '', 'code');
    }

}