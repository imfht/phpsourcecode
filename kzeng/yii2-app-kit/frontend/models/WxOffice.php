<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "wx_office".
 *
 * @property string $office_id
 * @property string $gh_id
 * @property string $scene_id
 * @property string $title
 * @property string $branch
 * @property string $region
 * @property string $address
 * @property string $manager
 * @property string $member_cnt
 * @property string $mobile
 * @property string $pswd
 * @property double $lat
 * @property double $lon
 * @property double $lat_bd09
 * @property double $lon_bd09
 * @property integer $visable
 * @property integer $is_jingxiaoshang
 * @property integer $role
 * @property integer $status
 * @property integer $is_selfOperated
 * @property integer $score
 */
class WxOffice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_office';
    }

    static function getWxOfficeOption($key=null)
    {
        $offices = WxOffice::find()->asArray()->all();
        foreach ($offices as $office) {
            $value = $office['title'];
            $arr[$value] = "{$office['title']}";
        }

        return $key === null ? $arr : (isset($arr[$key]) ? $arr[$key] : '');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['scene_id', 'member_cnt', 'visable', 'is_jingxiaoshang', 'role', 'status', 'is_selfOperated', 'score'], 'integer'],
            [['lat', 'lon', 'lat_bd09', 'lon_bd09'], 'number'],
            [['gh_id', 'manager'], 'string', 'max' => 32],
            [['title', 'branch', 'region', 'address'], 'string', 'max' => 128],
            [['mobile', 'pswd'], 'string', 'max' => 16],
            [['gh_id', 'title'], 'unique', 'targetAttribute' => ['gh_id', 'title'], 'message' => 'The combination of Gh ID and Title has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'office_id' => 'Office ID',
            'gh_id' => 'Gh ID',
            'scene_id' => 'Scene ID',
            'title' => 'Title',
            'branch' => 'Branch',
            'region' => 'Region',
            'address' => 'Address',
            'manager' => 'Manager',
            'member_cnt' => 'Member Cnt',
            'mobile' => 'Mobile',
            'pswd' => 'Pswd',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'lat_bd09' => 'Lat Bd09',
            'lon_bd09' => 'Lon Bd09',
            'visable' => 'Visable',
            'is_jingxiaoshang' => 'Is Jingxiaoshang',
            'role' => 'Role',
            'status' => 'Status',
            'is_selfOperated' => 'Is Self Operated',
            'score' => 'Score',
        ];
    }
}
