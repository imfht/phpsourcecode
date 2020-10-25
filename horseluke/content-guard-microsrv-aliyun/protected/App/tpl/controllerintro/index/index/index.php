<?php 
use SCH60\Kernel\StrHelper;
?>

<div class="container">
    <h3>作品介绍：基于阿里云安全“云盾魔方”开放平台的内容安全微服务</h3>
    <h6>作者：Horse Luke / 发布时间：2015-9-25 / 最后更新：2015-9-30</h6>
    <hr />

    <p>本demo是2015"云朵之上，编码未来"<a href="http://bbs.aliyun.com/read/256663.html?spm=5176.100131.1.6.urYu37" target="_blank">阿里云开源编程马拉松</a>的参赛作品。选题方向是面向传媒等的内容安全检测。</p>
    
    <p>本演示网站将于2016年03月30日到期。</p>
    
    <p>点击上面导航条即可开始演示过程。</p>
    
    <p>本Demo同时包括以下部分子课题和示例：</p>
    
    <p>&nbsp;</p>
    <p><strong>1.&nbsp;淘宝OAuth 2.0登录demo</strong></p>
    <p>主要演示如何集成“阿里巴巴SDK For PHP”到应用中，使其拥有“用淘宝帐号登录”（OAuth 2.0）的功能。</p>
    

    <p>&nbsp;</p>
    <p><strong>2.&nbsp;“阿里巴巴SDK For PHP”demo</strong></p>
    <p>主要演示如何集成“阿里巴巴SDK For PHP”并直接调用淘宝开放平台API，比如调用阿里云安全（云盾魔方）API。</p>
    
    
    <p>1和2的demo同时用于阐述以“应用-阿里云安全”直接通讯架构，对应用嵌入阿里云安全。</p>
    <p style="text-align: center;"><img alt="“应用-阿里云安全”直接通讯架构" style="max-width: 100%;" src="<?=StrHelper::urlStatic("static/introapp/img/csc-aliyun-ugc-app-direct-connect.png");?>" /></p>
    
    <p>&nbsp;</p>
    <p><strong>2.&nbsp;内容安全微服务demo</strong></p>
    <p>主要演示基于阿里云安全“云盾魔方”开放平台接口，以“阿里巴巴SDK For PHP”为基础，面向集团内网提供统一简单的内容安全检测和过滤相关的微服务接口。</p>

    <p>&nbsp;</p>
    <p><strong>3.&nbsp;内容安全微服务SDK与嵌入demo</strong></p>
    <p>主要演示应用接入微服务和使用方法。</p>
    <p>&nbsp;</p>
    <p>3和4的demo同时用于阐述以“应用-微服务-阿里云安全”微服务通讯架构，向内网应用持续有效地提供阿里云安全。</p>
    <p style="text-align: center;"><img alt="“应用-微服务-阿里云安全”微服务通讯架构" style="max-width: 100%;" src="<?=StrHelper::urlStatic("static/introapp/img/csc-aliyun-microsrv-ugc-app-microsrv-connect.png");?>" /></p>

    <p>&nbsp;</p>
    <p>相关代码仓库：</p>
    <p>“阿里巴巴SDK For PHP”：<a href="http://git.oschina.net/horseluke/AlibabaSDK">http://git.oschina.net/horseluke/AlibabaSDK</a></p>
    <p>内容安全微服务、微服务SDK及演示demo：<a href="http://git.oschina.net/horseluke/content-guard-microsrv-aliyun">http://git.oschina.net/horseluke/content-guard-microsrv-aliyun</a></p>
    
    
</div>