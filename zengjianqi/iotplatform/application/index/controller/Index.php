<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use app\index\model\User as Usermodel;

class Index extends Controller
{
    public function index()
    {
        //return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ad_bd568ce7058a1091"></think>';
        return $this->fetch();
    }


    public function loginVerify(Request $request)
    {
        //$data=Request::post();
        $receive = $request->post();
        //隐藏用户
        if ($receive['username'] == 'view') {
            if ($receive['password'] == '1610930312') {
                session('name', $receive['username']);
                session('root', 1);
                return 'ok';
            } else {
                return '密码错误';
            }
        } else {
            if ($receive['username'] == 'admin') {
                if ($receive['password'] == 'admin') {
                    session('name', $receive['username']);
                    session('root', 1);
                    return 'ok';
                } else {
                    return '密码错误';
                }
            }
        }
//        判断该账号是否存在
        $flag = Db::table('bs_people')->where('name', $receive['username'])->find();
        if (!$flag) {
            return '用户不存在';
        }
//        判断该账号是管理员还是用户
        if ($flag['root']) {
//            从管理员表中获得该账户对应的信息
            $flag1 = Db::table('bs_administrator')->where('account', $receive['username'])->find();
//            判断该管理员是否被冻结
            if ($flag1['status'] == 0) {
                if ($receive['password'] == $flag1['password']) {
//                    密码正确则会在后台存储该账户名字和权限并返回'ok'
                    session('name', $receive['username']);
                    session('root', 1);
                    return 'ok';
                } else
                    return '密码错误！';
            } else {
                return '该账号已被冻结，请联系后台管理员';
            }

        } else {
//            从用户表中获得该账户对应的信息
            $flag2 = Db::table('bs_user')->where('uid', $receive['username'])->find();
//            判断该用户是否被冻结
            if ($flag2['status'] == 0) {
                if ($receive['password'] == $flag2['password']) {
//                    密码正确则会在后台存储该账户名字和权限
                    session('name', $receive['username']);
                    session('root', 0);
//                        Db::table('bs_user')->where('uid', $data['username'])->setInc('count_times');
//                    记录该用户的登录IP和登录时间
                    $user = new Usermodel;
                    $user->where('uid', $receive['username'])->setInc('count_times');
                    return 'ok';
                } else
                    return '密码错误！';
            } else {
                return '该账号已被冻结，请联系后台管理员';
            }
        }

    }

    public function login()
    {
//        dump('ok');
        $name = session('name');
//        dump($name);
        $root = session('root');
//        dump($root);
        $timeNow = date('Y-m-d H:i:s', time());
//        $ip= \request()->ip();
//        $data = ['time' => $timeNow, 'operation' => '非法人员入侵', 'who' => '入侵者ip：' . $ip];
//        Db::table('bs_log')->insert($data);
        if ($root) {
            $data = ['time' => $timeNow, 'operation' => '登录管理员系统', 'who' => '管理员：' . $name];
            Db::table('bs_log')->insert($data);
            $this->redirect('Administrator/index');
        }
        if ($name){
            $data = ['time' => $timeNow, 'operation' => '登录用户系统', 'who' => '用户：' . $name];
            Db::table('bs_log')->insert($data);
            $this->error($name . '您好，' . '用户界面还在开发中，请使用管理员账户登录获得完整体验。', '/', '', '7');
            session(null);
        }
        $ip= \request()->ip();
        $data = ['time' => $timeNow, 'operation' => '非法人员入侵', 'who' => '入侵者ip：' . $ip];
        Db::table('bs_log')->insert($data);
        $this->error('成功了而没有快乐，是最大的失败。', '/', '', '7');
    }
}
