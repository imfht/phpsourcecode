<?php

$lcinfo = file_get_contents(LESSCREATOR_DIR ."/lcproject.json");
$lcinfo = json_decode($lcinfo, true);

?>
<style>
.opwpv6 li {
    margin: 2px;
}
</style>
<span class="badge badge-success pull-right hide">当前版本: <?php echo $lcinfo['version']?></span>

<div class="opwpv6">

<strong>编辑器</strong>
<ul>
    <li>支持更多第3方开发框架，长期任务</li>
    <li>代码补全: 覆盖主流编程语言</li>
    <li>版本控制: git, svn 功能细化</li>
    <li>远程结对编程: 多人查看、编辑同一代码</li>
</ul>

<strong>插件、应用</strong>
<ul>
    <li>协作: 在线IM工具，基于 WebRTC 的视频聊天、会议</li>
    <li>效率: 个人任务管理; 轻量级项目进度管理</li>
    <li>管理: 知识库，文档管理(doc,ppt,pdf). 支持全文检索</li>
    <li>管理: 在线产品 UI 原型设计; 在线图表设计 <a href="http://www.lucidchart.com" target="_blank">www.lucidchart.com</a></li> 
</ul>


<strong>系统服务</strong>
<ul>
    <li>沙盒、容器: 基于 LXC 轻量级运行环境</li>
    <li>运行环境与主流框架集成: golang, node.js, java, python</li>
    <li>集群管理、部署、监控</li>
</ul>
</div>

<script>

lessModalButtonAdd("t0g7a8", "<?php echo $this->T('Close')?>", "lessModalClose()", "btn-inverse");


</script>
