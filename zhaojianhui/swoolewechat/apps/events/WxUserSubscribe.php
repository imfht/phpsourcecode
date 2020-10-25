<?php
/**
 * 微信关注/取消关注事件触发定义
 */
return [
    'handlers' => [
        App\Handler\WxSyncUser::class,
        App\Handler\WxSaveUserSubLog::class,
    ]
];