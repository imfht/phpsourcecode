<?php

return [
    //请将以下配置拷贝到 ./Config/app.php 文件对应的位置中
    'DB_CONFIG_ELASTICSEARCH' => [
        'DB_HOST' => 'your DB_HOST',//es服务ip
        'DB_PORT' => 'your DB_PORT',//es服务端口
        'DB_INDEX' => 'your DB_INDEX',  //默认index 通过switchIndex 切换
        'DB_TABLE' => 'your DB_TABLE',//默认table 通过switchTable 切换
    ]
];


