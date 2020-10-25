<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/zhuce.css" type="text/css" media="screen" />
<div id="container">
<div id="zhuce">
<h1></h1>
<div id="zhuce_left">
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl.'/js/valiregister.js'?>"></script>
<script type="text/javascript">
function register()
{
	var v1= valiMname();
	var v2 = valiPassword();
	var v3 = valiCpassword();
	var v4 = valiEmail();
	var v5 = valiRealname();
	var v6 = valiIdcard();
	var v7 = valiTreaty();
	var v8 = valiCode();
	if(v1==false)
		return false;
	if(v2==false)
		return false;
	if(v3==false)
		return false;
	if(v4==false)
		return false;
	if(v5==false)
		return false;
	if(v6==false)
		return false;
	if(v7==false)
		return false;
	if(v8==false)
		return false;
	return true;
	

}
</script>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'member-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data','onSubmit'=>'return register()'),
)); ?>
<table width="600" border="0">
  <tr>
    <td width="84"><em>*</em>昵称：</td>
    <td width="506"><?php echo $form->textField($model,'mname',array('size'=>20,'maxlength'=>20,'class'=>"text")); ?><span></span></td>
  </tr>
  <tr>
    <td><em>*</em>密码：</td>
    <td><?php echo $form->passwordField($model,'password',array('size'=>50,'maxlength'=>50,'class'=>"text")); ?><span></span></td>
  </tr>
  <tr>
    <td><em>*</em>确认密码：</td>
    <td><input class="text"  type="password" id="cpassword" name="Member[passwordrepeat]" /><span></span></td>
  </tr>
  <tr>
    <td><em>*</em>email：</td>
    <td><?php echo $form->textField($model,'email' ,array('class'=>"text")); ?><span></span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td style="color:#9a9898; font:12px/24px tahoma,simsun,arial;">根据文化部《网络游戏管理暂行办法》规定，网络游戏用户须使用<br />有效身份证件进行实名注册</td>
  </tr>
  <tr>
    <td><em></em>真实姓名：</td>
    <td><?php echo $form->textField($model,'real_name',array('size'=>15,'maxlength'=>15,'class'=>"text")); ?><span></span></td>
  </tr>
  <tr>
    <td><em></em>身份证：</td>
    <td><?php echo $form->textField($model,'id_card',array('size'=>25,'maxlength'=>25,'class'=>"text")); ?><span></span></td>
  </tr>
  <tr>
    <td><em>*</em>验证码：</td>
    <td>
		<input  class="text" type="text" id="code" name="Member[verifyCode]" width="10" style="width:100px;" />
		<?php $this->widget('CCaptcha',array('showRefreshButton'=>array('style'=>'padding-left:-20px;'),'clickableImage'=>true,'imageOptions'=>array('alt'=>'点击换图','title'=>'点击换图','style'=>'cursor:pointer;'))); ?>
       <span></span>
		<!--<input  class="text" type="text" name="yzm" width="10" /><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/yanzhengma.jpg" /><a href="#" style="text-decoration:none; font-size:12px;">看不清</a>-->
	</td>
  </tr>
  <tr>
    <td colspan="2" class="xie" ><input type="checkbox" name="Member[clause]" checked="checked" id="Member_treaty" class="text1" style="width:10px;" value="agree"/>
      我已看过并同意<a href="#">《918游戏网站服务使用协议》</a>和<a href="#">《918游戏隐私安全政策》</a></td>
    </tr>
    <tr>
    <td></td>
    <td style="padding-top:17px; padding-left:30px;"><input type="image" src="<?php echo Yii::app()->request->baseUrl; ?>/images/instantly .jpg" value="立即注册"  name="zhece"/></td>
    </tr>
</table>


<?php $this->endWidget(); ?>

</div>

<!-----------------注册右部---------------------------->
<script type="text/javascript">
function change()
{
	return vali();
}
function vali()
{
	var flag;
	$.ajax({
		url:'/site/ajaxLogin',
		type:'POST',
		async:false,
		data:{password:$("#LoginForm_password").val(),username:$("#LoginForm_username").val(),code:$("#LoginForm_verifyCode").val()},
		success:function(data){
			switch(data){
				case 'namenull':
					flag = false;alert('用户名错误');break;
				case 'passworderror':
					flag = false;alert('密码错误');break;
				case 'verifyCodeerror':
					flag = false;alert('验证码错误');break;
				default:
					flag = true;
			}
		},
	});
	return flag;
}
</script>

<div id="zhuce_right">

<h2> 已有帐号,登陆</h2>
<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/xuxian.jpg" />
<?php
	$modelLogin=new LoginForm;
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'action'=>Yii::app()->request->baseUrl.'/site/login',
		'enableClientValidation'=>true,
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
		),
		'htmlOptions'=>array('enctype'=>'multipart/form-data','onSubmit'=>'return change()'),
	)); 
?>
帐号：<br />
<?php echo $form->textField($modelLogin,'username',array('autocomplete'=>'on','class'=>'text'));?>
<br />
密码：<br />
<?php echo $form->passwordField($modelLogin,'password',array('class'=>'text')) ?>
<br />
验证码：<br />
<?php echo CHtml::activeTextField($modelLogin,'verifyCode',array('size'=>10,'maxlength'=>10,'autocomplete'=>'on'));
	$this->widget('CCaptcha',array('showRefreshButton'=>array('style'=>'padding-left:-20px;'),'clickableImage'=>true,'imageOptions'=>array('alt'=>'点击换图','title'=>'点击换图','style'=>'cursor:pointer;')));?>
<br />
<?php echo CHtml::activeCheckBox($modelLogin,'rememberMe');?><em><?php echo $form->label($modelLogin,'rememberMe');?></em>
<a href="<?php echo Yii::app()->request->baseUrl;?>/email/">忘记密码</a> <br />
<input type="image" src="<?php echo Yii::app()->request->baseUrl; ?>/images/denglu.jpg" name="登陆" style="padding-top:10px; padding-left:50px;" />
<?php $this->endWidget();?>
</div>


</div>
</div>
<!-----------------注册---------------------------->