<{include file="public/header.tpl"}>
<body>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>添加管理员</h3>
<form  method="post" action="<{$smarty.const.__CONTROLLER__}>/insert">

  <div class="form-group">
    <label for="InputVName">管理员名称</label>
    <input type="text" class="form-control" id="InputVName" name="name" value="<{$data.name}>">
  </div>

  <div class="form-group">
    <label for="InputVName">管理员密码</label>
    <input type="password" class="form-control" id="InputPassword" name="password" >
  </div>

   <div class="form-group">
    <label for="InputVName">确认密码</label>
    <input type="password" class="form-control" id="InputRPword" name="repassword" >
  </div>

  <button type="submit" class="btn btn-default">Submit</button>
</form>
</div>
</div>
</body>
<{include file="public/footer.tpl"}>