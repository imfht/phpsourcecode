<?php
// 后台权限控制器
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\http\middleware;



use tpvue\admin\model\AdminAuthRuleModel;
use think\facade\Session;
use think\facade\View;
use think\Request;
use tpvue\admin\App;
use tpvue\admin\model\AuthModel;
use traits\controller\Jump;


class AuthMiddleware
{
    use Jump;


    public function handle(Request $request, \Closure $next)
    {
        $routeInfo = $request->routeInfo();
        $leftNav = [];
        $topNav = [];
        if (isset($routeInfo['option']['__ke__']) && $routeInfo['option']['__ke__']) {
            $controller = $routeInfo['option']['__rule__'];
        } else {
            $controller = $request->module() . '/' . $request->controller() . '/' . $request->action(true);
        }
        $auth_white_list = App::get('auth.no_check_controller');

        if (!in_array($controller, $auth_white_list)) {
            $auth = new AuthModel();
            $loginId = Session::get('user_auth_session.LoginId');

            if (!$auth->check($controller, $loginId)) {// 第一个参数是规则名称,第二个参数是用户UID
                $this->error('你没有权限');
            }
            $getGroups = $auth->getGroups($loginId);

            $rules = '';

            foreach ($getGroups as $k => $v) {
                if ($v['rules']) {
                    $rules .= $v['rules'] . ',';
                }
            }

            $pieces = explode(",", $rules);

            $rules = implode(',', array_unique(explode(',', $rules)));

            $hd_auth_rule = AdminAuthRuleModel::where('id', 'in', $rules)->field('name,pid,id,level,title')->where('type', 1)->where('is_display', 1)->order('sort,id')->select();
            if (!$hd_auth_rule->isEmpty()) {
                foreach ($hd_auth_rule as $key => $value) {
                    if ($value->level == 3 or $value->level == 4) {
                        foreach ($pieces as $k => $v) {
                            if ($v == $value->id) {
                                $leftNav[] = $value;
                            }
                        }
                    } elseif ($value->level == 2) {
                        foreach ($pieces as $k => $v) {
                            if ($v == $value->id) {
                                $topNav[] = $value;
                            }
                        }
                    }
                }
            }
        }

        View::share('topNav', $topNav);
        View::share('leftNav', $leftNav);
        return $next($request);
    }
}