<?php
/**
 * 基础公共控制器
 */

namespace Ucenter\Controller;

use Think\Controller;
use Common\Controller\CommonController;


class BaseController extends CommonController
{
    public function _initialize()
    {
        parent::_initialize();
        if (!_need_login()) {
            $this->error(L('_ERROR_NEED_LOGIN_'));
        }
        $this->assign('uid', is_login());
        $this->mid = is_login();
    }

    protected function defaultTabHash($tabHash)
    {
        $tabHash = op_t($_REQUEST['tabHash']) ?  op_t($_REQUEST['tabHash']): $tabHash;
        $this->assign('tabHash', $tabHash);
    }

    protected function getCall($uid)
    {
        if ($uid == is_login()) {
            return L('_I_');
        } else {
            $apiProfile = callApi('User/getProfile', array($uid));
            return $apiProfile['sex'] == 'm' ? L('_HE_') : L('_SHE_');
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
            $this->error(L('_ERROR_MUST_LOGIN_'));
        }
    }
}