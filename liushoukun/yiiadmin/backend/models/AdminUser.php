<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/10 15:36
// +----------------------------------------------------------------------
// | TITLE: 用户
// +----------------------------------------------------------------------


namespace backend\models;


class AdminUser extends BackendUser
{

    /**
     * 用户修改
     */
    const SCENARIO_USER_UPDATE = 'user_update';

    /**
     * 用户状态
     */
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * @var 密码
     */
    public $password;

    public function rules()
    {
        return [
            ['username', 'required', 'message' => '名称必须'],
            [['auth_key', 'password_hash', 'password_reset_token'], 'string', 'max' => 400],
            [['created_at', 'updated_at', 'role_id'], 'safe'],
            ['created_at', 'default', 'value' => self::getDate()],
            ['updated_at', 'default', 'value' => self::getDate()],
            ['email', 'email', 'message' => '请填写正确邮箱格式'],
            ['mobile', 'string', 'max' => 15],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
        ];

    }


    public static function tableName()
    {
        return '{{%admin_user}}';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] =
            [
                'username',
                'auth_key',
                'password_hash',
                'password_reset_token',
                'email',
                'created_at',
                'status',
                'role_id',
                'mobile'
            ];

        $scenarios[self::SCENARIO_UPDATE] =
            [
                'username',
                'auth_key',
                'password_hash',
                'password_reset_token',
                'email',
                'created_at',
                'status',
                'role_id',
                'mobile'
            ];
        $scenarios[self::SCENARIO_USER_UPDATE] = [
            'password_hash',
            'email',
            'mobile'
        ];
        return $scenarios;

    }

    public function attributeLabels()
    {

        return [
            'id' => '主键',
            'role_id' => '角色',
            'username' => '用户名称',
            'email' => '邮箱',
            'mobile' => '手机号',
            'status' => '状态',

        ];


    }

    public function attributeValues()
    {
        return [
            'status' => [
                '0' => '停用',
                '1' => '正常',
            ]
        ];

    }

    public static function getDate()
    {
        return date('Y-m-d H:i:s');
    }

    public static function deleteUser($id)
    {
        $model = self::findOne($id);
        if ($model) {
            $model->scenarios(self::SCENARIO_UPDATE);
            $model->status = self::STATUS_DELETED;
            $model->save();
            return ($model->save()) ? true : false;
        } else {
            return false;
        }
    }

    /**
     * 获取用户角色
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getAdminRole()
    {
        return $this->hasOne(AdminRole::className(), ['id' => 'role_id'])->asArray()->one();
    }


}