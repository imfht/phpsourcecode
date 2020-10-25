<?php
namespace app\ucenter\controller;

use think\Controller;
use app\common\controller\Common;


class Base extends Common
{
    public function _initialize()
    {
        parent::_initialize();
        if (!_need_login()) {
            $this->error(lang('_ERROR_NEED_LOGIN_'));
        }
        $this->assign('uid', is_login());
        $this->mid = is_login();
    }

    protected function defaultTabHash($tabHash)
    {
        $tabHash = text($_REQUEST['tabHash']) ?  text($_REQUEST['tabHash']): $tabHash;
        $this->assign('tabHash', $tabHash);
    }

    protected function getCall($uid)
    {
        if ($uid == is_login()) {
            return lang('_I_');
        } else {
            $apiProfile = callApi('User/getProfile', array($uid));
            return $apiProfile['sex'] == 'm' ? lang('_HE_') : lang('_SHE_');
        }
    }

    protected function ensureApiSuccess($result)
    {
        if (!$result['success']) {
            $this->error($result['message'], $result['url']);
        }
    }

    protected function requireLogin()
    {
        if (!is_login()) {
            $this->error(lang('_ERROR_MUST_LOGIN_'));
        }
    }
}