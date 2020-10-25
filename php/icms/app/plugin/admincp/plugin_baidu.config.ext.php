<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
defined('iPHP') OR exit('What are you doing?');
?>
<h3 class="title">百度站长平台 主动推送(实时)</h3>
<span class="help-inline">申请地址:http://zhanzhang.baidu.com/ (需要权限)</span>
<div class="clearfloat"></div>
<div class="input-prepend"> <span class="add-on">站点</span>
  <input type="text" name="config[plugin][baidu][sitemap][site]" class="span3" id="plugin_baidu_sitemap_site" value="<?php echo $config['plugin']['baidu']['sitemap']['site'] ; ?>"/>
</div>
<span class="help-inline">在站长平台验证的站点，比如www.example.com</span>
<div class="clearfloat mt10"></div>
<div class="input-prepend"> <span class="add-on">准入密钥</span>
  <input type="text" name="config[plugin][baidu][sitemap][access_token]" class="span3" id="plugin_baidu_sitemap_access_token" value="<?php echo $config['plugin']['baidu']['sitemap']['access_token'] ; ?>"/>
</div>
<span class="help-inline">在站长平台申请的推送用的准入密钥</span>
<div class="clearfloat mb10"></div>
<div class="input-prepend"> <span class="add-on">同步推送</span>
  <div class="switch" data-on-label="启用" data-off-label="关闭">
    <input type="checkbox" data-type="switch" name="config[plugin][baidu][sitemap][sync]" id="plugin_baidu_sitemap_sync" <?php echo $config['plugin']['baidu']['sitemap']['sync']?'checked':''; ?>/>
  </div>
</div>
<span class="help-inline">启用文章发布时同步推送 如果发布文章无法正常返回 建议关闭</span>
<div class="clearfloat mb10"></div>
<h3 class="title">熊掌号天级收录 API提交</h3>
<div class="clearfloat"></div>
<div class="input-prepend"> <span class="add-on">appid</span>
  <input type="text" class="span3" name="config[plugin][baidu][xzh][appid]" id="plugin_baidu_xzh_appid" value="<?php echo $config['plugin']['baidu']['xzh']['appid'] ; ?>"/>
</div>
<span class="help-inline">您的唯一识别ID</span>
<div class="clearfloat mt10"></div>
<div class="input-prepend"> <span class="add-on">token</span>
  <input type="text" class="span3" name="config[plugin][baidu][xzh][token]" id="plugin_baidu_xzh_token"  value="<?php echo $config['plugin']['baidu']['xzh']['token'] ; ?>"/>
</div>
<span class="help-inline">在搜索资源平台申请的推送用的准入密钥</span>
