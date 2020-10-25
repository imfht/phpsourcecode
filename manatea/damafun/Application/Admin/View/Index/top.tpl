<{include file="public/header.tpl"}>
<body>
<nav class="navbar navbar-default"  style="background:#e5e5e5;">
  <div class="container-fluid" >
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <a class="navbar-brand" href="#" style="color:#000;">后台管理界面</a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="#" style="color:#000;">您好，<{$smarty.session.user.name}>用户</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="<{$smarty.const.__MODULE__}>/login/logout" target="_top" style="color:#000;">登出</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<!-- <div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<h3 class="text-center">
				h3. 这是一套可视化布局系统.
			</h3>
			<ul class="nav nav-tabs">
				<li class="active">
					您好，<{$smarty.session.user.name}>用户
				</li>
<li>
					<a href="#">资料</a>
				</li>
				<li class="disabled">
					<a href="#">信息</a>
				</li>
				<li class=" pull-right">
					 <a href="<{$app}>/login/logout" target="_top">登出</a>
				</li>
			</ul>
		</div>
	</div>
</div> -->
</body>
<{include file="public/footer.tpl"}>