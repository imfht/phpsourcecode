<?php

namespace app\lib;

use think\Session;

/**
 * 管理后台 用户登录 退出
 */
class BehaviorRecording
{
    public static $logActions = [
        'Menu_del' => '菜单-删除',
        'Menu_updatemenu' => '菜单-(添加|编辑)',
        'Config_save' => '配置-编辑',
        'AuthManager_updategroup' => '用户组-添加|编辑',
        'AuthManager_updateaccess' => '用户组-更新权限',
        'AuthManager_addtogroup' => '用户组-添加用户',
        'AuthManager_removefromgroup' => '用户组-移除用户',
    ];

    /**
     * 用户系统行为日志记录
     * @param   integer $uid        用户ID
     * @param   string  $controller 控制器
     * @param   string  $action     方法
     * @param   string  $remark     备注
     * @author baiyouwen
     */
    public static function log($uid=0, $controller='', $action='', $remark='', $secre=['password', 'passwd'])
    {
        if($controller=='' || $action == ''){
            return 1;
        }
        $key = $controller.'_'.$action;
        if(isset(self::$logActions[$key])){
            $req = \think\Request::instance();
            $data = [];
            $data['user_id'] = $uid;
            $data['ip'] = $req->ip(1);
            $data['req_type'] = $req->method();
            $data['request'] = $req->url();
            $param = $req->param('');
            foreach ($secre as $value) {
                if(isset($param[$value]))
                    $param[$value] = '**';
            }
            $data['param'] = json_encode($param);
            $data['remark'] = $remark != '' ? $remark : self::$logActions[$key];

            $data['create_time'] = date('Y-m-d H:i:s');
            $ret = db('admin_behavior_log')->insert($data);
            return $ret;
        }
        return 1;
    }

    public static function writeLog($uid=0, $controller='', $action='', $remark='', $secre=['password', 'passwd'])
    {
        $req = \think\Request::instance();
        $data = [];
        $data['user_id'] = $uid;
        $data['ip'] = $req->ip(1);
        $data['req_type'] = $req->method();
        $data['request'] = $req->url();
        $param = $req->param('');
        foreach ($secre as $value) {
            if(isset($param[$value]))
                $param[$value] = '**';
        }
        $data['param'] = json_encode($param);
        $data['remark'] = $remark;

        $data['create_time'] = date('Y-m-d H:i:s');
        $ret = db('admin_behavior_log')->insert($data);
        return $ret;
    }
}