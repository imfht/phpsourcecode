<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/10 15:43
// +----------------------------------------------------------------------
// | TITLE: 角色
// +----------------------------------------------------------------------


namespace backend\models;


class AdminRole extends BaseModel
{

    /**
     * 超级管理员分组
     */
    const ADMIN_ID = 1;

    public static $statusList = [
        1 => '开启',
        0 => '关闭',
    ];


    public static function tableName()
    {
        return 'admin_role';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['code', 'name', 'create_date', 'des',];
        $scenarios[self::SCENARIO_UPDATE] = ['code', 'name', 'update_user', 'des', 'update_date', 'rule'];
        return $scenarios;

    }


    public function rules()
    {
        return [
            ['code', 'required', 'message' => '编号必须'],
            ['name', 'required', 'message' => '名称必须'],
            [['create_date', 'update_date'], 'safe'],
            ['update_date', 'default', 'value' => self::getDate()],
            ['create_date', 'default', 'value' => self::getDate()],
            [['code', 'name', 'create_user', 'update_user'], 'string', 'max' => 50],
            [['des'], 'string', 'max' => 400],
            ['rule', 'string',]
        ];

    }

    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'code' => '角色编号',
            'name' => '角色名称',
            'des' => '角色描述',
            'create_user' => '创建人',
            'create_date' => '创建时间',
            'update_user' => '更新人',
            'update_date' => '更新时间',
            'rule' => '权限',
            'status' => '状态',
        ];
    }

    public static function getDate()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 转换状态
     * @param $status
     * @return mixed
     */
    public static function status_to_str($status)
    {
        return self::$statusList[$status];
    }

    /**
     * 删除角色
     * @param $id
     * @return bool
     */
    public static function deleteRole($id)
    {
        $model = self::findOne($id);
        if ($model) {
            $model->status = 0;
            $model->save();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取权限
     * @param $id 用户角色
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRule($id)
    {
        $AdminRule = AdminRule::find();
        $AdminRule->where(['status' => 1]);
        $AdminRule->andWhere(['is_show' => 1]);
        $AdminRule->orderBy('order desc');
        if (self::ADMIN_ID != $id) {
            $roleOne = AdminRole::findOne($id);
            $roleOne->rule = explode(',', $roleOne->rule);
            $AdminRule->andWhere(['in', 'id', $roleOne->rule]);
        }
        return $AdminRule->asArray()->all();
    }


}