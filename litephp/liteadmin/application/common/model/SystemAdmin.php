<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/4
 * Time: 17:09
 */

namespace app\common\model;

use think\Model;

/**
 * 后台用户模型
 * Class SystemAdmin
 * @package app\common\model
 */
class SystemAdmin extends Model
{
    /**
     * 用户名 字段
     * @return string
     */
    public static function username()
    {
        return 'username';
    }
    /**
     * 关联角色
     * @return \think\model\relation\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(SystemRole::class,SystemAuthMap::class,'role_id','admin_id');
    }
}