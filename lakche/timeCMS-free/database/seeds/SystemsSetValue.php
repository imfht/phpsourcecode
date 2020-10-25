<?php

use Illuminate\Database\Seeder;
use App\Model\System;

class SystemsSetValue extends Seeder
{
    public function run()
    {
        $system = [
            'title'=>'时光CMS',
            'keywords'=>'laravel,php,bootstrap,time,cms',
            'description'=>'时光CMS，基于laravel5.1的开源CMS系统。时光流逝那些朦胧的回忆，只留下最值得珍惜的瞬间。',
            'copyright'=>'© obday.com',
            'record'=>'',
            'is_open'=>1,
            'qq'=>'402227052',
            'wechat'=>'lakche',
            'wechatcode'=>'',
            'weibo'=>'332121900',
            'theme'=>'time',
            'subtitle'=>'时光如水'
        ];

        System::saveValue($system);
    }
}
