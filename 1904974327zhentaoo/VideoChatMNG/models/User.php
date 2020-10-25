<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;


/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const ROLE_USER = 10;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%manager}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'password'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['password'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'password' => Yii::t('app', 'Password'),
        ];
    }

    public static function findByUsername($username)
    {
        return static::findOne([
            'name' => $username,
//            'status' => self::STATUS_ACTIVE
        ]);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = NULL)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
//        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
//        return $this->getAuthKey() === $authKey;
    }
}