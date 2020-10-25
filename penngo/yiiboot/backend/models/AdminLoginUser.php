<?php
namespace backend\models;

use Yii;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

/**
 * Login form
 */
class AdminLoginUser extends AdminUser implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

  

    /**
     *
     */
    public static function validatePassword($user, $password)
    {
        return ($user != null && $user->password == $password);
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login($username, $password, $rememberMe)
    {
        $user = self::findByUsername($username);
        
        if (self::validatePassword($user, $password) == true) {
            $this->setAttributes($user->getAttributes());
            var_dump($this->getAttributes());
            return Yii::$app->admin_user->login($this, $rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }
    
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return AdminUser::findOne(['uname' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    
  
    
    public static function findIdentity($id){
        return AdminUser::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }
    
     
    public static function findIdentityByAccessToken($token, $type = null){
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
    
    /**
     * cookie
     * @see \yii\web\IdentityInterface::getId()
     */
    public function getId(){
        return $this->uname;
    }
    
    /**
     * cookie登录需要实现
     * @see \yii\web\IdentityInterface::getAuthKey()
     */
    public function getAuthKey(){
        return $this->auth_key;
    }
    
    /**
     * cookie登录需要实现
     * @see \yii\web\IdentityInterface::getAuthKey()
     */
    public function validateAuthKey($authKey){
        return $this->getAuthKey() === $authKey;
    }
}
