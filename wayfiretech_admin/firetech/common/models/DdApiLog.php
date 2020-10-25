<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-29 01:51:53
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:51:56
 */
 

namespace common\models;

use Yii;

/**
 * This is the model class for table "dd_api_log".
 *
 * @property int $id
 * @property string|null $method 提交类型
 * @property string|null $url 提交url
 * @property string|null $get_data get数据
 * @property string|null $post_data post数据
 * @property string|null $ip ip地址
 * @property int|null $append 创建时间
 */
class DdApiLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%api_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['get_data', 'post_data'], 'string'],
            [['append'], 'integer'],
            [['method'], 'string', 'max' => 20],
            [['url'], 'string', 'max' => 1000],
            [['ip'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'method' => 'Method',
            'url' => 'Url',
            'get_data' => 'Get Data',
            'post_data' => 'Post Data',
            'ip' => 'Ip',
            'append' => 'Append',
        ];
    }
}
