<?php
/**
 * Standard Web Server Config
 */
return [
    'address'         => '0.0.0.0',
    'port'            => 8080,
    'worker_num'      => 16,
    'deamonize'       => FALSE,
    'max_request'     => 10000,
    'task_worker_num' => 2, //设置task worker数量
    'dispatch_mode'   => 1,
    'log_file'        => NULL,
];
