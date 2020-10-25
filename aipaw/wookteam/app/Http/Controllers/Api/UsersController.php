<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\DBCache;
use App\Module\Base;
use App\Module\Users;
use DB;
use Request;
use Session;

/**
 * @apiDefine users
 *
 * 会员
 */
class UsersController extends Controller
{
    public function __invoke($method, $action = '')
    {
        $app = $method ? $method : 'main';
        if ($action) {
            $app .= "__" . $action;
        }
        return (method_exists($this, $app)) ? $this->$app() : Base::ajaxError("404 not found (" . str_replace("__", "/", $app) . ").");
    }

    /**
     * 登陆、注册
     *
     * @apiParam {String} type           类型
     * - login:登录（默认）
     * - reg:注册
     * @apiParam {String} username       用户名
     * @apiParam {String} userpass       密码
     */
    public function login()
    {
        $type = trim(Request::input('type'));
        $username = trim(Request::input('username'));
        $userpass = trim(Request::input('userpass'));
        if ($type == 'reg') {
            $setting = Base::setting('system');
            if ($setting['reg'] == 'close') {
                return Base::retError('未开放注册。');
            }
            $user = Users::reg($username, $userpass);
            if (Base::isError($user)) {
                return $user;
            } else {
                $user = $user['data'];
            }
        } else {
            $user = Base::DBC2A(DB::table('users')->where('username', $username)->first());
            if (empty($user)) {
                return Base::retError('账号或密码错误。');
            }
            if ($user['userpass'] != Base::md52($userpass, $user['encrypt'])) {
                return Base::retError('账号或密码错误！');
            }
            if (in_array($user['id'], [1, 2])) {
                $user['setting'] = Base::string2array($user['setting']);
                if (intval($user['setting']['version']) < 1) {
                    $user['setting']['version'] = intval($user['setting']['version']) + 1;
                    $user['identity'] = ',admin,';
                    DB::table('users')->where('username', $username)->update([
                        'setting' => Base::array2string($user['setting']),
                        'identity' => $user['identity'],
                    ]);
                }
            }
        }
        //
        $array = [
            'token' => Users::token($user),
            'loginnum' => $user['loginnum'] + 1,
            'lastip' => Base::getIp(),
            'lastdate' => Base::time(),
            'lineip' => Base::getIp(),
            'linedate' => Base::time(),
        ];
        Base::array_over($user, $array);
        DB::table('users')->where('id', $user['id'])->update($array);
        //
        return Base::retSuccess($type == 'reg' ? "注册成功！" : "登陆成功！", Users::retInfo($user));
    }

    /**
     * 获取我的信息
     *
     * @apiParam {String} [callback]           jsonp返回字段
     */
    public function info()
    {
        $callback = Request::input('callback');
        //
        $user = Users::authE();
        if (Base::isError($user)) {
            if (strlen($callback) > 3) {
                return $callback . '(' . json_encode($user) . ')';
            }
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        if (strlen($callback) > 3) {
            return $callback . '(' . json_encode(Base::retSuccess('success', Users::retInfo($user))) . ')';
        }
        return Base::retSuccess('success', Users::retInfo($user));
    }

    /**
     * 获取指定会员基本信息
     *
     * @apiParam {String|jsonArray} username          会员用户名(多个格式：jsonArray，一次最多30个)
     */
    public function basic()
    {
        $username = trim(Request::input('username'));
        $array = Base::json2array($username);
        if (empty($array)) {
            $array[] = $username;
        }
        if (count($array) > 50) {
            return Base::retError(['一次最多只能获取%条数据！', 50]);
        }
        $retArray = [];
        foreach ($array AS $name) {
            $basic = Users::username2basic($name);
            if ($basic) {
                $retArray[] = $basic;
            }
        }
        return Base::retSuccess('success', $retArray);
    }

    /**
     * 搜索会员列表
     *
     * @apiParam {Object} where            搜索条件
     * - where.usernameequal
     * - where.nousername
     * - where.username
     * - where.noidentity
     * - where.identity
     * - where.noprojectid
     * - where.projectid
     * - where.nobookid
     * @apiParam {Number} [take]           获取数量，10-100
     */
    public function searchinfo()
    {
        $keys = Request::input('where');
        $whereArr = [];
        $whereRaw = null;
        if ($keys['usernameequal'])     $whereArr[] = ['username', '=', $keys['usernameequal']];
        if ($keys['identity'])          $whereArr[] = ['identity', 'like', '%,' . $keys['identity'] . ',%'];
        if ($keys['noidentity'])        $whereArr[] = ['identity', 'not like', '%,' . $keys['noidentity'] . ',%'];
        if ($keys['username']) {
            $whereRaw.= $whereRaw ? ' AND ' : '';
            $whereRaw.= "(`username` LIKE '%" . $keys['username'] . "%' OR `nickname` LIKE '%" . $keys['username'] . "%')";
        }
        if (intval($keys['projectid']) > 0) {
            $whereRaw.= $whereRaw ? ' AND ' : '';
            $whereRaw.= "`username` IN (SELECT username FROM `" . env('DB_PREFIX') . "project_users` WHERE `type`='成员' AND `projectid`=" . intval($keys['projectid']) .")";
        }
        if ($keys['nousername']) {
            $nousername = [];
            foreach (explode(",", $keys['nousername']) AS $name) {
                $name = trim($name);
                if ($name && !in_array($name, $nousername)) {
                    $nousername[] = $name;
                }
            }
            if ($nousername) {
                $whereRaw.= $whereRaw ? ' AND ' : '';
                $whereRaw.= "(`username` NOT IN ('" . implode("','", $nousername) . "'))";
            }
        }
        if (intval($keys['noprojectid']) > 0) {
            $whereRaw.= $whereRaw ? ' AND ' : '';
            $whereRaw.= "`username` NOT IN (SELECT username FROM `" . env('DB_PREFIX') . "project_users` WHERE `type`='成员' AND `projectid`=" . intval($keys['noprojectid']) .")";
        }
        if (intval($keys['nobookid']) > 0) {
            $whereRaw.= $whereRaw ? ' AND ' : '';
            $whereRaw.= "`username` NOT IN (SELECT username FROM `" . env('DB_PREFIX') . "docs_users` WHERE `bookid`=" . intval($keys['nobookid']) .")";
        }
        //
        $lists = DBCache::table('users')->select(['id', 'username', 'nickname', 'userimg', 'profession'])
            ->where($whereArr)
            ->whereRaw($whereRaw)
            ->orderBy('id')
            ->cacheMinutes(now()->addSeconds(10))
            ->take(Base::getPaginate(100, 10, 'take'))
            ->get();
        foreach ($lists AS $key => $item) {
            $lists[$key]['userimg'] = Users::userimg($item['userimg']);
            $lists[$key]['identitys'] = explode(",", trim($item['identity'], ","));
            $lists[$key]['setting'] = Base::string2array($item['setting']);
        }
        return Base::retSuccess('success', $lists);
    }

    /**
     * 修改资料
     *
     * @apiParam {Object} [userimg]             会员头像
     * @apiParam {String} [nickname]            昵称
     * @apiParam {String} [profession]          职位/职称
     * @apiParam {String} [bgid]                背景编号
     */
    public function editdata()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $array = [];
        //头像
        $userimg = Request::input('userimg');
        if ($userimg) {
            $userimg = is_array($userimg) ? $userimg[0]['path'] : $userimg;
            $array['userimg'] = Base::unFillUrl($userimg);
        }
        //昵称
        $nickname = trim(Request::input('nickname'));
        if ($nickname) {
            if (mb_strlen($nickname) < 2) {
                return Base::retError('昵称不可以少于2个字！');
            } elseif (mb_strlen($nickname) > 8) {
                return Base::retError('昵称最多只能设置8个字！');
            } else {
                $array['nickname'] = $nickname;
            }
        }
        //职位/职称
        $profession = trim(Request::input('profession'));
        if ($profession) {
            if (mb_strlen($profession) < 2) {
                return Base::retError('职位/职称不可以少于2个字！');
            } elseif (mb_strlen($profession) > 20) {
                return Base::retError('职位/职称最多只能设置20个字！');
            } else {
                $array['profession'] = $profession;
            }
        }
        //背景
        $bgid = intval(Request::input('bgid'));
        if ($bgid > 0) {
            $array['bgid'] = $bgid;
        }
        //
        if ($array) {
            DB::table('users')->where('id', $user['id'])->update($array);
            Users::AZUpdate($user['id']);
        } else {
            return Base::retError('请设置要修改的内容！');
        }
        return Base::retSuccess('修改成功！');
    }

    /**
     * 修改密码
     *
     * @apiParam {String} oldpass           旧密码
     * @apiParam {String} newpass           新密码
     */
    public function editpass()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $oldpass = trim(Request::input('oldpass'));
        $newpass = trim(Request::input('newpass'));
        if (strlen($newpass) < 6) {
            return Base::retError('密码设置不能小于6位数！');
        } elseif (strlen($newpass) > 32) {
            return Base::retError('密码最多只能设置32位数！');
        }
        if ($oldpass == $newpass) {
            return Base::retError('新旧密码一致！');
        }
        //
        if (env("PASSWORD_ADMIN") == 'disabled') {
            if ($user['id'] == 1) {
                return Base::retError('当前环境禁止修改密码！');
            }
        }
        if (env("PASSWORD_OWNER") == 'disabled') {
            return Base::retError('当前环境禁止修改密码！');
        }
        //
        if ($user['setpass']) {
            $verify = DB::table('users')->where(['id'=>$user['id'], 'userpass'=>Base::md52($oldpass, Users::token2encrypt())])->count();
            if (empty($verify)) {
                return Base::retError('请填写正确的旧密码！');
            }
        }
        $encrypt = Base::generatePassword(6);
        DB::table('users')->where('id', $user['id'])->update([
            'encrypt' => $encrypt,
            'userpass' => Base::md52($newpass, $encrypt),
            'changepass' => 0
        ]);
        return Base::retSuccess('修改成功');
    }

    /**
     * 团队列表
     *
     * @apiParam {Object} [sorts]               排序方式，格式：{key:'', order:''}
     * - key: username|az|id(默认)
     * - order: asc|desc
     * @apiParam {String} [username]            指定获取某个成员（返回对象）
     * @apiParam {Number} [page]                当前页，默认:1
     * @apiParam {Number} [pagesize]            每页显示数量，默认:10，最大:100
     */
    public function team__lists()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        $username = trim(Request::input('username'));
        $whereArray = [];
        if ($username) {
            $whereArray[] = ['username', '=', $username];
        }
        //
        $orderBy = '`id` DESC';
        $sorts = Base::json2array(Request::input('sorts'));
        if (in_array($sorts['order'], ['asc', 'desc'])) {
            switch ($sorts['key']) {
                case 'username':
                    $orderBy = '`' . $sorts['key'] . '` ' . $sorts['order'] . ',`id` DESC';
                    break;
                case 'az':
                    $orderBy = '`' . $sorts['key'] . '` ' . $sorts['order'] . ',`username` ' . $sorts['order'] . ',`id` DESC';
                    break;
            }
        }
        //
        $lists = DB::table('users')->where($whereArray)->select(['id', 'identity', 'username', 'nickname', 'az', 'userimg', 'profession', 'regdate'])->orderByRaw($orderBy)->paginate(Base::getPaginate(100, 10));
        $lists = Base::getPageList($lists);
        if ($lists['total'] == 0) {
            return Base::retError('未找到任何相关的团队成员');
        }
        foreach ($lists['lists'] AS $key => $item) {
            $lists['lists'][$key]['identity'] = is_array($item['identity']) ? $item['identity'] : explode(",", trim($item['identity'], ","));
            $lists['lists'][$key]['userimg'] = Users::userimg($item['userimg']);
        }
        if ($username) {
            return Base::retSuccess('success', $lists['lists'][0]);
        }
        return Base::retSuccess('success', $lists);
    }

    /**
     * 添加团队成员
     *
     * @apiParam {Number} [id]                  用户ID（留空为添加用户）
     * @apiParam {String} username              用户名（修改时无效，多个用英文逗号分隔）
     * @apiParam {String} userpass              密码
     * @apiParam {Object} [userimg]             会员头像
     * @apiParam {String} [nickname]            昵称
     * @apiParam {String} [profession]          职位/职称
     * @apiParam {Number} changepass            登陆是否需要修改密码
     */
    public function team__add()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        if (Base::isError(Users::identity('admin'))) {
            return Base::retError('权限不足！', [], -1);
        }
        //头像
        $userimg = Request::input('userimg');
        if ($userimg) {
            $userimg = is_array($userimg) ? $userimg[0]['path'] : $userimg;
        }
        //昵称
        $nickname = trim(Request::input('nickname'));
        if ($nickname) {
            if (mb_strlen($nickname) < 2) {
                return Base::retError('昵称不可以少于2个字！');
            } elseif (mb_strlen($nickname) > 8) {
                return Base::retError('昵称最多只能设置8个字！');
            }
        }
        //职位/职称
        $profession = trim(Request::input('profession'));
        if ($profession) {
            if (mb_strlen($profession) < 2) {
                return Base::retError('职位/职称不可以少于2个字！');
            } elseif (mb_strlen($profession) > 20) {
                return Base::retError('职位/职称最多只能设置20个字！');
            }
        }
        //
        $id = intval(Request::input('id'));
        $userpass = trim(Request::input('userpass'));
        $otherArray = [
            'userimg' => $userimg ?: '',
            'nickname' => $nickname ?: '',
            'profession' => $profession ?: '',
            'changepass' => intval(Request::input('changepass')),
        ];
        if ($id > 0) {
            //开始修改
            if ($userpass) {
                if (strlen($userpass) < 6) {
                    return Base::retError('密码设置不能小于6位数！');
                } elseif (strlen($userpass) > 32) {
                    return Base::retError('密码最多只能设置32位数！');
                }
                $encrypt = Base::generatePassword(6);
                $otherArray['encrypt'] = $encrypt;
                $otherArray['userpass'] = Base::md52($userpass, $encrypt);
            }
            DB::table('users')->where('id', $id)->update($otherArray);
            Users::AZUpdate($id);
            return Base::retSuccess('修改成功！');
        } else {
            //开始注册
            if (strlen($userpass) < 6) {
                return Base::retError('密码设置不能小于6位数！');
            } elseif (strlen($userpass) > 32) {
                return Base::retError('密码最多只能设置32位数！');
            }
            $username = trim(Request::input('username'));
            $array = array_values(array_filter(array_unique(explode(",", $username))));
            if (empty($array)) {
                return Base::retError('请填写有效的用户名！');
            }
            if (count($array) > 500) {
                return Base::retError(['一次最多只能添加%个账号！', 500]);
            }
            foreach ($array AS $item) {
                $username = trim($item);
                if ($username) {
                    $user = Users::reg($username, $userpass, $otherArray);
                    if (Base::isError($user)) {
                        return $user;
                    }
                }
            }
            return Base::retSuccess('添加成功！');
        }
    }

    /**
     * 删除团队成员
     *
     * @apiParam {String} username           用户名
     */
    public function team__delete()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        if (Base::isError(Users::identity('admin'))) {
            return Base::retError('权限不足！', [], -1);
        }
        $username = trim(Request::input('username'));
        if ($user['username'] == $username) {
            return Base::retError('不能删除自己！');
        }
        //
        if (DB::table('users')->where('username', $username)->delete()) {
            return Base::retSuccess('删除成功！');
        } else {
            return Base::retError('删除失败！');
        }
    }

    /**
     * 设置、删除管理员
     *
     * @apiParam {String} act           操作
     * - set: 设置管理员
     * - del: 删除管理员
     * @apiParam {String} username      用户名
     */
    public function team__admin()
    {
        $user = Users::authE();
        if (Base::isError($user)) {
            return $user;
        } else {
            $user = $user['data'];
        }
        //
        if (Base::isError(Users::identity('admin'))) {
            return Base::retError('权限不足！', [], -1);
        }
        //
        $username = trim(Request::input('username'));
        if ($user['username'] == $username) {
            return Base::retError('不能操作自己！');
        }
        $userInfo = Base::DBC2A(DB::table('users')->where('username', $username)->first());
        if (empty($userInfo)) {
            return Base::retError('成员不存在！');
        }
        $identity = is_array($userInfo['identity']) ? $userInfo['identity'] : explode(",", trim($userInfo['identity'], ","));
        $isUp = false;
        if (trim(Request::input('act')) == 'del') {
            if (Users::identityRaw('admin', $identity)) {
                $identity = array_diff($identity, ['admin']);
                $isUp = true;
            }
        } else {
            if (!Users::identityRaw('admin', $identity)) {
                $identity[] = 'admin';
                $isUp = true;
            }
        }
        if ($isUp) {
            DB::table('users')->where('username', $username)->update([
                'identity' => $identity ? (',' . implode(",", $identity) . ',') : ''
            ]);
        }
        return Base::retSuccess('操作成功！', [
            'up' => $isUp ? 1 : 0,
            'identity' => $identity
        ]);
    }

    /**
     * 设置、删除友盟token
     *
     * @apiParam {String} act           操作
     * - set: 设置token
     * - del: 删除token
     * @apiParam {String} token         友盟token
     * @apiParam {String} platform      ios|android
     */
    public function umeng__token()
    {
        $act = trim(Request::input('act'));
        $token = trim(Request::input('token'));
        if (empty($token)) {
            return Base::retError('token empty');
        }
        $platform = strtolower(trim(Request::input('platform')));
        DB::table('umeng')->where('token', $token)->delete();
        //
        if ($act == 'set') {
            $user = Users::authE();
            if (Base::isError($user)) {
                return $user;
            } else {
                $user = $user['data'];
            }
            DB::table('umeng')->insert([
                'token' => $token,
                'username' => $user['username'],
                'platform' => $platform,
                'update' => Base::time(),
            ]);
        }
        //
        return Base::retSuccess('success');
    }
}
