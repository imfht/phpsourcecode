<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-29 01:54:14
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:54:17
 */
 

namespace common\models;

use Yii;

/**
 * This is the model class for table "dd_dictionary".
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $value
 */
class DdDictionary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dictionary}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'required'],
            [['type'], 'string', 'max' => 30],
            [['name', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'value' => 'Value',
        ];
    }
}
