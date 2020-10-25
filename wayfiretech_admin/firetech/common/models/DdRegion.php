<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-12 19:45:45
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:55:02
 */

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "dd_region".
 *
 * @property int         $id
 * @property int|null    $pid
 * @property string|null $shortname
 * @property string|null $name
 * @property string|null $merger_name
 * @property int|null    $level
 * @property string|null $pinyin
 * @property string|null $code
 * @property string|null $zip_code
 * @property string|null $first
 * @property string|null $lng
 * @property string|null $lat
 */
class DdRegion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%region}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'level'], 'integer'],
            [['shortname', 'name', 'pinyin', 'code', 'zip_code', 'lng', 'lat'], 'string', 'max' => 100],
            [['merger_name'], 'string', 'max' => 255],
            [['first'], 'string', 'max' => 50],
        ];
    }

    public static function getRegion($parentId = 0)
    {
        $result = static::find()->where(['pid' => $parentId])->asArray()->all();

        return ArrayHelper::map($result, 'id', 'name');
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'shortname' => 'Shortname',
            'name' => 'Name',
            'merger_name' => 'Merger Name',
            'level' => 'Level',
            'pinyin' => 'Pinyin',
            'code' => 'Code',
            'zip_code' => 'Zip Code',
            'first' => 'First',
            'lng' => 'Lng',
            'lat' => 'Lat',
        ];
    }
}
