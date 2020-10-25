<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-09-08 14:13:05
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-08 14:13:35
 */
 
/*** 
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:40
 * @LastEditTime: 2020-04-25 18:05:32
 */

namespace common\models\forms;
use Yii;
use yii\base\Model;

class Weburl extends Model
{
    /**
     * @var string application name
     */
    public $backendurl;

    /**
     * @var string admin email
     */
    public $frendurl;

    public $apiurl;
    
    public $urls;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['backendurl', 'frendurl','apiurl','urls'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'backendurl' => '后台地址',
            'frendurl' => '前台地址',
            'apiurl' => '接口地址',
            'urls'=>'跨域域名'
        ];
    }
}
