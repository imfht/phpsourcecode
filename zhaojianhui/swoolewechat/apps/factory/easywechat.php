<?php
/**
 * EasyWeChat微信SDK开发组件
 */
global $php;

$config = $php->config['wechat'][$php->factory_key];
//初始化强大的微信SDK开发工具微信EasyWeChat
if (empty($this->config['wechat'][$php->factory_key]))
{
    throw new Swoole\Exception\Factory("wechat->".\Swoole::$php->factory_key." is not found.");
}
$wechatConfig = $this->config['wechat'][$php->factory_key];

$app   = new \EasyWeChat\Foundation\Application($wechatConfig);
$cache = new App\Component\EasywechatCache();
$app->access_token->setCache($cache);
$app->access_token->setPrefix('easywechat_accessToken');

return $app;
