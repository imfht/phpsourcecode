<?php
/**
 * 行为扩展
 */
return [
    'module_init'=>[
        'app\\admin\\behavior\\Install',
        'app\\admin\\behavior\\SystemConfig',
    ],
    'action_begin' => [
        'app\\admin\\behavior\\CheckIp',
        'app\\admin\\behavior\\CheckAuth',
    ],
    'user_behavior'=>[
        'app\\admin\\behavior\\UserBehavior',
    ]
];
