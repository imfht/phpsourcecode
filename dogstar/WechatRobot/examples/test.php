<?php
//假装微信请求

echo "模拟发送一条文本消息，内容为：\n一个人\n\n";

$GLOBALS['HTTP_RAW_POST_DATA'] = '<xml><ToUserName><![CDATA[gh_43235ff1360f]]></ToUserName>
<FromUserName><![CDATA[oWNXvjipYqRViMpO8GZwXxE43pUY]]></FromUserName>
<CreateTime>1419757723</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[一个人]]></Content>
<MsgId>6097812988731466682</MsgId>
</xml>';

echo "返回给微信的报文是：\n";

require_once dirname(__FILE__) . '/index.php';

echo "\n\n";
