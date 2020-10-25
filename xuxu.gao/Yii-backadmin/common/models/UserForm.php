<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/18
 * Time: 14:44
 */

namespace common\models;

use backend\models\AuthAssignment\AuthAssignment;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use Yii;
class UserForm extends Model{


    public $id = 0;
    public $username;
    public $password;
    public $email;
    public $roles;
    public function rules()
    {
        return [
            // username and password are both required
            ['username', 'unique','targetClass' => '\common\models\User','message' => '用户名已经存在','on'=>['create','update']],
            ['username','required','message'=>'用户名不能为空'],
            ['password','required','message'=>'密码不能为空'],
            // rememberMe must be a boolean value
            ['email','required','message'=>'邮箱不能为空'],
            ['email', 'email','message'=>'邮箱格式不正确']
        ];
    }

    /**
     * 添加用户
     * @return bool|User
     */
    public function addUser(){

        $user           = new User();
        $user->username = $this->username;
        $user->email    = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        if ($user->save()) {

            //分配角色
            if(!empty($this->roles)){

                foreach ($this->roles as $item){
                    $role = Yii::$app->authManager->getRole($item);
                    Yii::$app->authManager->assign( $role, $user->id );
                }
            }
            return true;
        }else{

            return false;
        }
    }
    public function updateUser(){
        $user           = User::findOne($this->id);
        $user->username = $this->username;
        $user->email    = $this->email;
        if ($user->save()) {

            //角色更改
            if(!empty($this->roles)){

                AuthAssignment::deleteAll(['user_id'=>$this->id]);
                foreach ($this->roles as $item){

                    $role = Yii::$app->authManager->getRole($item);
                    Yii::$app->authManager->assign( $role, $user->id );
                }
            }
            return $user;
        }else{

            return false;
        }
    }
    // 更新 ，添加，场景
    public function scenarios()
    {
        return [
            'create' => ['username', 'password', 'email'],
            'update' => ['email']
        ];
    }
    //验证用户名是否重复
    public function beforeValidate()
    {
        $ret = User::find()->where(['username'=>$this->username])->asArray()->one();

        if($ret) {

            if ($ret['id'] == $this->id) {

                return true;
            } else {

                $this->addError('username', '用户名不能重复');
                return false;
            }
        }else{
            return true;
        }
    }
}