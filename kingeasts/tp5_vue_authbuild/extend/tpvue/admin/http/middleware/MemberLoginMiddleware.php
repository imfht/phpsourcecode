<?php
// 用户登录控制器
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------

namespace tpvue\admin\http\middleware;

use think\Request;

class MemberLoginMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        if( !is_login() ) {
            return redirect('admin/login');
        }
        return $next($request);
    }
}