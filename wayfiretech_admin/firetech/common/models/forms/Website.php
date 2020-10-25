<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-22 19:33:19
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-04-30 23:41:50
 */

namespace common\models\forms;

use yii\base\Model;

class Website extends Model
{
    /**
     * @var string application name
     */
    public $status;
    public $reason;
    public $icp;
    public $code;
    public $location;
    public $develop_status;
    public $flogo;
    public $blogo;
    public $notice;
    public $statcode;
    public $footerright;
    public $footerleft;
    public $name;
    public $intro;
    public $description;
    public $keywords;
    public $themcolor;
    public $bloc_id;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [[
                'status', 'name', 'reason', 'icp', 'code', 'location', 'develop_status', 'flogo', 'blogo',
                'notice', 'statcode', 'footerright', 'footerleft',
                'intro',
                'description',
                'keywords',
                'themcolor',
            ], 'string'],
            ['bloc_id', 'integer'],
            // [['status'],'in',['1'=>'关闭','0'=>'开启']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'name' => '站点名称',
            'intro' => '站点一句话说明',
            'description' => '站点简介',
            'keywords' => '站点关键字',
            'status' => '是否关闭站点',
            'reason' => '关闭站点的原因',
            'icp' => 'ICP备案',
            'code' => '备案号',
            'location' => '备案地址',
            'develop_status' => '是否开启调试',
            'flogo' => '前台logo',
            'blogo' => '后台logo',
            'notice' => '幻灯片中的文字',
            'statcode' => '第三方统计代码',
            'footerright' => '底部右侧',
            'footerleft' => '底部左侧',
            'themcolor' => '后台主题',
            'bloc_id' => '全局默认公司',
        ];
    }
}
