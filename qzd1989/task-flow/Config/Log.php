<?php
return [
    'default'     => 'file',

    'connections' => [

        /*
        |--------------------------------------------------------------------------
        | Log
        |--------------------------------------------------------------------------
        |
        | Available Drivers: "file"
        | Available Channel: "daily", "stack"
        |
         */

        'file' => [
            'dir'     => TASKFLOW_ROOT . 'Log/',
            'channel' => 'stack',
        ],

    ],

];
