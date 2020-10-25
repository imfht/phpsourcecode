<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\ucenter\controller;

use think\Controller;

class Base extends Controller
{
    public function _initialize()
    {
        $uid = intval($_REQUEST['uid']) ? intval($_REQUEST['uid']) : is_login();
        if (!$uid) {
            $this->error(lang('_ERROR_NEED_LOGIN_'));
        }
        $this->assign('uid', $uid);
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
            return lang('_I_');
        } else {
            $apiProfile = action('User/getProfile', array($uid));
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