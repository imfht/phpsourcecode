<!DOCTYPE HTML>
<html>
<head>
    <include file="Public/meta"/>
    <include file="Public/head"/>
    <?php
        $img_id = modC('JUMP_BACKGROUND','','config');
        if($img_id){
        $background =get_cover($img_id,'path');
        }else{
        $background = '__PUBLIC__/images/jump_background.jpg';
        }
    ?>
    <style>
        *{
            margin: 0;
            padding: 0;
          }
        html,body{
            position:relative;
            width: 100%;
            height: 100%;
            color:#999;
            <?php if($img_id){?>
            background: url(<?php echo($background); ?>);
            <?php }?>
        }
        .main {
            position:relative;
            display:table;
            width:100%;
            height:100%;
        }
        .main .box {
            position:relative;
            min-width: 300px;
            margin: 80px auto;
            padding: 20px 40px 40px;
            text-align: center;
            background: #fff;
            border: 1px solid #ccc;
        }
        .main .box::before,.login-container::after{
            content: "";
            position: absolute;
            width: 100%;height: 100%;
            top: 3.5px;left: 0;
            background: #fff;
            z-index: -1;
            -webkit-transform: rotateZ(4deg);
            -moz-transform: rotateZ(4deg);
            -ms-transform: rotateZ(4deg);
            border: 1px solid #ccc;
        }
        .main .box::after{
            top: 5px;
            z-index: -2;
            -webkit-transform: rotateZ(-2deg);
            -moz-transform: rotateZ(-2deg);
            -ms-transform: rotateZ(-2deg);
        }
        .main .box .icon-box {
            padding:15px 0;
        }
        .main .box .icon-box>[class*=icon-] {
            margin-top: -5px;
            font-size: 52px;
        }
        .main .box .icon-box.success{
            color: #329d38;
        }
        .main .box .icon-box.error{
            color: rgb(228, 105, 105);
        }
        .font{
            color:#999;
            font-size: 25px;;
        }
    </style>
</head>
<body>

<div class="container main">

    <div class="box text-center">
        <?php if(isset($success_message)) {?>
        <div class="icon-box success">
            <i class="icon-ok-sign"></i>
        </div>

        <div class="content">
            <p class="font"><?php echo($success_message); ?></p>
        </div>

        <?php }else{?>

        <div class="icon-box error">
            <i class="icon-remove-sign"></i>
        </div>

        <div class="content">
            <p class="font"><?php echo($error_message); ?></p>
        </div>

        <?php }?>

        <p class="jump">
            页面自动 <a id="href" style="color: #329d38" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>。

            或 <a href="http://{$_SERVER['HTTP_HOST']}__ROOT__" style="color: #329d38">返回首页</a>
        </p>

    </div>
</div>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>
</body>
</html>

