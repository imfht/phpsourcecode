<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

use yii\helpers\Html;
use yii\helpers\Url;
/**
 * This is the model class for table "pform".
 *
 * @property integer $id
 * @property string $uid
 * @property string $title
 * @property integer $create_at
 * @property integer $updated_at
 * @property integer $user_id
 * @property string $description
 */
class Pform extends \yii\db\ActiveRecord
{
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pform';
    }

    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'title', 'created_at', 'updated_at', 'user_id'], 'required'],
            [['form_img_url', 'file', 'description', 'detail'], 'safe'],
            [['created_at', 'updated_at', 'user_id'], 'integer'],
            [['uid'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 255],
            [['detail'], 'string'],
            [['file'], 'file'],
            [['description'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '唯一编码',
            'title' => '表单名称',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'user_id' => '创建者',
            'description' => '简要描述',
            'detail' => '表单详情介绍',
            'form_img_url' => '图片',
            'file' => '图片文件',
        ];
    }


    public static function addMetadataAjax($form_uid, $field_title, $field_type, $field_value, $field_placeholder, $field_order ) {
        /*
         * @property integer $id
         * @property string $title
         * @property integer $type
         * @property string $value
         * @property string $placeholder
         * @property integer $sort
         * @property string $pform_uid
        */
        // var form_uid;
        // var field_title;
        // var field_type;
        // var field_value;
        // var field_placeholder;
        // var field_order;

        $metadata = new \backend\models\PformField;

        $metadata->title = $field_title;
        $metadata->type = $field_type;
        $metadata->value = $field_value;
        $metadata->placeholder = $field_placeholder;
        $metadata->sort = $field_order;
        $metadata->pform_uid = $form_uid;

        $metadata->save(false);

        return \yii\helpers\Json::encode(['code' => 0]);
    }

    public static function addCustomerFormData($form_uid, $myformfield_id, $myformfield) {
        $uniqid = uniqid();
        $myformfield_arr = explode('<====>', $myformfield);
        $myformfield_id_arr = explode('<====>', $myformfield_id);

         for ($i= 0;$i< count($myformfield_arr); $i++){
            $value= $myformfield_arr[$i];
            $pform_field_id = $myformfield_id_arr[$i];

            $cp = new \backend\models\CustomerPform;
            $cp->pform_uid = $form_uid;
            $cp->pform_field_id = $pform_field_id;
            $cp->value = $value;
            $cp->customer_pform_uid = $uniqid;

            $cp->save(false);
         }

        $endlink = Url::to(['/customer-pform/ok', 'form_uid' => $form_uid]);
        return \yii\helpers\Json::encode(['code' => 0, 'endlink' => $endlink]);
    }


    static function getFormField($model) {
        $formfields = \backend\models\PformField::find()
            ->where(["pform_uid" => $model->uid])
            ->all();

        $field_str = "";
        if(empty($formfields))
            return $field_str;

        foreach ($formfields as $formfield) {
            $field_str = $field_str.  Html::a('删除', ['delformfield', 'view_id' => $model->id, 'formfield_id' => $formfield->id], ['class' => 'btn btn-danger btn-xs']) . "【".$formfield->title."】<br>";
        }
        return "<span style='color:blue; font-size:14pt'>".$field_str."</span>";
    }
    // static function getStatistic() {

    // }
}
