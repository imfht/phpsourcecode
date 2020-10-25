<?php
if(!defined('SAE_TMP_PATH')){exit('Sorry, the EFZer Hole program requires some services provided by the Sina App Engine(SAE) such as the NoSQL database hosting service named KVDB and Channel service used for message pushing. Therefore, this program needs to be operated on SAE platform.<br>Gain more information about SAE, please visit the official website http://sae.sina.com.cn/.<br>If you have any problem about how to deploy this application, you can feel free to contact with me via Email. My mailbox is zhouyinan@outlook.com, I\'m glad to offer assistance.');}
//前端显示设置
define('SITE_NAME','树洞'); //显示在<title></title>中的站名
define('SITE_NAME_DISPLAY','树洞'); //显示在页面顶端的站名
define('COPYRIGHT_HOLDER','Example Company'); //显示在前台页面底部的版权所有一栏，也请尊重个人的劳动，部分后台功能底部的版权请不要修改
define('INDEX_MESSAGES_AMOUNTS',50); //首页加载时最多预加载多少条历史树洞（请勿将数值调整得过大以免大幅增加KVDB花费）

//首页相关文案
define('PAGE_TITLE','树洞'); //在页面巨幕上的标题
define('PAGE_DESCRIBTION','<p>一个匿名吐槽的地方</p><p>发送“sd#你要说的话”到微信公众号XXXX，你的话就会出现在这里</p>'); //支持HTML文本，显示在巨幕标题下方

//Channel配置
define('MESSAGE_PUSH',true); //是否通过Channel实时推送信息
define('CHANNEL_NAME','EFZerHole'); //通信Channel的名称

//数据存储相关配置（一旦服务开始运行有数据产生后请不要修改）
define('MESSAGE_STORE_ENABLED',true); //是否将消息存储在KVDB中（如果设为false则不存储，只能做实时推送）
define('MESSAGE_PREFIX','Message-'); //消息存储在KVDB时的前缀

//杂项配置
define('COUNTER_NAME','MessageAmount'); //计数的Counter名称，与您在SAE后台的创建的Counter名称一致
define('MESSAGE_DELIMITER','%:'); //分割发送用户、时间、消息内容在平文本存储过程中的分隔符

//人人相关配置
define('RENREN_PUBLISH_ENABLED',false); //是否开启推送至人人公共主页的功能
define('RENREN_PAGE_ID',''); //人人公共主页ID（如果为空则视为推送到个人新鲜事而非公共主页新鲜事）
define('RENREN_APIKEY',''); //从人人开发者平台获得的APIKEY
define('RENREN_APPSECRET',''); //从人人开发者平台获得的APPSECRET
define('RENREN_ACCESS_TOKEN_AUTOREFRESH',true); //是否自动通过刷新TOKEN刷新AccessToken以替换即将过期的AccessToken
define('RENREN_TOPIC',false); //是否需要更新的人人状态前添加话题，也就是在消息前添加#前缀+树洞ID#如#华小二树洞88#，如果需要请将其修改false为话题前缀如'华小二树洞'（包括引号）。

//远程提交请求设置
define('REMOTE_ENABLED',true); //是否接受远程提交的请求（通过GET以及POST方式提交请求至remote.php）
define('REMOTE_SIGNATURE_REQUIRED',false); //设置为true后远程提交的GET或POST请求需要附带一个字段sig，不符合要求的请求将被拒绝。远程提交请求的sig字段值为message字段与下面定义的KEY相连接后进行md5运算
define('REMOTE_SIGNATURE_KEY',''); //如果启用远程请求签名验证，请设置该Key
define('REMOTE_SOURCE_CUSTOM_DISPLAY_ENABLED',true); //是否允许远端提交自定义的来源信息（来源信息使用source字段）
define('REMOTE_SOURCE_DEFAULT_DISPLAY','匿名用户'); //设置默认的来源信息（在远端未提供source字段或不允许远端自定义来源信息时使用）

//关于管理员的配置主要通过config.yaml中对于admin的访问目录配置来实现