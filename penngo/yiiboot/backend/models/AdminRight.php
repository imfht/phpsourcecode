<?php
namespace backend\models;

use Yii;
use backend\models\AdminRightUrl;
/**
 * This is the model class for table "admin_right".
 *
 * @property integer $id
 * @property integer $menu_id
 * @property string $right_name
 * @property string $display_label
 * @property string $des
 * @property integer $display_order
 * @property string $has_lef
 * @property string $create_user
 * @property string $create_date
 * @property string $update_user
 * @property string $update_date
 *
 * @property AdminMenu $menu
 * @property AdminRightUrl[] $adminRightUrls
 */
class AdminRight extends \backend\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_right';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_id', 'right_name'], 'required'],
            [['menu_id', 'display_order'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['right_name', 'display_label', 'des'], 'string', 'max' => 200],
            [['has_lef'], 'string', 'max' => 1],
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
            'menu_id' => '功能主键',
            'right_name' => '名称',
            'display_label' => '显示名',
            'des' => '描述',
            'display_order' => '显示顺序',
            'has_lef' => '是否有子',
            'create_user' => '创建人',
            'create_date' => '创建时间',
            'update_user' => '修改人',
            'update_date' => '修改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenu()
    {
        return $this->hasOne(AdminMenu::className(), ['id' => 'menu_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdminRightUrls()
    {
        return $this->hasMany(AdminRightUrl::className(), ['right_id' => 'id']);
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
		'menu_id' => array(
                        'name' => 'menu_id',
                        'allowNull' => false,
//                         'autoIncrement' => false,
//                         'comment' => '功能主键',
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
                        'label'=>$this->getAttributeLabel('menu_id'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'right_name' => array(
                        'name' => 'right_name',
                        'allowNull' => false,
//                         'autoIncrement' => false,
//                         'comment' => '名称',
//                         'dbType' => "varchar(200)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'string',
                        'precision' => '200',
                        'scale' => '',
                        'size' => '200',
                        'type' => 'string',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('right_name'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'display_label' => array(
                        'name' => 'display_label',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '显示名',
//                         'dbType' => "varchar(200)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'string',
                        'precision' => '200',
                        'scale' => '',
                        'size' => '200',
                        'type' => 'string',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('display_label'),
                        'inputType' => 'text',
                        'isEdit' => false,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'des' => array(
                        'name' => 'des',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '描述',
//                         'dbType' => "varchar(200)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'string',
                        'precision' => '200',
                        'scale' => '',
                        'size' => '200',
                        'type' => 'string',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('des'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'display_order' => array(
                        'name' => 'display_order',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '显示顺序',
//                         'dbType' => "int(5)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'integer',
                        'precision' => '5',
                        'scale' => '',
                        'size' => '5',
                        'type' => 'integer',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('display_order'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'has_lef' => array(
                        'name' => 'has_lef',
                        'allowNull' => false,
//                         'autoIncrement' => false,
//                         'comment' => '是否有子',
//                         'dbType' => "varchar(1)",
                        'defaultValue' => 'n',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'string',
                        'precision' => '1',
                        'scale' => '',
                        'size' => '1',
                        'type' => 'string',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('has_lef'),
                        'inputType' => 'text',
                        'isEdit' => false,
                        'isSearch' => false,
                        'isDisplay' => false,
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
