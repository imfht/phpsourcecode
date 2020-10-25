<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-04 18:42:55
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-04 18:44:03
 */
 
/*** 
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:40
 * @LastEditTime: 2020-04-25 19:10:24
 */

namespace common\models\forms;
use Yii;
use yii\base\Model;

class Wxapp extends Model
{
    /**
     * @var string application name
     */
    public $name;

    /**
     * @var string admin email
     */
    public $description;
    public $original;
    public $AppId;
    public $AppSecret;
    public $headimg;
    public $codeUrl;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [[
                'name',
                'description',
                'original',
                'AppId',
                'AppSecret',
                'headimg',
                'codeUrl'
            ], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
           'name'=>'小程序名称',
            'description'=>'小程序描述',
            'original'=>'原始id',
            'AppId'=>'AppId',
            'AppSecret'=>'AppSecret',
            'headimg'=>'二维码',
            'codeUrl'=>'普通二维码链接',
        ];
    }
}
