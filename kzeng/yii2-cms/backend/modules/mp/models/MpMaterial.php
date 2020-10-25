<?php
namespace backend\modules\mp\models;

use yeesoft\helpers\AuthHelper;
use yeesoft\helpers\YeeHelper;
use Yii;
use yii\helpers\ArrayHelper;

class MpMaterial extends \yeesoft\db\ActiveRecord {
    public static function tableName() {
        return '{{%mp_material}}';
    }

    public function rules() {
        return [
            [['type'], 'required'],
            [['media_id', 'name', 'url', 'content'], 'string'],
            [['update_time'], 'integer'],
        ];
    }


    public function attributeLabels() {
        return [
            'id' => '标识',
            'media_id' => '媒体ID',
            'name' => '名称',
            'update_time' => '更新时间',
            'url' => '链接',
            'type' => '素材类型',
            'content' => '内容',
        ];
    }





}