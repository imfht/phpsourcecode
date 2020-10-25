<?php
// 外部模块调用配置
return array(
    'crm' => array(
        // 商机
        'opportunity' => array(
            'url' => 'crm/opportunity/detail',
            'key' => 'oid',
            'table' => '{{crm_opportunity}}',
            // 标题字段名
            'titlename' => 'subject',
            'urlidname' => 'id'
        ),
        // 客户
        'client' => array(
            'url' => 'crm/client/detail',
            'key' => 'cid',
            'table' => '{{crm_client}}',
            'titlename' => 'fullname',
            'urlidname' => 'id'
        ),
        // 跟进
        'event' => array(
            'url' => 'crm/event/index',
            'key' => 'eventid',
            'table' => '{{crm_event}}',
            'titlename' => 'content'
        ),
        // 合同
        'contract' => array(
            'url' => 'crm/contract/detail',
            'key' => 'contractid',
            'table' => '{{crm_contract}}',
            'titlename' => 'name',
            'urlidname' => 'id'
        ),
    )
);