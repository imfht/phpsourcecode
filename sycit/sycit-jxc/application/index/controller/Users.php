<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/8/21
// +----------------------------------------------------------------------
// | Title:  Users.php
// +----------------------------------------------------------------------
namespace app\index\controller;

use app\index\model\AuthGroupAccess;
use app\index\model\Users AS UsersModel;
use think\Db;
use think\Request;
use think\Session;
use think\Url;
use think\Loader;

class Users extends Common_base
{
    public function index() {
        $Request = Request::instance();
        $query = $Request->param(); // 分页查询传参数
        $m = $Request->param('m');
        $k = $Request->param('k');

        $users = new UsersModel();

        if ($m == 'status' && $k !=='') {
            //
            $data = $users->scope('status', $k)->paginate('', false, ['query' => $query ]);
        } elseif ($m == 'nick' && $k !=='') {
            $data = $users->scope('nick', $k)->paginate('', false, ['query' => $query ]);
        }
        else {
            $data = $users->where('status','>=',0)->paginate(20); // 默认查询
        }

        // 获取分页显示
        $page = $data->render();
        $this->assign('title','账户管理');
        $this->assign('page',$page);
        $this->assign('data', $data);
        $this->assign('empty', '<tr><td colspan="10" align="center">当前条件没有查到数据</td></tr>');
        return $this->fetch();
    }

    // 新增账户
    public function add() {
        // 是否有权限
        IS_ROOT([1])  ? true : $this->error('没有权限');
        //
        $group = Db::name('auth_group')->order('sort','asc')->select();
        $this->assign('title','添加账户');
        $this->assign('group',$group);
        return $this->fetch();
    }

    // 验证登录名
    public function check_name() {
        // 设定数据返回格式
        \think\Config::set("default_return_type","json");
        $Request = Request::instance();
        if ($Request->isPost()) {
            $username = $Request->param("username");
            // 查询是否有此账户
            $userDb = Db::name('users')->where('user_name', $username)->find();
            if ($userDb) {
                return false;
            } else {
                return true;
            }
        }
    }

    // 验证姓名
    public function check_nick() {
        // 设定数据返回格式
        \think\Config::set("default_return_type","json");
        $Request = Request::instance();
        if ($Request->isPost()) {
            $nickname = $Request->param("nickname");
            // 查询是否有此账户
            $userDb = Db::name('users')->where('user_nick', $nickname)->find();
            if ($userDb) {
                return false;
            } else {
                return true;
            }
        }
    }

    // 提交新增账户
    public function user_do() {
        // 设定数据返回格式
        \think\Config::set("default_return_type","json");
        // 是否有权限
        IS_ROOT([1])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        //p($Request->param());
        //exit();
        if ($Request->isPost()) {
            $username = $Request->param('username');
            $password = $Request->param('password');
            $nickname = $Request->param('nickname');
            $bumen    = $Request->param('bumen');
            $sex      = $Request->param('sex');
            $email    = $Request->param('email');
            //查询是否已有名称存在
            if (UsersModel::getByUserName($username)) {
                $this->error('登录名称重复，请更换！');
            }
            //查询是否有员工姓名存在
            if (UsersModel::getByUserNick($nickname)) {
                $this->error('员工姓名重复，请更换！');
            }
            //验证输入是否正确格式
            $loader = Loader::validate('Users');
            if (!$loader->scene("add")->check([
                'username'=>$username,
                'nickname'=>$nickname,
                'password'=>$password,
                'bumen'=>$bumen,
                '__token__'=>$Request->param('__token__')
            ])) {
                $this->error($loader->getError());
            }
            $user = new UsersModel([
                'user_name' => $username,
                'user_password' => create_hash($password),
                'user_nick' => $nickname,
                'user_auth' => $bumen,
                'user_sex' => $sex,
                'user_email' => $email,
                'entry_time' => $Request->param('ruzhishijian'),
                //'create_ip' =>
            ]);
            if ($user->save()) {
                $group = Db::name('auth_group_access')->insert([
                    'uid' => $user->id,
                    'group_id' => $bumen
                ]);
                if ($group) {
                    $this->success('添加员工【'.$nickname.'】成功',Url::build('users/index'));
                }
            }
        }
    }

    // 修改账户
    public function edit() {
        // 是否有权限
        IS_ROOT([1])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        $uid = $Request->param("uid");
        if (empty($uid)) {
            $this->error('参数错误');
        }

        $group = Db::name('auth_group')->order('sort','asc')->select();
        $user = Db::name('users')->where('id',$uid)->field('user_password',true)->find();

        if (empty($user)) {
            $this->error('无此账户！');
        }

        $this->assign('title','账户修改');
        $this->assign('group',$group);
        $this->assign('user',$user);
        return $this->fetch();
    }

    // 更新账户
    public function update() {
        $Request = Request::instance();
        if ($Request->isPost()) {
            $uid = $Request->param('uid');
            $password = $Request->param('password');
            $nickname = $Request->param('nickname');
            $bumen = $Request->param('bumen');
            $sex = $Request->param('sex');
            $email = $Request->param('email');
            $ruzhishijian = $Request->param('ruzhishijian');
            if (empty($uid)) {
                $this->error('参数错误！');
            }

            $user = UsersModel::get($uid);
            if (empty($user)) {
                $this->error('更新没有此账户！');
            }
            $data = [
                'user_nick' => $nickname,
                'user_sex' => $sex,
                'user_email' => $email,
                'status' => $Request->param('status')
            ];
            if (!empty($password)) {
                $validate = Loader::validate("Users");
                if (!$validate->scene("update")->check(['password'=>$password])) {
                    // dump($validate->getError()); // 输出 验证的 错误信息
                    $this->error($validate->getError());
                }
                $data['user_password'] = create_hash($password);
            }

            if (!empty($bumen)) {
                $data['user_auth'] = $bumen;
                AuthGroupAccess::where('uid',$uid)->update(['group_id'=>$bumen]);
            }
            if (!empty($ruzhishijian)) {
                $data['entry_time'] = $ruzhishijian;
            }

            //p($data);

            //UsersModel::where('id',$uid)->update($data);
            Db::name('users')->where('id',$uid)->update($data);
            $this->success('更新成功',Url::build('users/index'));
        }
    }

    // 删除操作
    public function delete() {
        // 是否有权限
        IS_ROOT([1])  ? true : $this->error('没有权限');
        // 设定数据返回格式
        \think\Config::set("default_return_type","json");
        $Request = Request::instance();
        if ($Request->isPost()) {
            $uid = $Request->param("uid");
            if ($uid == Session::get('user_id')) {
                return $this->error('不能删除自己');
            }
            $name = $Request->param("name");
            if (empty($uid)) {
                return $this->error('传入参数错误');
            }
            if ($name == 'delone') {
                // 单条删除操作
                Db::name('users')->where('id', $uid)->update(['status'=>'-1']);
                return $this->success('删除成功', Url::build('users/index'));
            } elseif ($name == 'delallattr') {
                // 多条删除操作
                $arrUid = explode(",",$uid);
                if (!empty($arrUid)) {
                    $i=0;
                    foreach ($arrUid as $key=>$val) {
                        Db::name('users')->where('id', $val)->update(['status'=>'-1']);
                        $i++;
                    }
                    return $this->success($i.' 条记录删除成功', Url::build('users/index'));
                }
            } else {
                // 不执行操作
                return $this->error('传入参数错误');
            }
        }
    }
}