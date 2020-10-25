<?php include($this->view_path('common/header',1));?>
<div><?php echo $ver;?></div>
<h2>欢迎使用MicroPHP框架。I'm in hmvc.</h2>
<hr style="border-bottom-color:black;border-width: 0 0 2px 0;"/>
<p>控制器位于:application/modules/hmvc_demo/controllers/welcome.php</p>
<p>视图位于:application/modules/hmvc_demo/views/welcome.view.php</p>
<p>你可以通过修改application/modules/hmvc_demo/hmvc.php里面的配置改变默认控制器</p>
<p>你可以通过修改index.php里面的$system['hmvc_modules']配置，注册HMVC模块</p>
<?php include($this->view_path('common/footer',1));?>