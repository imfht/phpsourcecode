<?php /*a:1:{s:52:"E:\kyweixin\EasyAdmin\cqkyicms\admin\view\login.html";i:1525664613;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <!--<link rel="shortcut icon" href="assets/images/favicon_1.ico">-->

    <title>柯一CMS通用后台-PHP版</title>

    <link href="/static/admins/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/core.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/icons.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/components.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/pages.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/menu.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/responsive.css" rel="stylesheet" type="text/css">

    <script src="/static/admins/js/modernizr.min.js"></script>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->


</head>
<body>


<div class="wrapper-page">
    <div class="panel panel-color panel-primary panel-pages">
        <div class="panel-heading ">
            <img src="/static/admins/img/logo.png"/>
            <span>柯一网络CMS通用后台系统</span>
            <p class="badge badge-danger">PHP版</p>
            <p class="welcomes">亲爱的用户，欢迎您使用本系统!</p>
        </div>


        <div class="panel-body">
            <form class="form-horizontal m-t-20" id="loginSubmit" method="post" >

                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control input-lg" name="username" id="username" type="text" required="" placeholder="管理用户名：">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control input-lg" name="password" id="password" type="password" required="" placeholder="管理密码：">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control input-lg" name="code" id="code" type="text" required="" placeholder="验证码：">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                    <img src="<?php echo captcha_src(); ?>" alt="captcha" id="captcha" onClick="this.src='<?php echo captcha_src(); ?>?'+Math.random();" style="width: 100%; height: 50px"  />

                    </div>
                    </div>
                <div class="form-group text-center m-t-40" >
                    <div class="col-xs-12" >
                        <button style="font-size: 12px;" class="btn btn-block btn-lg btn-primary waves-effect waves-light" type="submit">
                            <i class="fa fa-lock m-r-5"></i>
                            <span>确认登入</span></button>
                    </div>
                </div>


            </form>
        </div>

    </div>
</div>


<script>
    var resizefunc = [];
</script>

<!-- Main  -->
<script src="/static/admins/js/jquery.min.js"></script>
<script src="/static/admins/js/bootstrap.min.js"></script>
<script src="/static/admins/js/detect.js"></script>
<script src="/static/admins/js/fastclick.js"></script>
<script src="/static/admins/js/jquery.slimscroll.js"></script>
<script src="/static/admins/js/jquery.blockUI.js"></script>
<script src="/static/admins/js/waves.js"></script>
<script src="/static/admins/js/wow.min.js"></script>
<script src="/static/admins/js/jquery.nicescroll.js"></script>
<script src="/static/admins/js/jquery.scrollTo.min.js"></script>
<script src="/static/admins/js/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="/static/admins/js/layer/layer.js"></script>
<script src="/static/admins/js/jquery.app.js"></script>
<script>
    $().ready(function() {

        $("#loginSubmit").validate({
            rules: {
                username: "required",
                password: "required",
            },
            messages: {
                username: "请输入您的管理账号",
                password: "请输入您的管理密码",
            },
            showErrors: function(errorMap, errorList) {

                $.each(errorList, function (i, v) {

                    layer.tips(v.message, v.element, {tips: [1, '#3595CC'], time: 2000 });
                    return false;
                });
                onfocusout: false
            }
        });

    });

    $.validator.setDefaults({
        submitHandler: function () {


            $.ajax({
                type: "post",
                url: "<?php echo url('login/doLogin'); ?>",
                data: $('#loginSubmit').serialize(),
                dataType: "json",
                success: function(r) {
                    if(r.code==1){
                        location.href ="<?php echo url('index/index'); ?>";
                    }else{
                       layer.msg(r.msg);
                        $('#captcha').click();
                    }


                },
                error:function (ret) {
                    console.log(ret.responseText);
                }
            });

        }
    });
</script>
</body>
</html>