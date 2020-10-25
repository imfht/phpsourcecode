<?php
namespace modules\aliyunlog\models;

use yii\base\Model;

class Log extends Model
{
    public $time;
    
    public $topic;
    
    public $ip;

    public $content;
    
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'topic'=>'主题',
            'time' => '时间',
            'ip' => 'IP',
            'content' => '消息',
        ];
    }
}

