<?php 
/**
*	桌面首页项(关于官网)
*/
defined('HOST') or die ('not access');

?>
<div class="panel panel-success">
  <div class="panel-heading">
	<h3  class="panel-title">关于信呼</h3>
  </div>
  <div class="panel-body">
	
	<div style="line-height:25px">
	软件：信呼<br>
	官网：<a href="<?=URLY?>" target="_blank"><?=URLY?></a><br>
	版本：V<?=VERSION?><br>
	下载：服务端，PC客户端，APP<a href="<?=URLY?>view_down.html" target="_blank">[去下载]</a><br>
	声明：我们是开源的，请遵守我们的<a href="<?=URLY?>view_version.html" target="_blank">开源协议</a>，谢谢！<br>
	帮助：提供开发帮助使用文档<a href="<?=URLY?>help.html" target="_blank">[查看]，<a href="<?=URLY?>view_bidu.html" target="_blank">[必读]</a>
	</div>

  </div>
</div>