<?php
/**
 * 通用主动提醒配置文件
 * 一维:模块
 * 二维:事件节点
 * 三维:时间节点
 */

use application\core\utils\Ibos;

return array(
    // 消息提醒模块 TODO 每个模块放到各个模块中
    'message' => array(
        // 普通提醒
        'normal_alarm_notily' => array(
            'alarmType' => array(0), // 支持提醒类型,一定要有 0 自定义时间 1 关联事件时间
            'eventName' => '普通提醒',
            'eventIdKey'=>'', // 事件idkey值，用于找确定列表事件
            'timeNodes' => array()
        )
    ),
    // 会议模块提醒模块
    'meeting' => array(
        'meeting_management' => array(
            'alarmType' => array(0,1), // 支持提醒类型,一定要有 0 自定义时间 1 关联事件时间
            'eventName' => '会议管理',
            'eventUrl'  => 'meeting/manager/show', // 事件url不带参数
            'eventIdKey'=>'mid',
            'timeNodes' => array(
                array(
                    'tableName' => '{{meeting}}', // 关联事件表名
                    'fieldName' => 'begin', // 关联事件时间表字段名
                    'timeName' => '会议开始时', // 事件名称
                    'idName' => 'mid', // 关联事件表的id名称
                    'timeNode' => 'meeting_begin_time',
                    'diffeTime' =>array(
                        Ibos::lang('Not in advance') => '0',
                        Ibos::lang('Five minutes early') => '-5',
                        Ibos::lang('Ten minutes ahead of schedule') => '-10',
                        Ibos::lang('Half an hour early') => '-30',
                        Ibos::lang('1 hour in advance') => '-60',
                        Ibos::lang('1 day in advance') => '-1440',
                        Ibos::lang('2 days in advance') => '-2880',
                    ) // 差异时间
                ),
                array(
                    'tableName' => '{{meeting}}', // 关联事件表名
                    'fieldName' => 'end', // 关联事件时间表字段名
                    'timeName' => '会议结束时', // 事件名称
                    'idName' => 'mid', // 关联事件表的id名称
                    'timeNode' => 'meeting_end_time',
                    'diffeTime' =>array(
                        Ibos::lang('Not in advance') => '0',
                        Ibos::lang('Five minutes early') => '-5',
                        Ibos::lang('Ten minutes ahead of schedule') => '-10',
                        Ibos::lang('Half an hour early') => '-30',
                        Ibos::lang('1 hour in advance') => '-60',
                        Ibos::lang('1 day in advance') => '-1440',
                        Ibos::lang('2 days in advance') => '-2880',
                    ) // 差异时间
                )
            )
        )
    ),
    // 任务指派
    "assignment" => array(
        'assignment_task' => array(
            'alarmType' => array(0,1),
            'eventName' => '任务指派',
            'eventUrl'  => 'assignment/default/show',
            'eventIdKey'=>'assignmentid',
            'timeNodes' => array(
                array(
                    'tableName' => '{{assignment}}',
                    'fieldName' => 'starttime',
                    'timeName' => '任务开始时',
                    'idName' => 'assignmentid',
                    'timeNode' => 'assignment_start_time',
                    'diffeTime' =>array(
                        Ibos::lang('Not in advance') => '0',
                        Ibos::lang('Five minutes early') => '-5',
                        Ibos::lang('Ten minutes ahead of schedule') => '-10',
                        Ibos::lang('Half an hour early') => '-30',
                        Ibos::lang('1 hour in advance') => '-60',
                        Ibos::lang('1 day in advance') => '-1440',
                        Ibos::lang('2 days in advance') => '-2880',
                    )
                ),
                array(
                    'tableName' => '{{assignment}}',
                    'fieldName' => 'endtime',
                    'timeName' => '任务结束时',
                    'idName' => 'assignmentid',
                    'timeNode' => 'assignment_end_time',
                    'diffeTime' =>array(
                        Ibos::lang('Not in advance') => '0',
                        Ibos::lang('Five minutes early') => '-5',
                        Ibos::lang('Ten minutes ahead of schedule') => '-10',
                        Ibos::lang('Half an hour early') => '-30',
                        Ibos::lang('1 hour in advance') => '-60',
                        Ibos::lang('1 day in advance') => '-1440',
                        Ibos::lang('2 days in advance') => '-2880',
                    )
                )
            )
        )
    ),
    // 调查投票
    "vote" => array(
        'vote_survey' => array(
            'alarmType' => array(0,1),
            'eventName' => '调查投票',
            'eventUrl'  => 'vote/default/show',
            'eventIdKey'=>'voteid',
            'timeNodes' => array(
                array(
                    'tableName' => '{{vote}}',
                    'fieldName' => 'endtime',
                    'timeName' => '投票结束时',
                    'idName' => 'voteid',
                    'timeNode' => 'vote_end_time',
                    'diffeTime' =>array(
                        Ibos::lang('Not in advance') => '0',
                        Ibos::lang('Five minutes early') => '-5',
                        Ibos::lang('Ten minutes ahead of schedule') => '-10',
                        Ibos::lang('Half an hour early') => '-30',
                        Ibos::lang('1 hour in advance') => '-60',
                        Ibos::lang('1 day in advance') => '-1440',
                        Ibos::lang('2 days in advance') => '-2880',
                    )
                )
            )
        )
    ),
    // 资产管理
    "assets" => array(
        'fixed_assets' => array(
            'alarmType' => array(0),
            'eventName' => '固定资产',
            'eventUrl'  => 'assets/fixed/detail',
            'eventIdKey'=>'id',
            'timeNodes' => array()
            // 主表为 assets_asset, idname 为id
        )
    ),
    // 活动中心
    "activity" => array(
        'activity_center' => array(
            'alarmType' => array(0,1),
            'eventName' => '活动中心',
            'eventUrl'  => 'activity/manage/detail',
            'eventIdKey'=>'activityid',
            'timeNodes' => array(
                array(
                    'tableName' => '{{activity}}',
                    'fieldName' => 'begin',
                    'timeName' => '活动开始时',
                    'idName' => 'activityid',
                    'timeNode' => 'activity_begin_time',
                    'diffeTime' =>array(
                        Ibos::lang('Not in advance') => '0',
                        Ibos::lang('Five minutes early') => '-5',
                        Ibos::lang('Ten minutes ahead of schedule') => '-10',
                        Ibos::lang('Half an hour early') => '-30',
                        Ibos::lang('1 hour in advance') => '-60',
                        Ibos::lang('1 day in advance') => '-1440',
                        Ibos::lang('2 days in advance') => '-2880',
                    )
                ),
                array(
                    'tableName' => '{{activity}}',
                    'fieldName' => 'end',
                    'timeName' => '活动结束时',
                    'idName' => 'activityid',
                    'timeNode' => 'activity_end_time',
                    'diffeTime' =>array(
                        Ibos::lang('Not in advance') => '0',
                        Ibos::lang('Five minutes early') => '-5',
                        Ibos::lang('Ten minutes ahead of schedule') => '-10',
                        Ibos::lang('Half an hour early') => '-30',
                        Ibos::lang('1 hour in advance') => '-60',
                        Ibos::lang('1 day in advance') => '-1440',
                        Ibos::lang('2 days in advance') => '-2880',
                    )
                )
            )
        )
    ),
    // 项目主线
        "thread" => array(
        'project_thread' => array(
            'alarmType' => array(0,1),
            'eventName' => '项目主线',
            'eventUrl'  => 'thread/detail/show',
            'eventIdKey'=>'threadid',
            'timeNodes' => array(
                array(
                    'tableName' => '{{thread}}',
                    'fieldName' => 'starttime',
                    'timeName' => '项目开始时',
                    'idName' => 'threadid',
                    'timeNode' => 'thread_start_time',
                    'diffeTime' =>array(
                        Ibos::lang('Not in advance') => '0',
                        Ibos::lang('Five minutes early') => '-5',
                        Ibos::lang('Ten minutes ahead of schedule') => '-10',
                        Ibos::lang('Half an hour early') => '-30',
                        Ibos::lang('1 hour in advance') => '-60',
                        Ibos::lang('1 day in advance') => '-1440',
                        Ibos::lang('2 days in advance') => '-2880',
                    )
                ),
                array(
                    'tableName' => '{{thread}}',
                    'fieldName' => 'endtime',
                    'timeName' => '项目结束时',
                    'idName' => 'threadid',
                    'timeNode' => 'thread_end_time',
                    'diffeTime' =>array(
                        Ibos::lang('Not in advance') => '0',
                        Ibos::lang('Five minutes early') => '-5',
                        Ibos::lang('Ten minutes ahead of schedule') => '-10',
                        Ibos::lang('Half an hour early') => '-30',
                        Ibos::lang('1 hour in advance') => '-60',
                        Ibos::lang('1 day in advance') => '-1440',
                        Ibos::lang('2 days in advance') => '-2880',
                    )
                )
            )
        )
    ),
    // 工作流
    "workflow" => array(
        'handling_work' => array(
            'alarmType' => array(0),
            'eventName' => '办理工作',
            'eventUrl'  => 'workflow/form/index',
            'eventIdKey'=>'runid',
            'timeNodes' => array()
        )
    ),
    // CRM
    "crm" => array(
        // 跟进
        'event' => array(
            'alarmType' => array(0),
            'eventName' => '跟进',
            'eventUrl'  => 'crm/event/index',
            'eventIdKey'=>'eventid',
            'timeNodes' => array(),
        ),
        // 合同
        'contract' => array(
            'alarmType' => array(0,1),
            'eventName' => '合同',
            'eventUrl'  => 'crm/contract/detail',
            'eventIdKey'=>'contractid',
            'timeNodes' => array(
                array(
                    'tableName' => '{{crm_contract}}',
                    'fieldName' => 'signdate',
                    'timeName' => '合同开始时',
                    'idName' => 'contractid',
                    'timeNode' => 'contract_start_time',
                    'diffeTime' =>array(
                        Ibos::lang('Not in advance') => '0',
                        Ibos::lang('Five minutes early') => '-5',
                        Ibos::lang('Ten minutes ahead of schedule') => '-10',
                        Ibos::lang('Half an hour early') => '-30',
                        Ibos::lang('1 hour in advance') => '-60',
                        Ibos::lang('1 day in advance') => '-1440',
                        Ibos::lang('2 days in advance') => '-2880',
                    )
                ),
                array(
                    'tableName' => '{{crm_contract}}',
                    'fieldName' => 'expiretime',
                    'timeName' => '合同结束时',
                    'idName' => 'contractid',
                    'timeNode' => 'contract_end_time',
                    'diffeTime' =>array(
                        Ibos::lang('Not in advance') => '0',
                        Ibos::lang('Five minutes early') => '-5',
                        Ibos::lang('Ten minutes ahead of schedule') => '-10',
                        Ibos::lang('Half an hour early') => '-30',
                        Ibos::lang('1 hour in advance') => '-60',
                        Ibos::lang('1 day in advance') => '-1440',
                        Ibos::lang('2 days in advance') => '-2880',
                    )
                )
            )
        ),
        // 商机
        'opportunity' => array(
            'alarmType' => array(0),
            'eventName' => '商机',
            'eventUrl'  => 'crm/opportunity/detail',
            'eventIdKey'=>'oid',
            'timeNodes' => array(),
        ),
        // 客户
        'client' => array(
            'alarmType' => array(0),
            'eventName' => '客户',
            'eventUrl'  => 'crm/client/detail',
            'eventIdKey'=> 'cid',
            'timeNodes' => array(),
        ),
    ),
);