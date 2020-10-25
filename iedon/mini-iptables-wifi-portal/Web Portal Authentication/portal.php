<?php
/*
    iEdon Mini Portal System
*/
require_once('config.php');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title><?php echo constant("PORTAL_NAME");?> Authentication</title>  
    <link rel="stylesheet" href="css/pintuer.css">
    <link rel="stylesheet" href="css/portal.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/pintuer.js"></script>
</head>
<body>
<?php
if(isset($_GET['nas']) && isset($_GET['ip']) && isset($_GET['act']) && $_GET['act'] == 'logout')
{
	$url = constant($_GET['nas'] . '_URL') . '?act=logout';
	$post_data = array ("ip" => $_GET['ip']);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$output = curl_exec($ch);
	curl_close($ch);
	if($output != "OK")
	{
	    echo '<br /><h1 style="padding-left:20px">Disconnect failed：Exception on callback</h1></body>';
	} else {
		header("Location: " . DEFAULT_REDIRECT_URL);
	}
	exit;
}
if(!isset($_GET['ip']) || !isset($_GET['mac']) || !isset($_GET['nas']))
{
	echo '<br /><h1 style="padding-left:20px">Unknown status, please try it again later.</h1></body>';
	exit;
}
?>
<div class="bg"></div>
<div class="container">
    <div class="line bouncein">
        <div class="xs6 xm4 xs3-move xm4-move">
            <div style="height:50px;"></div>
            <div class="media media-y margin-big-bottom">           
            </div>
            <form action="login.php?nas=<?php $attr=$_GET['nas'].'&ip='.$_GET['ip']."&mac=".urlencode($_GET['mac']);if(isset($_GET['redirect'])){$attr=$attr."&redirect=".urlencode($_GET['redirect']);}echo $attr;?>" method="post">
            <div class="panel loginbox">
                <div class="text-center margin-big padding-big-top">
					  <div class="logo margin-big-left fadein-top">
						<h1 style="text-shadow:0 0 0 #000, 0px 0px 1px #000"><?php echo constant("PORTAL_NAME");?> Authentication</h1>
						<span>IP：<?php echo $_GET['ip']; ?>&nbsp;&nbsp;&nbsp;&nbsp;AP Node：<?php echo constant($_GET['nas'] . '_NAME'); ?></span>
				      </div>
				</div>
                <div class="panel-body" style="padding:30px; padding-bottom:10px; padding-top:10px;">
                    <div class="form-group">
                        <div class="field field-icon-right">
                            <input autofocus type="text" class="input input-big" name="usr" placeholder="Your ID" data-validate="required:Please enter your ID" />
                            <span class="icon icon-user margin-small"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="field field-icon-right">
                            <input type="password" class="input input-big" name="pwd" placeholder="Your password" data-validate="required:Please enter your password" />
                            <span class="icon icon-key margin-small"></span>
                        </div>
                    </div>
                </div>
                <div style="padding:0px 30px 10px 30px">
					<input type="submit" class="button button-block bg-main text-big input-big" value="Log in">
				</div>
				<div style="text-align:center;margin:0 auto;font-weight:bold;color:#fff;text-shadow:0 0 0 #000, 0px 0px 1px #000;">
					<p style="margin-bottom:0px"><?php echo constant("FOOTER_TEXT");?></p>
					<p style="padding-bottom:10px"><?php echo constant("COPYRIGHT");?></p>
				</div>
            </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>