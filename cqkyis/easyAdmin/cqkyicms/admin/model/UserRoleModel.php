<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/11 21:59
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\model;


use think\Model;

class UserRoleModel extends Model
{

    protected  $name="system_user_role";


    public function add($data){
        return $this->save($data);
    }

}