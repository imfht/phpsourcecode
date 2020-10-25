<?php
namespace backend\models;

use Yii;

/**
 * This is the model class for table "admin_user_role".
 *
 * @property integer $id
 * @property string $user_id
 * @property integer $role_id
 * @property string $create_user
 * @property string $create_date
 * @property string $update_user
 * @property string $update_date
 *
 * @property AdminUser $user
 * @property AdminRole $role
 */
class AdminUserRole extends \backend\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_user_role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'role_id'], 'required'],
            [['user_id', 'role_id'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['create_user', 'update_user'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'user_id' => '用户id',
            'role_id' => '角色',
            'create_user' => '创建人',
            'create_date' => '创建时间',
            'update_user' => '修改人',
            'update_date' => '修改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(AdminRole::className(), ['id' => 'role_id']);
    }

  /**
     * 返回数据库字段信息，仅在生成CRUD时使用，如不需要生成CRUD，请注释或删除该getTableColumnInfo()代码
     * COLUMN_COMMENT可用key如下:
     * label - 显示的label
     * inputType 控件类型, 包含text,select,checkbox,radio,file,password,hidden
     * isEdit   是否允许编辑，如果允许编辑将在添加和修改时输入
     * isSearch 是否允许搜索
     * isDisplay 是否在列表中显示
     * isOrder 是否排序
     * udc - udc code，inputtype为select,checkbox,radio三个值时用到。
     * 特别字段：
     * id：主键。必须含有主键，统一都是id
     * create_date: 创建时间。生成的代码自动赋值
     * update_date: 修改时间。生成的代码自动赋值
     */
    public function getTableColumnInfo(){
        return array(
        'id' => array(
                        'name' => 'id',
                        'allowNull' => false,
//                         'autoIncrement' => true,
//                         'comment' => '主键',
//                         'dbType' => "int(11)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => true,
                        'phpType' => 'integer',
                        'precision' => '11',
                        'scale' => '',
                        'size' => '11',
                        'type' => 'integer',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('id'),
                        'inputType' => 'hidden',
                        'isEdit' => true,
                        'isSearch' => true,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'user_id' => array(
                        'name' => 'user_id',
                        'allowNull' => false,
//                         'autoIncrement' => false,
//                         'comment' => '用户',
//                         'dbType' => "bigint(20) unsigned",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'string',
                        'precision' => '20',
                        'scale' => '',
                        'size' => '20',
                        'type' => 'bigint',
                        'unsigned' => true,
                        'label'=>$this->getAttributeLabel('user_id'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => true,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'role_id' => array(
                        'name' => 'role_id',
                        'allowNull' => false,
//                         'autoIncrement' => false,
//                         'comment' => '角色',
//                         'dbType' => "int(11)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'integer',
                        'precision' => '11',
                        'scale' => '',
                        'size' => '11',
                        'type' => 'integer',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('role_id'),
                        'inputType' => 'hidden',
                        'isEdit' => false,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'create_user' => array(
                        'name' => 'create_user',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '创建人',
//                         'dbType' => "varchar(50)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'string',
                        'precision' => '50',
                        'scale' => '',
                        'size' => '50',
                        'type' => 'string',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('create_user'),
                        'inputType' => 'text',
                        'isEdit' => false,
                        'isSearch' => false,
                        'isDisplay' => false,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'create_date' => array(
                        'name' => 'create_date',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '创建时间',
//                         'dbType' => "datetime",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'string',
                        'precision' => '',
                        'scale' => '',
                        'size' => '',
                        'type' => 'datetime',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('create_date'),
                        'inputType' => 'text',
                        'isEdit' => false,
                        'isSearch' => false,
                        'isDisplay' => false,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'update_user' => array(
                        'name' => 'update_user',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '修改人',
//                         'dbType' => "varchar(50)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'string',
                        'precision' => '50',
                        'scale' => '',
                        'size' => '50',
                        'type' => 'string',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('update_user'),
                        'inputType' => 'text',
                        'isEdit' => false,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'update_date' => array(
                        'name' => 'update_date',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '修改时间',
//                         'dbType' => "datetime",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'string',
                        'precision' => '',
                        'scale' => '',
                        'size' => '',
                        'type' => 'datetime',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('update_date'),
                        'inputType' => 'text',
                        'isEdit' => false,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		        );
        
    }
 
}
