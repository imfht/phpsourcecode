<{include file="public/header.tpl"}>
<body>
<div class="container">

		<div class="row">
			<div class="col-md-7 reg-pic">
			</div>
			<div class="col-md-4" style=" border:1px solid #cccccc; margin-top:20px;">
<div class="page-header">
<h2>注册 CzFun</h2>
</div>
<form class="form-horizontal" method="post" action="<{$smarty.const.__CONTROLLER__}>/registerAction">				

  <div class="form-group" style="position:relative; left:-15px;">
    <label for="inputPassword3" class="col-sm-3 control-label">用户名</label>
    <div class="col-sm-7">
      <input type="text" name="name" class="form-control" id="inputPassword3" placeholder="请输入用户名" required >
    </div>
  </div>
  <div class="form-group" style="position:relative; left:20px;">
    <label for="inputEmail3" class="col-sm-2 control-label">密码</label>
    <div class="col-sm-7">
      <input type="password" name="password" class="form-control" id="inputEmail3" placeholder="请输入密码" required >
    </div>
  </div>
  <div class="form-group" style="position:relative; left:-15px;">
    <label for="inputEmail3" class="col-sm-3 control-label">确认密码</label>
    <div class="col-sm-7">
      <input type="password" name="repassword" class="form-control" id="inputEmail3" placeholder="请输入确认密码" required >
    </div>
  </div>
  <div class="form-group" style="position:relative; left:20px;">
    <label for="inputEmail3" class="col-sm-2 control-label">邮箱</label>
    <div class="col-sm-7">
      <input type="email" name="email" class="form-control" id="inputEmail3" >
    </div>
  </div>
  <div class="form-group" style="position:relative; left:-15px;">
    <label for="inputEmail3" class="col-sm-3 control-label">性别</label>
    <div class="col-sm-7">
       <div class="radio">
        <label>
          <input type="radio" name="sex" id="optionsRadios1" value="0" checked>
         男
        </label>
        <label>
          <input type="radio" name="sex" id="optionsRadios2" value="1">
          女
        </label>
      </div>
    </div>
  </div>
  <div class="form-group" style="position:relative; left:-15px;">
    <label for="inputEmail3" class="col-sm-3 control-label">联系方式</label>
    <div class="col-sm-7">
      <input type="tel" name="tel" class="form-control" id="inputtel" pattern="[0-9]{11}" title="11位验证号码">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10" style="position:relative; left:20px;"> 
      <button type="submit" class="btn btn-primary" ><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span>注册</button>
      <button type="submit" class="btn btn-default" >返回登陆</button>
    </div>
  </div>
</form>
			</div>
		</div>
</body>
<{include file="public/footer.tpl"}>