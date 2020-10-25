<?php
namespace backend\modules\mp\models;

use yeesoft\helpers\AuthHelper;
use yeesoft\helpers\YeeHelper;
use Yii;
use yii\helpers\ArrayHelper;

class MpMenu extends \yeesoft\db\ActiveRecord {
    public static function tableName() {
        return '{{%mp_menu}}';
    }
    public function rules() {
        return [
            [['type', 'name', 'key', 'parent_id'], 'required'],
            [['id', 'parent_id', 'order'], 'integer'],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => '标识',
            'type' => '菜单类型',
            'name' => '菜单名称',
            'key' => '信息',
            'parent_id' => '父菜单标识',
            'order' => '排序',
        ];
    }
    
    public static function getMenuType() {
        return [
            'sub_button' => '父菜单',
            'miniprogram' => '小程序',
            'click' => '点击推事件',
            'view' => '网页',
            'scancode_waitmsg' => '扫码带提示',
            'scancode_push' => '扫码推事件',
            'pic_sysphoto' => '系统拍照发图',
            'pic_photo_or_album' => '拍照或者相册发图',
            'pic_weixin' => '微信相册发图',
            'location_select' => '发送位置',
            'media_id' => '图片',
            'view_limited' => '图文消息',
        ];
    }

    public static function getMenuLevelOne() {
        $data = [
            0 => '-= 一级菜单 =-',
        ];

        $temp = self::find()->where([
            'parent_id' => 0,
        ])->all();
        foreach ( $temp as $key => $value ) {
            $data[$value->id] = $value->name;
        }

        return $data;
    }
}