<?php
/**
 * YICMS
 * ============================================================================
 * 版权所有 2014-2017 YICMS，并保留所有权利。
 * 网站地址: http://www.yicms.vip
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Created by PhpStorm.
 * Author: kenuo
 * Date: 2017/11/13
 * Time: 上午10:35
 */

namespace App\Repositories;


use App\Models\Role;

class RolesRepository
{
    /**
     * 获取所有角色
     * @return mixed
     */
    public function getRoles()
    {
        return Role::get();
    }
}