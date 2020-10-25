<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/26
 * Time: 11:07
 */

namespace backend\services\permission\Impl;


use backend\models\AuthItem\AuthItem;
use backend\models\AuthItem\PermissionForm;
use backend\services\permission\IPermissionService;
use Yii;
class PermissionServiceImpl  implements IPermissionService{


    /**
     * 添加权限
     * @param array $params
     * @return mixed
     */
    public function addPermission($params = [])
    {
         $permission                = new PermissionForm();
         $permission->setScenario('create');
         $permission->name          = $params['name'];
         $permission->description   = $params['description'];
         $permission->typename      = $params['typename'];
         if($permission->validate()){

            return $permission->addPermission();

         }else{

             return $permission;
         }
    }

    /**
     * 权限列表
     * @param array $params
     * @return mixed
     */
    public function permissionList($params = [])
    {
        $list = AuthItem::dataPage($params);
        $is_delete  = false;
        $is_edit = false;
        //检查权限
        if(Yii::$app->user->can('admin.permission.permissiondelete')){

            $is_delete = true;
        }
        if(Yii::$app->user->can('admin.permission.permissionupdate')){

            $is_edit = true;
        }
        foreach ($list as &$item){

            $item['is_delete'] = $is_delete;
            $item['is_edit']   = $is_edit;
        }
        return $list;
    }

    /**
     * 记录数量
     * @param array $params
     * @return mixed
     */
    public function permissionCount($params = [])
    {
        return AuthItem::dataCount($params);
    }

    /**
     * 查询分组后的权限列表
     * @return mixed
     */
    public function permissionGroupByTypeName()
    {
        return AuthItem::find() ->select(['typename','GROUP_CONCAT(description) AS description','GROUP_CONCAT(name) AS name'])
                                ->where(['type'=>2])->groupBy('typename')
                                ->asArray()
                                ->all();
    }

    /**
     * 根据条件查询数据
     * @param array $params
     * @return mixed
     */
    public function queryPermission($params = [])
    {
        return AuthItem::find() ->where($params)
                                ->one();
    }

    /**
     * 更新权限
     * @param array $params
     * @return mixed
     */
    public function updatePermission($params = [])
    {
        $permission                = new PermissionForm();
        $permission->setScenario('update');
        $permission->name          = $params['name'];
        $permission->description   = $params['description'];
        $permission->typename      = $params['typename'];
        if($permission->validate()){

            return $permission->updatePermission($params['oldname']);
        }else{

            return $permission;
        }
    }

    /**
     * 删除权限
     * @param array $params
     * @return mixed
     */
    public function deletePermission($params = [])
    {
        return AuthItem::find() ->where($params)
                                ->one()
                                ->delete();
    }

    /**
     * 返回多个数据
     * @param array $params
     * @return mixed
     */
    public function queryAllPermission($params = [])
    {
        return AuthItem::find() ->where($params)
                                ->asArray()
                                ->all();
    }
}