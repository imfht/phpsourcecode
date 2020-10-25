<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/25
 * Time: 15:03
 */

namespace backend\services\role\Impl;

use backend\models\AuthItem\AuthItem;
use backend\models\AuthItem\RoleFrom;
use backend\services\role\IRoleService;
use Yii;
class RoleServiceImpl implements IRoleService{


    /**
     * 添加角色
     * @param array $params
     * @return mixed
     */
    public function addRole($params = [])
    {
        $role               = new RoleFrom();
        $role->setScenario('create');
        $role->name         = $params['name'];
        $role->description  = $params['description'];
        if($role->validate()){

            return $role->addRole();

        }else{
            return $role;
        }
    }

    /**
     * 角色列表
     * @param array $params
     * @return mixed
     */
    public function roleList($params = [])
    {
        $list = AuthItem::dataPage($params);
        $is_delete  = false;
        $is_edit = false;
        //检查权限
        if(Yii::$app->user->can('admin.role.roledelete')){

            $is_delete = true;
        }
        if(Yii::$app->user->can('admin.role.roleupdate')){

            $is_edit = true;
        }
        foreach ($list as &$item){

            $item['is_delete'] = $is_delete;
            $item['is_edit']   = $is_edit;
        }

        return $list;
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function roleCount($params = [])
    {
        return AuthItem::dataCount($params);
    }

    /**
     * 权限分配
     * @param array $params
     * @return mixed
     */
    public function assignRole($params = [])
    {
        //当前角色对象
        $role = Yii::$app->authManager->getRole($params['name']);
        Yii::$app->authManager->removeChildren($role);

        //权限添加
        foreach ($params['roles'] as $item) {

            $childObj = Yii::$app->authManager->getPermission($item);
            //给item_child表写入数据（权限表）
            Yii::$app->authManager->addChild($role, $childObj);
        }

        return true;
    }

    /**
     * 更新角色数据
     * @param array $params
     * @return mixed
     */
    public function roleUpdate($params = [])
    {

        $role               = new RoleFrom();
        $role->setScenario('create');
        $role->name         = $params['name'];
        $role->description  = $params['description'];
        if($role->validate()){

            return $role->updateRole($params['oldname']);
        }else{

            return $role;
        }
    }

    /**
     * 根据条件获取权限数据
     * @param array $params
     * @return mixed
     */
    public function queryRoleByWhere($params = [])
    {
        return AuthItem::find() ->where($params)
                                ->one();
    }

    /**
     * 删除角色
     * @param array $params
     * @return mixed
     */
    public function deleteRole($params = [])
    {
       return AuthItem::find()  ->where($params)
                                ->one()
                                ->delete();

    }

    /**
     * 根据条件获取多条数据记录
     * @param array $params
     * @return mixed
     */
    public function queryAllRoleByWhere($params = [])
    {
        return AuthItem::find() ->where($params)
                                ->asArray()
                                ->all();
    }
}