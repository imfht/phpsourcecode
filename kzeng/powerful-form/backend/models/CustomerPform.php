<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "customer_pform".
 *
 * @property integer $id
 * @property string $pform_uid
 * @property integer $pform_field_id
 * @property string $value
 */
class CustomerPform extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_pform';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pform_uid', 'pform_field_id', 'value'], 'required'],
            [['pform_field_id'], 'integer'],
            [['pform_uid'], 'string', 'max' => 64],
            [['value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pform_uid' => 'Pform Uid',
            'pform_field_id' => 'Pform Field ID',
            'value' => '字段内容',
        ];
    }

    public static function statistic() {
        $command = \Yii::$app->db->createCommand('SELECT id,title FROM pform_field where pform_uid = "' . Yii::$app->request->get('uid') .'"');
        $pformfield = $command->queryAll();
        $command = \Yii::$app->db->createCommand('SELECT * FROM customer_pform where pform_uid = "' . Yii::$app->request->get('uid') .'"');
        $customer_pform = $command->queryAll();
        $customer_pform = ArrayHelper::map($customer_pform, 'pform_field_id', 'value', 'customer_pform_uid');

        $return_data = [];

        foreach ( $pformfield as $key => $value ) {
            $return_data['title'][] = $value['title'];
        }
        foreach ( $customer_pform as $key => $value ) {
            $temp = [];
            foreach ( $pformfield as $k => $val ) {
                if(empty($value[$val['id']])) continue;
                $temp[] = $value[$val['id']];

            }
            $return_data['data'][] = $temp;
        }

        return $return_data;
    }
}
