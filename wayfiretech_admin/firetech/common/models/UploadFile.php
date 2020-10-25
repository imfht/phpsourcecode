<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-21 22:01:08
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:57:40
 */
 

namespace common\models;

use diandi\admin\models\Bloc;
use diandi\admin\models\BlocStore;
use Yii;

/**
 * This is the model class for table "dd_upload_file".
 *
 * @property int $file_id 文件ID
 * @property string $storage 对象存储
 * @property int $group_id 文件分组
 * @property string $file_url 文件地址
 * @property string $file_name 文件名称
 * @property int $file_size 文件尺寸
 * @property string $file_type 文件类型
 * @property string $extension 文件后缀
 * @property int $is_delete 是否删除
 * @property int $bloc_id 公司ID
 * @property int $create_time 创建时间
 * @property int|null $store_id 商户ID
 *
 * @property DiandiBloc $bloc
 * @property UploadFileGroup $group
 * @property DiandiStore $store
 * @property UploadFileGroup[] $uploadFileGroups
 * @property UploadFileUsed[] $uploadFileUseds
 */
class UploadFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%upload_file}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'file_size', 'is_delete', 'bloc_id', 'create_time', 'store_id'], 'integer'],
            [['storage', 'file_type', 'extension'], 'string', 'max' => 20],
            [['file_url', 'file_name'], 'string', 'max' => 255],
            [['file_name'], 'unique'],
            // [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => UploadFileGroup::className(), 'targetAttribute' => ['group_id' => 'group_id']],
            [['bloc_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bloc::className(), 'targetAttribute' => ['bloc_id' => 'bloc_id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => BlocStore::className(), 'targetAttribute' => ['store_id' => 'store_id']],
        ];
    }

    public function behaviors()
    {
        /*自动添加创建和修改时间*/
        return [
            [
                'class' => \common\behaviors\SaveBehavior::className(),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'file_id' => '文件ID',
            'storage' => '对象存储',
            'group_id' => '文件分组',
            'file_url' => '文件地址',
            'file_name' => '文件名称',
            'file_size' => '文件尺寸',
            'file_type' => '文件类型',
            'extension' => '文件后缀',
            'is_delete' => '是否删除',
            'bloc_id' => '公司ID',
            'create_time' => '创建时间',
            'store_id' => '商户ID',
        ];
    }

    /**
     * Gets query for [[Bloc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBloc()
    {
        return $this->hasOne(Bloc::className(), ['bloc_id' => 'bloc_id']);
    }

    /**
     * Gets query for [[Group]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(UploadFileGroup::className(), ['group_id' => 'group_id']);
    }

    /**
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(BlocStore::className(), ['store_id' => 'store_id']);
    }

    /**
     * Gets query for [[UploadFileGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUploadFileGroups()
    {
        return $this->hasMany(UploadFileGroup::className(), ['file_id' => 'file_id']);
    }

    /**
     * Gets query for [[UploadFileUseds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUploadFileUseds()
    {
        return $this->hasMany(UploadFileUsed::className(), ['file_id' => 'file_id']);
    }
}
