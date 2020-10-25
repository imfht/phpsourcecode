<?php

$log['master'] = [
    'type' => 'FileLog',
    'file' => WEBPATH . '/logs/app.log',
];

$log['test'] = [
    'type' => 'FileLog',
    'file' => WEBPATH . '/logs/test.log',
];

return $log;
