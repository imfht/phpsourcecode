<?php

use think\Bridge\CacheBridge;
use think\Bridge\LoggerBridge;

return [
    'cache' => new CacheBridge(),
    'logger' => new LoggerBridge(),
    'corp_id' => 'wwda3a2b90ffc70040',
    'contacts' => [ //通讯录
        'token' => 'your-contacts-agent-token',
        'aes_key' => 'your-contacts-agent-aes-key',
        'secret' => 'rh8mrSlHZ5MgAW4RIxtytzpvfIfbvjL5PewTDZm0bSg',
    ],
    // 企业微信中的打卡记录的对接
    // oit 中 访问 web服务器中的控制器,web服务器对接企业微信中的外勤打卡记录
    // 1 在oit中定义 环境参数 web_site,指向企业微信中的域名(公司域名,不带www)
    // 2 在oit中定义 脚本，设置开始日期、结束日期，访问链接
    // 3 链接控制器接收参数，对接企业微信打卡数据，并写入数据库
    // 4 注意只能提取最近3个月的打卡数据，每次查询只能最多100名员工
    'chart' => [ // 企业微信打卡数据
        'agent_id' => '3010011',
        'secret' => '2ei3Q4-asQs9vSm35BtT6vpzzMumdPAGj-_OAumWY3o',
    ],
    'approval' => [ // 审批数据
        'agent_id' => '3010040',
        'secret' => 'UOwOFGmgZQmwVHLLFT3yQIrUu0v3qNbZQWqkN4Qpyxg',
    ],
    'Timer' => [ // 外勤管理
        'agent_id' => '1000002',
        'secret' => 'kguQEvd9w3NmMerl7jAyWJoz6cVU4H56cLIaH4R7xwg',
        'token' => 'Vho3vhxeSQ1EX4qiTVJMGv18nwGKujT',
        'aes_key' => '7ImCLAVee0sfp4B8Yxgz5ORgvozVXWdp9sozNsMSM6h',
    ],
    'Eba' => [ // 客户管理
        'agent_id' => '1000004',
        'secret' => 'GDv554QuEze2KPULgzlhePvPSdUc7vzw1WRfhmB1T1c',
        'token' => 'zvOlrDkmbAWr2H5cKpjo2xCTUyzoj',
        'aes_key' => 'PooYW8oa7DI3mpJYtJMH6ZYQQ18wbIMOWC6WfzQepAQ',
    ],
    'WorkLog' => [ // 工作日志
        'agent_id' => '1000005',
        'secret' => 'wgG95zzjX3TwDUEknXlcSM5JJKuC5spMXp6MOaSnzBw',
        'token' => 'p1sd9j98IG4u',
        'aes_key' => 'aPY1ksAfIo0zRfqRwfZdCOzXMIQLqfznwVmmxqhjouN',
    ],
    'VrNet' => [ // 客户订单
        'agent_id' => '1000003',
        'secret' => 'nq8Udd6enRgloANbH3sf7tFE49z5dju0Rb6X4jRvgFM',
        'token' => 'vGWAq5gde',
        'aes_key' => 'EbmyDzwyn2ZTDpjlskMV1HgfABlPQCFHcPdSrUN6x1P',
    ],
];
