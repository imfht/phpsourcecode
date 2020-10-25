<?php
/*** 
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:40
 * @LastEditTime: 2020-04-25 18:05:07
 */

namespace common\models\forms;
use Yii;
use yii\base\Model;

class Baidu extends Model
{

    /**
     * @var string application name
     */
    public $APP_ID;
    public $name;

    /**
     * @var string admin email
     */
    public $API_KEY;

    /**
     * @var string 
     */
    public $SECRET_KEY;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['APP_ID', 'API_KEY','SECRET_KEY','name'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'APP_ID' => Yii::t('app', 'APP_ID'),
            'API_KEY' => Yii::t('app', 'API_KEY'),
            'SECRET_KEY' => Yii::t('app', 'SECRET_KEY'),
            'name'=>'应用名称'
        ];
    }
}
