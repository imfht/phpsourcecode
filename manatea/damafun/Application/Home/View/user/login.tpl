<{include file="public/header.tpl"}>
<body>
<div class="container">

		<div class="row">
      <div class="col-md-7 reg-pic">
      </div>
			<div class="col-md-4" style=" border:1px solid #cccccc; margin-top:20px;">
<div class="page-header">
<h2>登陆 CzFun</h2>
</div>
<form class="form-horizontal" method="post" action="<{$smarty.const.__CONTROLLER__}>/loginCheck">				
  <div class="form-group" style="position:relative; left:20px;">
    <label for="inputEmail3" class="col-sm-2 control-label">账号</label>
    <div class="col-sm-7">
      <input type="text" class="form-control" id="inputEmail3" name="username" placeholder="请输入账号" required >
    </div>
  </div>
  <div class="form-group" style="position:relative; left:20px;">
    <label for="inputPassword3" class="col-sm-2 control-label">密码</label>
    <div class="col-sm-7">
      <input type="password" class="form-control" name="password" id="inputPassword3" placeholder="请输入密码" required >
    </div>
  </div>
  <div class="col-sm-offset-2 col-sm-10" style="position:relative; left:20px;">
  	<a href="<{$smarty.const.__MODULE__}>/user/register"><h6>>注册账号</h6></a>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10" style="position:relative; left:20px;"> 
      <button type="submit" class="btn btn-success" style="width:200px;"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span>登陆</button>
    </div>
  </div>
</form>
			</div>
		</div>
</body>
<{include file="public/footer.tpl"}>