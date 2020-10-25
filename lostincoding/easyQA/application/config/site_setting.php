<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 网站设置
 */

//公司信息
$config['company_info'] = array(
    'short_name' => '香果科技',
    'full_name' => '深圳市香果科技有限公司',
    'domain' => '52xiangguo.com',
    'home_url' => 'http://52xiangguo.com/',
);

//网站信息
$config['site_info'] = array(
    'version' => '0.1.2.20170108_base',
    'domain' => 'easyqa.com',
    'home_url' => 'http://easyqa.com/',
    'id' => 'easyqa',
    'name' => 'easyQA',
    'brief' => 'IT交流学习社区',
    'meta' => array(
        'description' => '这是一个供IT人交流学习的社区，可以问答，可以讨论，也有最新的IT行业资讯。',
        'keywords' => 'IT问答网站 IT技术 IT资讯',
    ),
    'icp' => array(
        'num' => '备案号',
        'link' => 'http://www.miitbeian.gov.cn/',
    ),
    'tongji' => '<span style="display: none;">统计代码</span>',
);

//一些文件地址,方便统一修改引用（不需要改动）
$config['files'] = array(
    'web' => array(
        'common.js' => '/static/js/common.min.js',
        'jquery.js' => '/static/lib/jquery/jquery-3.1.0.min.js',
        'nprogress.css' => '/static/lib/nprogress/nprogress.min.css',
        'plupload.js' => '/static/lib/plupload/plupload.full.min.js',
        'qiniu.js' => '/static/lib/qiniu/qiniu.min.js',
        'file_progress.js' => '/static/lib/qiniu/file_progress.js',
        'nprogress.js' => '/static/lib/nprogress/nprogress.min.js',
        'pjax.js' => '/static/lib/pjax/pjax.min.js',
        'highlight.js' => '/static/lib/highlight/highlight.min.js',
        'highlight.css' => '/static/lib/highlight/monokai-sublime.min.css',

        'admin_common.css' => '/static/admin/css/common.min.css',
        'admin_common.js' => '/static/admin/js/common.min.js',
        'amazeui.css' => '/static/lib/amazeui/css/amazeui.min.css',
        'amazeui.js' => '/static/lib/amazeui/js/amazeui.min.js',
        'layer.js' => '/static/lib/layer/layer.min.js',
        'zeroclipboard.js' => '/static/lib/zeroclipboard/ZeroClipboard.min.js',
    ),
);

//登录设置
$config['remember_me_time'] = 365 * 24 * 60 * 60;

//枚举显示（不需要改动）
$config['enum_show'] = array(
    //性别
    'gender' => array(
        'n' => '保密',
        'f' => '女',
        'm' => '男',
    ),
    //性别(第三人称)
    'gender3' => array(
        'n' => 'Ta',
        'f' => '她',
        'm' => '他',
    ),
    //文章类型
    'article_type' => array(
        1 => 'q',
        2 => 'discuss',
        3 => 'news',
    ),
    //文章类型
    'article_type_text' => array(
        1 => '问答',
        2 => '讨论',
        3 => '头条',
    ),
);

//邮箱设置
$config['email'] = array(
    'CharSet' => 'utf-8',
    'SMTPDebug' => 0,
    //如果是qq邮箱，只支持加密发送邮件，所以请填写'tls'，其它不需加密发送邮件的请留空即可
    'SMTPSecure' => '',
    'Host' => 'SMTP地址，如smtp.qq.com',
    //端口，端口一般为25，SMTPSecure为tls时端口一般为587，具体是多少请咨询SMTP供应商
    'Port' => 25,
    //注意！Username和From中的发件人来源地址要保持一致
    'Username' => '邮箱账号，如123456@qq.com',
    'Password' => '邮箱密码，如果是QQ邮箱，现在已使用了授权码，请到QQ邮箱账号设置里生成授权码',
    'From' => array('发件人来源地址，如123456@qq.com', '发件人来源名称，如no-reply'),
);

//加密使用的key，一定要32位，不能多也不能少，主要用来生成邮件验证或者找回密码时的url加密参数
$config['encrypt_key'] = 'qweqweqweqweqweqweqweqweqweqweqw';

//md5加密使用的salt，最好是32位，主要用来存储加密的密码
$config['salt'] = 'asdasdasdasdasdasdasdasdasdasdas';

//账号相关
$config['account'] = array(
    //邮箱验证链接超时时间(单位小时)
    'email_link_timeout' => 24,
);

//Github登录配置
$config['github'] = array(
    'client_id' => '',
    'client_secret' => '',
    'redirect_uri' => 'http://' . $config['site_info']['domain'] . '/account/github_callback',
);

//微信登录配置
$config['weixin'] = array(
    'appid' => '',
    'secret' => '',
    'redirect_uri' => 'http://' . $config['site_info']['domain'] . '/account/weixin_callback',
);

//QQ登录配置
$config['qc'] = array(
    'appid' => '',
    'appkey' => '',
    'callback' => 'http://' . $config['site_info']['domain'] . '/account/qc_callback',
);

//微博登录配置
$config['weibo'] = array(
    'WB_AKEY' => '',
    'WB_SKEY' => '',
    'WB_CALLBACK_URL' => 'http://' . $config['site_info']['domain'] . '/account/weibo_callback',
);

//oschina登录配置
$config['oschina'] = array(
    'client_id' => '',
    'client_secret' => '',
    'redirect_uri' => 'http://' . $config['site_info']['domain'] . '/account/oschina_callback',
);

//Geetest配置，账号申请地址 http://www.geetest.com/
$config['geetest'] = array(
    'open' => false,
    'CAPTCHA_ID' => '',
    'PRIVATE_KEY' => '',
    'MOBILE_CAPTCHA_ID' => '',
    'MOBILE_PRIVATE_KEY' => '',
);

//七牛配置，账号申请地址 https://portal.qiniu.com/signup?code=3ln7n97luobiq
$config['qiniu'] = array(
    'accessKey' => '',
    'secretKey' => '',
    //公有空间,bucket名称
    'static_bucket_name' => '',
    //bucket对应的域名
    'static_bucket_domain' => '',
    //文件分隔符,用来模拟文件目录（不需要改动）
    'delimiter' => '/',
);

//头像根链接（不需要改动）
$config['avatar_base_url'] = 'http://' . $config['qiniu']['static_bucket_domain'] . '/avatar/';

//话题导航
$config['topic_navs'] = array(
    '技术',
    '创意',
    '好玩',
    '招聘',
    '交易',
    '创业',
    'js',
    'php',
    'python',
    'java',
    'mysql',
    'ios',
    'android',
    'node.js',
    'html5',
    'linux',
    'c++',
    'css3',
    'git',
    'golang',
    'ruby',
    'vim',
    'docker',
);

//友情链接
$config['friendslink_lists'] = array(
    array('LostInCoding', 'http://lostincoding.com/'),
);

//签到配置
$config['sign'] = array(
    //首次签到积分
    'first_sign_points' => 100,
    //签到积分
    'points' => 10,
    //连续签到时，每天增加的积分
    'increment' => 5,
    //签到积分的最大值
    'max_points' => 100,
);
