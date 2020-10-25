<?php
return array(
    'URL_ROUTER_ON'		=> true,   // 开启URL路由
    'URL_ROUTE_RULES'	=> array(  //路由规则定义
        /* 问卷资源服务 */
    	'questionnaires/:questionnaireID$'				=>	array('WebService/Questionnaires/meta', '', array('ext'=>'json','method'=>'get')),
    	'questionnaires/:questionnaireID/questions$'	=>	array('WebService/Questionnaires/getQuestions', '', array('ext'=>'json','method'=>'get')),
    	'questionnaires/:questionnaireID/reply$'		=>	array('WebService/Questionnaires/submitReply', '', array('ext'=>'json','method'=>'post')),
    	'questionnaires/:questionnaireID/judgement$'	=>	array('WebService/Questionnaires/getJudgement', '', array('ext'=>'json','method'=>'get')),
    	'questionnaires/:questionnaireID/signimages$'	=>	array('WebService/Questionnaires/getSignimages', '', array('ext'=>'json','method'=>'get')),

        /* 后台资源服务 */
        'subscribers$'                                  =>  array('WebService/Admin/listSubscribers', '', array('ext'=>'json','method'=>'get')),
        'questionnaires$'                               =>  array('WebService/Admin/listQuestionnaires', '', array('ext'=>'json','method'=>'get')),
    )
);