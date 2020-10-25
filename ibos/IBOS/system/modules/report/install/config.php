<?php

defined('IN_MODULE_ACTION') or die('Access Denied');
return array(
    'param' => array(
        'name' => '工作汇报',
        'category' => '工作汇报',
        'description' => '提供企业工作汇报',
        'author' => 'banyanCheung @ IBOS Team Inc',
        'version' => '1.0',
        'pushMovement' => 1,
        'indexShow' => array(
            'widget' => array(
                'report/report'
            ),
            'link' => 'report/default/index'
        )
    ),
    'config' => array(
        'modules' => array(
            'report' => array('class' => 'application\modules\report\ReportModule')
        ),
        'components' => array(
            'messages' => array(
                'extensionPaths' => array(
                    'report' => 'application.modules.report.language',
                )
            )
        ),
    ),
    'authorization' => array(
        'report' => array(
            'type' => 'node',
            'name' => '个人汇报',
            'group' => '工作汇报',
            'controllerMap' => array(
                'default' => array('index', 'add', 'edit', 'del', 'show'),
                'type' => array('add', 'edit', 'del'),
                'comment' => array('getcommentlist', 'addcomment', 'delcomment'),
                'api' => array('addcomment', 'allread', 'delcomment', 'delreport', 'formreport', 'getcommentlist',
                               'getlist', 'getreader', 'savereport', 'showreport', 'usertemplate',
                              'shoplist', 'getcount', 'getcommentview','getreviewcomment', 'getauthority', 'getcharge', 'addtemplate'),
            )
        ),
        'managertemplate' => array(
            'type' => 'node',
            'name' => '管理模板',
            'group' => '工作汇报',
            'controllerMap' => array(
                'api' => array('savetemplate', 'formtemplate', 'settemplate',
                    'deltemplte', 'sorttemplate', 'usertemplate', 'shoplist', 'managertemplate', 'getpicture'),
            )
        ),
        'settemplate' => array(
            'type' => 'node',
            'name' => '设置模板',
            'group' => '工作汇报',
            'controllerMap' => array(
                'api' => array('settemplate', 'managertemplate', 'usertemplate', 'shoplist'),
            )
        ),
        'review' => array(
            'type' => 'node',
            'name' => '评阅下属汇报',
            'group' => '工作汇报',
            'controllerMap' => array(
                'review' => array('index', 'personal', 'add', 'edit', 'del', 'show'),
                'api' => array('allread', 'formreport', 'getreader', 'savereport', 'showreport', 'usertemplate',
                              'getstamp', 'setstamp', 'shoplist', 'getcount', 'getcommentview', 'getreviewcomment', 'getauthority'),
            )
        ),
//        'statistics' => array(
//            'type' => 'node',
//            'name' => '查看统计',
//            'group' => '工作总结与计划',
//            'controllerMap' => array(
//                'stats' => array('personal', 'review')
//            )
//        )
    ),
    'statistics' => array(
        'sidebar' => 'application\modules\report\widgets\StatReportSidebar',
        'header' => 'application\modules\report\widgets\StatReportHeader',
        'summary' => 'application\modules\report\widgets\StatReportSummary',
        'count' => 'application\modules\report\widgets\StatReportCount'
    )
);
