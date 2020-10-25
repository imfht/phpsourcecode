<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>918游戏后台系统管理平台</title>
<style>
*{
margin:0 auto;
padding:0;
}
body{
background:#dfdfdf;
}
#main{
width:800px;
margin-top:10%;
background:url(<?php echo Yii::app()->request->baseUrl; ?>/system/images/login_bj.png) no-repeat;
height:451px;
}
#main .main_left{
padding:145px 0 0 70px;
width:220px;
float:left;

}
#main .main_left img{
padding-left:15px;
padding-bottom:10px;
}
h1{
	font:16px/1.5 Tahoma, Verdana, "微软雅黑";
	color:#666666;
	font-weight:700;
	padding-top:8px;
	}
	#main .main_right{
	padding:140px 0 0 10px;
	float:left;
		font:12px Helvetica, Tahoma, Arial, sans-serif;
	}
		#main .main_right table tr{
		height:40px;
		}
				#main .main_right form .text,#main .main_right form .text1{
				height:25px;
				border:1px solid #c5c5c5;
				}
				#main .main_right form .text1{
				width:80px;
				padding-right:8px;
				}
				#main .main_right form a{
				text-decoration:none;
				color:#fb665e;
				padding-left:5px;
				font-weight:700;
				}
</style>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/_assets/framework/web/js/source/jquery.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/_assets/framework/web/js/source/jquery.ba-bbq.js" type="text/javascript"></script>
</head>

<body>
<div id="main">
<div class="main_left">
<img src="<?php echo Yii::app()->request->baseUrl; ?>/system/images/logo.png" width="130" />
<h1>918游戏后台系统管理平台</h1>
</div>
<div class="main_right">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>
<table width="300" border="0">
  <tr>
    <td>用户名：</td>
    <td><?php echo $form->textField($model,'username',array('class'=>'text')); ?></td>
  </tr>
  <tr>
    <td>密&nbsp;&nbsp;&nbsp;&nbsp;码：</td>
    <td><?php echo $form->passwordField($model,'password',array('class'=>'text')); ?></td>
  </tr>
  <tr>
    <td>验证码：</td>
    <td><?php echo CHtml::activeTextField($model,'verifyCode',array('size'=>10,'maxlength'=>10,'autocomplete'=>'on','class'=>'text1'));
					  $this->widget('CCaptcha',array('showRefreshButton'=>array('style'=>'padding-left:-20px;'),'clickableImage'=>true,'imageOptions'=>array('alt'=>'点击换图','title'=>'点击换图','style'=>'cursor:pointer;')));
				?>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="image" src="<?php echo Yii::app()->request->baseUrl; ?>/system/images/login_an.jpg" /></td>
  </tr>
</table>

<?php $this->endWidget(); ?>

</div>
</div>

<script type="text/javascript">
/*<![CDATA[*/
jQuery(function($) {
jQuery('#yw0').after("<a id=\"yw0_button\" href=\"captcha?refresh=1\">\u770b\u4e0d\u6e05<\/a>");
jQuery('#yw0_button, #yw0').live('click',function(){
	jQuery.ajax({
		url: "captcha?refresh=1",
		dataType: 'json',
		cache: false,
		success: function(data) {
			jQuery('#yw0').attr('src', data['url']);
			jQuery('body').data('captcha.hash', [data['hash1'], data['hash2']]);
		}
	});
	return false;
});

});
/*]]>*/
</script>
</body>
</html>
