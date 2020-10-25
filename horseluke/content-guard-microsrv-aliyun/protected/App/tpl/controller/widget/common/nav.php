<?php
use SCH60\Kernel\App;
use SCH60\Kernel\KernelHelper;
use SCH60\Kernel\StrHelper;

use Common\AppCustomHelper;

$currentUrl = strtolower(App::$app->getRouter()['router']);

?>

<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
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
      <?php if(AppCustomHelper::isLogin()): ?>
      <ul class="nav navbar-nav">
        <li class="<?php if (stripos($currentUrl, 'index/index') !== false)echo StrHelper::O('active')?>"><a href="<?=StrHelper::url("index/index/index")?>">欢迎</a></li>
        <li class="dropdown <?php if (stripos($currentUrl, 'editor/') !== false)echo StrHelper::O('active')?>">
              <a class="dropdown-toggle" role="button" aria-expanded="false" aria-haspopup="true" href="javascript:void(0);" data-toggle="dropdown">编辑工作站 <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="<?=StrHelper::url("editor/content/check")?>">发表文章</a></li>
                <li class="divider" role="separator"></li>
                <li><a href="<?=StrHelper::url("editor/vote/list")?>">投票记录</a></li>
              </ul>
        </li>
        
        <li class="dropdown <?php if (stripos($currentUrl, 'microsrv/') !== false)echo StrHelper::O('active')?>">
              <a class="dropdown-toggle" role="button" aria-expanded="false" aria-haspopup="true" href="javascript:void(0);" data-toggle="dropdown">系统管理 <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="<?=StrHelper::url("microsrv/about/ping")?>">内容安全微服务</a></li>
                <li class="divider" role="separator"></li>
                <li><a href="<?=StrHelper::url("microsrv/ipquery/attackhistory")?>">IP攻击历史记录查询（微服务demo）</a></li>
              </ul>
        </li>
        
      </ul>
	  <?php endif; ?>
	  
      <?php if(!AppCustomHelper::isLogin()): ?>
	      <span class="navbar-right">
		      <a href="<?=StrHelper::url("user/login/index")?>"><button type="button" class="btn btn-default navbar-btn">登录</button></a>
		  </span>
	  <?php else: ?>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><strong><?=StrHelper::O(App::$app->request->session_get('username'))?></strong> <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="<?=StrHelper::url("user/login/logout")?>">退出</a></li>
          </ul>
        </li>
        
      </ul>
	  <?php endif; ?>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>