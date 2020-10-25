<extend name="Base/common"/>

<block name="body">
    <style>
        .font{
            font-size: 25px;;
        }
    </style>

    <?php
    $background = '__PUBLIC__/Core/images/jump_background.jpg';

     ?>

<div style="padding:280px 100px 0 100px;height:450px; background: url(<?php echo($background); ?>)">

<div class="text-center " style="margin: 0 auto; ">

<?php if(isset($message)) {?>

		<div class="alert alert-success"  > 
		        <p class="font"><i class="glyphicon glyphicon-ok pull-left"></i><?php echo($message); ?></p>
		</div>
<?php }else{?>

			<div class="alert alert-danger" >
			   
			        <p class="font"> <i class="glyphicon glyphicon-remove pull-left"></i> <?php echo($error); ?></p>
			</div>

<?php }?>


    <p class="jump">
        页面自动 <a id="href" style="color: green" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait">
		<?php echo($waitSecond); ?>
		</b>
        或 <a href="http://{$_SERVER['HTTP_HOST']}__ROOT__" style="color: green">返回首页</a>
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
</block>