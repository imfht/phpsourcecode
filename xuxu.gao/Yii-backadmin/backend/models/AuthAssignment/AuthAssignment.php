<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/28
 * Time: 14:21
 */

namespace backend\models\AuthAssignment;


use backend\models\AuthItem\AuthItem;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class AuthAssignment extends ActiveRecord{

    public static function tableName()
    {
        return '{{%auth_assignment}}';
    }

    /**
     * 自动更新  created_at  updated_at
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemName()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'item_name']);
    }

}