<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/18
 * Time: 15:28
 */

namespace backend\services\user\Impl;


use backend\services\user\IUserService;
use common\models\User;
use common\models\UserForm;
use Yii;
class UserServiceImpl implements IUserService{

    /**
     * 添加用户
     * @param null $params
     * @return mixed
     */
    public function addUser($params = null)
    {
        $model = new UserForm();
        $model->setScenario('create');
        $model->username = $params['username'];
        $model->password = $params['password'];
        $model->email    = $params['email'];
        $model->roles    = $params['roles'];
        if($model->validate()){

            return $model->addUser();
        }else{

            return $model;
        }

    }

    /**
     * 用户列表
     * @param null $params
     * @return mixed
     */
    public function userList($params)
    {
        $list = User::dataPage($params);

        $is_delete  = false;
        $is_edit = false;
        //检查权限
        if(Yii::$app->user->can('admin.user.userdelete')){

            $is_delete = true;
        }
        if(Yii::$app->user->can('admin.user.userupdate')){

            $is_edit = true;
        }
        foreach ($list as &$item){

            $item['is_delete'] = $is_delete;
            $item['is_edit']   = $is_edit;
        }
        return $list;
    }
    /**
     * 用户数量
     * @param $params
     * @return mixed
     */
    public function userCount($params)
    {
        return User::dataCount($params);
    }

    /**
     * 根据用户id查询用户
     * @param $uid
     * @return mixed
     */
    public function getUserById($uid)
    {
        return User::findOne($uid);
    }

    /**
     * 更新用户
     * @param $params
     * @return mixed
     */
    public function updateUser($params)
    {
        $model = new UserForm();
        $model->setScenario('update');
        $model->username = $params['username'];
        $model->id       = $params['id'];
        $model->email    = $params['email'];
        $model->roles    = $params['roles'];
        if($model->validate()){

            return $model->updateUser();

        }else{

            return $model;
        }
    }

    /**
     * 根据id删除用户
     * @param $uid
     * @return mixed
     */
    public function deleteUserById($uid)
    {
        return User::findOne($uid)->delete();
    }
}