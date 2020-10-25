<{include file="public/header.tpl"}>
<body  style="background:#f5f5f5;">

<div class="accordion" id="accordion2" >
  <div class="accordion-group">
    <div class="accordion-heading">
    <ul class="nav nav-tabs nav-stacked" data-toggle="collapse" data-parent="#accordion1" href="#collapseOne" style="cursor:pointer;"><h4>视频管理</h4></ul>
    </div>
    <div id="collapseOne" class="accordion-body collapse in">
      <div class="accordion-inner">
        <ul class="nav nav-pills nav-stacked nav-pills-stacked-example">
<!--       <li role="presentation"><a href="<{$smarty.const.__MODULE__}>/video/add" target="main">添加视频</a></li> -->
	      <li role="presentation"><a href="<{$smarty.const.__MODULE__}>/video/index" target="main">编辑视频</a></li>
	      </ul>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
    <ul class="nav nav-tabs nav-stacked" data-toggle="collapse" data-parent="#accordion1" href="#collapseTwo" style="cursor:pointer;"><h4>分类管理</h4></ul>
    </div>
    <div id="collapseTwo" class="accordion-body collapse">
      <div class="accordion-inner">
        <ul class="nav nav-pills nav-stacked nav-pills-stacked-example">
     
	      <li role="presentation"><a href="<{$smarty.const.__MODULE__}>/cat/add" target="main">添加分类</a></li>
	      <li role="presentation"><a href="<{$smarty.const.__MODULE__}>/cat/index" target="main">修改分类</a></li> 
	      </ul>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
    <ul class="nav nav-tabs nav-stacked" data-toggle="collapse" data-parent="#accordion1" href="#collapseThree" style="cursor:pointer;"><h4>用户管理</h4></ul>
    </div>
    <div id="collapseThree" class="accordion-body collapse">
      <div class="accordion-inner">
        <ul class="nav nav-pills nav-stacked nav-pills-stacked-example">
     <li role="presentation"><a href="<{$smarty.const.__MODULE__}>/user/index" target="main">用户权限</a></li> 
	      </ul>
      </div>
    </div>
  </div>
  <{if $smarty.session.user.allow==1}>
  <div class="accordion-group">
    <div class="accordion-heading">
    <ul class="nav nav-tabs nav-stacked" data-toggle="collapse" data-parent="#accordion1" href="#collapseFour" style="cursor:pointer;"><h4>管理员权限</h4></ul>
    </div>
    <div id="collapseFour" class="accordion-body collapse">
      <div class="accordion-inner">
        <ul class="nav nav-pills nav-stacked nav-pills-stacked-example">
     <li role="presentation"><a href="<{$smarty.const.__MODULE__}>/admin/index" target="main">管理员管理</a></li> 
     <li role="presentation"><a href="<{$smarty.const.__MODULE__}>/admin/add" target="main">添加管理员</a></li> 
        </ul>
      </div>
    </div>
  </div>
  <{/if}>
  </div>
</body>
<{include file="public/footer.tpl"}>