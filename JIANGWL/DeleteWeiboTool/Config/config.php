<?php
/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/9/12
 * Time: 20:49
 */
$config = array();

$config['debug'] = TRUE;
$config['del_weibo_api'] = 'http://m.weibo.cn/mblogDeal/delMyMblog';

/**************必填项目*******************/
$config['base_path'] = '/var/www/html/DelWeibo/';//代码根目录位置

//登陆后新浪的cookie，用chrome浏览器登陆微博后，打开F12开发者工具查看微博页面request请求中请求的cookie值，复制到该处
$config['sina_cookie'] = '_T_WM=d7193bc43fd5bfa00880f0763000b0c9; SCF=AuHRnn-UiRFN2SLuvt-YUI_wB4PePW0GLrj-HMqvAm2AEXCHxSN9LrdWMpRpV8xLzNiPuojOMMoUL5YmVEn4DNM.; SUBP=0033WrSXqPxfM725Ws9jqgMF55529P9D9WFjyNj8jSg1PXxFzv8CrJXN5JpX5o2p5NHD95QpSo2R1hz4Son0Ws4DqcjlC.LWK.UPx-LeKX4GxXUG; H5_INDEX=3; H5_INDEX_TITLE=JIANGWEILONGGGG; SUB=_2A25638dADeTxGedI6loZ8ifKwz-IHXVWI-kIrDV6PUJbkdBeLVndkW2FwuduZuJwCbQfmiBeWOtrHqVkjQ..; SUHB=0vJ50VDcDByI-L; SSOLoginState=1474017040; M_WEIBOCN_PARAMS=uicode%3D20000174';

//我的所有微博页面，抓取删除微博的url地址，访问手机端的页面
$config['self_page_url'] = 'http://m.weibo.cn/page/tpl?containerid=1005051618829683_-_WEIBO_SECOND_PROFILE_WEIBO';


/****************************************/

//PC页面的header
$config['sina_pc_header'] = array(
	'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.101 Safari/537.36',
	'Upgrade-Insecure-Requests:1',
	'Host:weibo.com',
	'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
	'Accept-Encoding:gzip, deflate, sdch',
	'Accept-Language:zh-CN,zh;q=0.8',
	'Cache-Control:max-age=0',
	'Connection:keep-alive');

$config['sina_phone_header'] = array(
	'Host:m.weibo.cn',
	'Referer:http://m.weibo.cn/',
	'Upgrade-Insecure-Requests:1',
	'User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
	'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
	'Accept-Encoding:gzip, deflate, sdch',
	'Accept-Language:zh-CN,zh;q=0.8',
	'Cache-Control:max-age=0',
	'Connection:keep-alive',
);
$config['sina_phone_delete_header'] = array(
	'Referer:' . $config['self_page_url'],
	'Host:m.weibo.cn',
	'Origin:http://m.weibo.cn',
	'User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
	'X-Requested-With:XMLHttpRequest',
	'Accept:application/json, text/javascript, */*; q=0.01',
	'Accept-Encoding:gzip, deflate',
	'Accept-Language:zh-CN,zh;q=0.8',
	'Connection:keep-alive',
	//注意这里content-tpye改成multipart/form-data，在这里卡了不少时间
	'Content-Type:multipart/form-data; charset=UTF-8',
);

//自动加载的类，component加载到loader中，
$config['auto_load'] = array(
	'session',//不要删除
	'http',//不要删除
	'config',//不要删除
	'curl'//不要删除
);