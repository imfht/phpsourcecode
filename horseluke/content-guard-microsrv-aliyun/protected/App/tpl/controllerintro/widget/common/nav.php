<?php
use SCH60\Kernel\App;
use SCH60\Kernel\KernelHelper;
use SCH60\Kernel\StrHelper;

$currentUrl = strtolower(App::$app->getRouter()['router']);

?>

<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">菜单</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand navbar-custom-brand" href="<?=StrHelper::url("index/index/index")?>"><?=StrHelper::O(KernelHelper::config('product_name'));?></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="<?php if (stripos($currentUrl, 'index/index') !== false)echo StrHelper::O('active')?>"><a href="<?=StrHelper::url("index/index/index")?>">欢迎</a></li>
        <li><a href="<?=StrHelper::urlStatic("cmsadmin.php")?>">CMS后台集成演示</a></li>
      </ul>
	  
      <span class="navbar-right">
	        <a href="javascript:void(0);"><button type="button" class="btn btn-default navbar-btn">DEMO</button></a>
	  </span>
	  
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>