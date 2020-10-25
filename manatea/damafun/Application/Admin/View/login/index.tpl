<{include file="public/header.tpl"}>
<body style="background:#555555; ">
<script type="text/javascript">
	$(function(){
		$('#book').hide();
		$('#book').fadeIn('normal');
	});
</script>
<div class="container"  id="#login">
    <div class="row" style="margin-top:100px">   
    <!-- <div class="col-md-6">
      <img src="<{$res}>/images/loginpic.jpg" style="width:400px;">
      </div>  -->
       <div class="col-md-4 col-md-offset-4">      
      <div class="panel panel-primary" id="book">
  <div class="panel-heading">请登录我的装逼系统</div>
  <div class="panel-body">
     <form class="form-horizontal" action="<{$smarty.const.__CONTROLLER__}>/login" method="post">
        <label class="control-label" for="inputName"></label>
			<div class="controls">
			<div class="input-group">
			 <span class="input-group-addon" id="basic-addon1">
			 <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
			 </span>
				<input id="inputName" name="username" type="text" class="form-control" placeholder="请输入用户名" required autofocus/>
				</div>
			</div>
			<label class="control-label" for="inputPassword"></label>
					<div class="controls">
					<div class="input-group">
			 <span class="input-group-addon" id="basic-addon1">
			 	<span class="glyphicon glyphicon-circle-arrow-right" aria-hidden="true"></span>
			 </span>
						<input id="inputPassword" name="password" type="password" class="form-control"placeholder="请输入密码" required/>
						</div>
					</div>        
        <div class="controls">
         <label>
        <input type="checkbox" value="remember-me"> Remember me
         </label>
			<button type="submit" class="btn  btn-primary btn-block">有本事你点我啊</button>
		</div>  
		</form>     
      </div>
      </div>
</div>
</div>
      </div>
    </div> 
<!-- <div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<form class="form-horizontal" action="<{$url}>/login" method="post">
				<div class="control-group">
					 <label class="control-label" for="inputName">用户名</label>
					<div class="controls">
						<input id="inputName" name="username" type="text" />
					</div>
				</div>
				<div class="control-group">
					 <label class="control-label" for="inputPassword">密码</label>
					<div class="controls">
						<input id="inputPassword" name="password" type="password" />
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						 <label class="checkbox"><input type="checkbox" /> Remember me</label> <button type="submit" class="btn">登陆</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div> -->
</body>
<{include file="public/footer.tpl"}>