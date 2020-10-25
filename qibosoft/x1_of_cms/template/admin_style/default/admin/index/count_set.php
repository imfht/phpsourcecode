<?php
defined('APP_PATH') || die('err!');

$array = [
	'attachment'=>'附件数',
	'bbs_reply'=>'论坛贴子回复数',
	'comment_content'=>'评论数',
	'fav'=>'收藏夹',
	'moneylog'=>'积分变动日志',
	'msg'=>'站内消息数',
	'msgtask_log'=>'群发消息日志',
	'rmb_consume'=>'RMB变动日志',
	'rmb_getout'=>'RMB提现日志',
	'rmb_infull'=>'RMB充值日志',
	'safe365_logs'=>'安全拦截日志',
	'signin_member'=>'会员签到日志',
	'memberdata'=>'用户数',
	'alilive_log'=>'直播日志',
	'alilive_order'=>'购买直播订单数',
];

$modules = modules_config();
foreach ($modules AS $rs){
    if (!is_file(APP_PATH.$rs['keywords'].'/model/Field.php')||!is_file(APP_PATH.$rs['keywords'].'/model/Module.php')||!is_file(APP_PATH.$rs['keywords'].'/model/Content.php')) {
        continue;
    }
    $ms = model_config(null,$rs['keywords']);
    $array[$rs['keywords'].'_content'] = $rs['name'].(count($ms)>1?' 所有':'');
    if (count($ms)>1) {
        foreach ($ms AS $ts){
            $array[$rs['keywords'].'_content'.$ts['id']] = $ts['title'];
        }
    }
    if ( is_file(APP_PATH.$rs['keywords'].'/model/Order.php') ) {
        $array[$rs['keywords'].'_order'] = $rs['name'].' 订单';
    }elseif ( is_file(APP_PATH.$rs['keywords'].'/model/Reply.php') ) {
        $array[$rs['keywords'].'_reply'] = $rs['name'].' 回复';
    }
}

$times = [
	'0'=>'所有',
	'day'=>'今天',
	'day2'=>'昨天',
	'day3'=>'前天',
	'week'=>'本周',
	'week2'=>'上周',
	'month'=>'本月',
	'month2'=>'上月',
    'year'=>'今年',
    'year2'=>'去年',
    'quarter'=>'本季度',
    'quarter2'=>'上一季度',
];
return [
	'form'=>[
		['icon','icon','图标'],
	    ['color','bgcolor','背景颜色'],
		['text','title','描述'],
		['text','url','链接地址'],
		//['radio','types','数据来源','',['默认(非自定义)','自定义'],0],
		['select','table_name2','统计哪种数据','',$array],
		['text','table_name','数据来源','若手工添加数据表,不要加前缀'],
		['text','where','附加查询条件','比如:“status=0”代表未审,“fid=5&status=0”代表ID为5的栏目并且未审的筛选'],
	    ['checkbox','showtime','时间范围','可多选,但页面未必挤得下',$times,'0'],
	    ['radio','timefield_type','时间字段','',['默认(自动识别)','自定义(无法识别才定义)'],'0'],
	    ['text','time_field','时间字段名','一般是create_time或posttime或update_time,用户注册是regdate,用户最近登录是lastvist'],
		['radio','count_type','统计类型','',['记录条数','指定字段累计求和'],'0'],
	    ['text','sum_field','求和字段名','一般是money或rmb'],
	],
	'trigger'=>[
		//['types','0','table_name'],
		//['types','1','table_name2'],
	    ['timefield_type','1','time_field'],
		['count_type','1','sum_field'],
	],
    'template'=>'admin_style/default/admin/index/count_set',
    'page_title'=>'自定义统计数据',
    'help_msg'=>'1、内容审核与未审的字段一般是“status=0”代表未审,“status>0”代表已审<br>
                 2、订单字段一般是“pay_status=0”代表未付款,“pay_status=1”代表已付款<br>
                 3、需要了解更多查询方法,请上论坛求助',
];










