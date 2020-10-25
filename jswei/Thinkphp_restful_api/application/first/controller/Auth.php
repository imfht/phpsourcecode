<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/3/26 14:24
// +----------------------------------------------------------------------
// | TITLE: this to do?
// +----------------------------------------------------------------------

namespace app\first\controller;

use app\first\auth\BasicAuth;
use app\first\auth\OauthAuth;
use think\Request;

class Auth
{
    public function accessToken(Request $request)
    {
        $OauthAuth = new OauthAuth();
        return $OauthAuth->accessToken($request);
//        $oath = new BasicAuth();
//        return $oath->certification($request);
    }

}