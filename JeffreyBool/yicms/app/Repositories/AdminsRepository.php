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
 * Time: 上午9:54
 */
namespace App\Repositories;

use App\Models\Admin;

class AdminsRepository
{
    /**
     * 创建管理员
     * @param array $params
     * @return mixed
     */
    public function create(array $params)
    {
        return Admin::create($params);
    }

    /**
     * 根据id获取管理员资料
     * @param $id
     * @return mixed
     */
    public function ById($id)
    {
        return Admin::find($id);
    }

    /**
     * 获取管理员列表 with ('roles')
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAdminsWithRoles()
    {
        return Admin::with('roles')->latest('updated_at')->paginate('10');
    }

    /**
     * 根据name查询管理员资料
     * @param $name
     * @return mixed
     */
    public function ByName($name)
    {
        return Admin::where('name',$name)->first();
    }
}