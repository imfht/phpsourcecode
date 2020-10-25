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

namespace app\user\model;

use think\Model;
use think\facade\Validate;
use think\Db;

/**
 * 会员模型
 * @Author: rainfer <rainfer520@qq.com>
 */
class User extends Model
{
    public function news()
    {
        return $this->hasMany('News', 'author');
    }

    public function groups()
    {
        return $this->belongsToMany('Role', 'role_access', 'role_id', 'uid');
    }

    /**
     * 增加会员
     *
     * @param string $username
     * @param string $salt
     * @param string $password
     * @param string $nickname
     * @param string $email
     * @param string $mobile
     * @param int    $open
     * @param int    $status
     * @param int    $province
     * @param int    $city
     * @param int    $town
     * @param int    $sex
     * @param string $user_url
     * @param string $signature
     * @param int    $score
     * @param int    $role_id
     *
     * @return mixed 0或会员id或错误信息
     */
    public static function add($username, $salt = '', $password = '', $nickname = '', $email = '', $mobile = '', $open = 0, $status = 0, $province = 0, $city = 0, $town = 0, $sex = 3, $user_url = '', $signature = '', $score = 0, $role_id = 1)
    {
        $sldata   = [
            'username' => $username,
            'password' => $password,
            'email'    => $email,
            'mobile'   => $mobile
        ];
        if (!Validate::unique($username, 'user,username', $sldata, 'username')) {
            return '用户名重复';
        } else {
            $salt                  = $salt ?: random(10);
            $sldata['pwd_salt']    = $salt;
            $sldata['password']    = encrypt_password($password, $salt);
            $sldata['nickname']    = $nickname;
            $sldata['open']        = $open;
            $sldata['last_ip']     = request()->ip();
            $sldata['create_time'] = time();
            $sldata['last_time']   = time();
            $sldata['status']      = $status;
            $sldata['province']    = $province;
            $sldata['city']        = $city;
            $sldata['town']        = $town;
            $sldata['sex']         = $sex;
            $sldata['user_url']    = $user_url;
            $sldata['signature']   = $signature;
            $sldata['score']        = $score;
            // 启动事务
            Db::startTrans();
            try {
                $user = self::create($sldata);
                $uid = intval($user['id']);
                //关联添加管理组
                $user->groups()->save($role_id);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $e->getMessage();
            }
            return $uid;
        }
    }

    /**
     * 删除会员
     *
     * @param int $id
     *
     * @return bool
     * @throws
     */
    public static function del($id)
    {
        $user = self::get($id, 'news');
        return $user->together('news')->delete();
    }
}
