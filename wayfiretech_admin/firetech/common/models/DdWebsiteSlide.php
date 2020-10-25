<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-29 01:56:50
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:56:53
 */
 

namespace common\models;

use Yii;

/**
 * This is the model class for table "dd_website_slide".
 *
 * @property int $id
 * @property string|null $images
 * @property string|null $title
 * @property string|null $description
 * @property string|null $menuname
 * @property string|null $menuurl
 * @property string|null $createtime
 * @property string|null $updatetime
 */
class DdWebsiteSlide extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%website_slide}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['createtime', 'updatetime'], 'safe'],
            [['images', 'title', 'description', 'menuname', 'menuurl'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'images' => '图片',
            'title' => '标题',
            'description' => '描述',
            'menuname' => '按钮名称',
            'menuurl' => '按钮地址',
            'createtime' => 'Createtime',
            'updatetime' => 'Updatetime',
        ];
    }
}
