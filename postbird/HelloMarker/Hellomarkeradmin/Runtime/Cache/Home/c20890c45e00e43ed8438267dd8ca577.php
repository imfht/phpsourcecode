<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>用户登录</title>
    <link rel="stylesheet" href="/hellomarkertest/Public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/hellomarkertest/Public/css/font-awesome.min.css">
    <script src="/hellomarkertest/Public/js/jquery-1.10.2.js"></script>
    <script src="/hellomarkertest/Public/js/bootstrap.min.js"></script>
</head>
<body style="background:url(/hellomarkertest/hellomarkeradmin/Home/View/Public/images/indexbanner.jpg);background-repeat: none;">
<div class="container">
     <div class="row">
         <div class="col-md-12">
             <div class="jumbotron text-center"style="margin:0 auto;margin-top:15%;width:70%;background-color:#F4F8FA;">
                  <h1><small>用户登录</small></h1>
                  <form action="/hellomarkertest/hellomarkeradmin.php/Home/Admin/login/" method="post" accept-charset="utf-8" class="text-left">
                      <div class="form-group">
                          <label>用户名：</label>
                          <input type="text" name="adminname" value="" class="form-control" style="width:50%">
                      </div>
                     <div class="form-group">
                          <label>密码：</label>
                          <input type="password" name="adminpassword" value="" class="form-control" style="width:50%">
                      </div>
                      <div class="form-group">
                          <button type="submit" class="btn btn-success ben-lg">登录</button>
                      </div>
                  </form>
                      <?php if($backFlag == 1): ?><div class="alert alert-danger alert-dismissible" role="alert">
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                           <strong>Error!</strong><?php echo ($backInfo); ?>
                           </div>
                      <?php else: endif; ?>
             </div>
         </div>
     </div>
</div>
    
</body>
</html>