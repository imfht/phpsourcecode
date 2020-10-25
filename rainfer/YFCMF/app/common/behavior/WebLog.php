<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

namespace app\common\behavior;

use app\common\model\Common as CommonModel;
use think\facade\Request;

/**
 * 日志行为
 * @Author: rainfer <rainfer520@qq.com>
 */
class WebLog
{
    /**
     * 执行行为 run方法是Behavior唯一的接口
     *
     * @param mixed $params 行为参数
     *
     * @return mixed
     * @throws \Exception
     */
    public function run($params)
    {
        $request = Request::instance();
        //不记录的模块
        $not_log_module = config('yfcmf.web_log.not_log_module') ?: [];

        //不记录的控制器 'module/controller'
        $not_log_controller = config('yfcmf.web_log.not_log_controller') ?: [];

        //不记录的操作方法 'module/controller/action'
        $not_log_action = config('yfcmf.web_log.not_log_action') ?: [];

        /**
         * 不记录data的操作方法 'module/controller/action'
         * 如涉及密码传输的地方：
         *      1、前、后台登录runlogin
         *      2、前台重置密码runpwd_reset
         *      3、前台runregister runchangepwd
         *      4、后台member_runadd member_runedit
         *      5、后台admin_runadd admin_runedit
         */
        $not_log_data = [
            'admin/Login/login',
            'admin/User/UserSave',
            'admin/User/UserUpdate',
            'admin/Admin/adminSave',
            'admin/Admin/adminUpdate'
        ];
        $not_log_data = array_merge($not_log_data, config('yfcmf.web_log.not_log_data') ?: []);

        //不记录的请求类型
        $not_log_request_method = config('yfcmf.web_log.not_log_request_method') ?: [];
        if (
            in_array($request->module(), $not_log_module) ||
            in_array($request->module() . '/' . $request->controller(), $not_log_controller) ||
            in_array($request->module() . '/' . $request->controller() . '/' . $request->action(), $not_log_action) ||
            in_array($request->method(), $not_log_request_method)
        ) {
            return true;
        }
        //只记录存在的操作方法
        if (!has_action($request->module(), $request->controller(), $request->method())) {
            return true;
        }
        try {
            if (in_array($request->module() . '/' . $request->controller() . '/' . $request->action(), $not_log_data)) {
                $requestData = '保密数据';
            } else {
                $requestData = $request->param();
                foreach ($requestData as &$v) {
                    if (is_string($v)) {
                        $v = mb_substr($v, 0, 200);
                    }
                }
            }
            $data      = [
                'uid'        => session('hid') ?: 0,
                'ip'         => $request->ip(),
                'location'   => implode(' ', \Ip::find($request->ip())),
                'os'         => getOs(),
                'browser'    => getBroswer(),
                'url'        => $request->url(),
                'module'     => $request->module(),
                'controller' => $request->controller(),
                'action'     => $request->action(),
                'method'     => $request->isAjax() ? 'Ajax' : ($request->isPjax() ? 'Pjax' : $request->method()),
                'data'       => serialize($requestData),
                'otime'      => time(),
            ];
            $web_model = new CommonModel();
            $web_model->setTable(config('database.prefix') . 'web_log')->setPk('id')->insert($data);
        } catch (\Exception $e) {
        }
    }
}
