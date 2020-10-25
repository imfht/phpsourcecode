<?php include($this->view_path('common/header'));?>
<div><?php echo $ver;?></div>
<h2>欢迎使用MicroPHP框架。</h2>
<hr style="border-bottom-color:black;border-width: 0 0 2px 0;"/>
<p>控制器位于:application/controllers/welcome.php</p>
<p>视图位于:application/views/welcome.view.php</p>
<p>你可以通过修改index.php里面的配置改变默认控制器</p>
<?php include($this->view_path('common/footer'));?>