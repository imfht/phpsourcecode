<?php

return [
    'sms_code_can_send_after' => [
        'time' => 60, // 单位/s
        'message' => '同一手机号 60 秒内只能发送一条短信'
    ],
    'sms_code_expire_time' => [
        'time' => 5, // 单位/min
        'message' => '短信验证码已过期，请重新获取'
    ],
];
