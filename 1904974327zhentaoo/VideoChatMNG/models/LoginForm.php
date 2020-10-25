<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            ['username', 'required','message'=>'用户名不能为空'],
            ['password', 'required','message'=>'密码不能为空'],

            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['username', 'validateUsername'],
            ['password', 'validatePassword'],


        ];
    }

    public function validateUsername($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $result = User::find()->where(['name' => $this->username])->one();
            if (!$result) {
                $this->addError($attribute, '用户名不存在');
            }
        }
    }
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $result = User::find()->where(['name' => $this->username])->one();
            if ($this->password != $result->password) {
                $this->addError($attribute, '用户名或密码错误');
            }
        }
    }
    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
