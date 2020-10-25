<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "img".
 *
 * @property string $size
 * @property string $name
 * @property string $url
 * @property integer $id
 */
class img extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'img';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['size', 'name', 'url'], 'required'],
            [['size', 'name', 'url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'size' => 'Size',
            'name' => 'Name',
            'url' => 'Url',
            'id' => 'ID',
        ];
    }
}
