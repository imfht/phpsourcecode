<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/css.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" type="text/css" media="screen" />

<title>918平台游戏--home</title>
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F8cc15c51c6678071800fd36ffda82d58' type='text/javascript'%3E%3C/script%3E"));
</script>

</head>
<body>
<div id="header">
  <div id="main_header">
    <div id="nav"> <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo.jpg" style="float:left;" />
      <ul>
        <li style="padding-left:40px;"><a href="<?php echo Yii::app()->request->baseUrl; ?>" class="home"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/home.jpg" /></a></li>
        <li><a href="<?php echo Yii::app()->request->baseUrl; ?>/member/"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/buluo.jpg" /></a></li>
        <li><a href="<?php echo Yii::app()->request->baseUrl; ?>/order/"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/chongzhi.jpg" /></a></li>
        <li><a href="#"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/jifen.jpg" /></a></li>
      </ul>
    </div>
  </div>
</div>
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/find_pass.css" type="text/css" media="screen" />
<!-----------------头部 end---------------------------->
<div id="container">
<!-----------------上圆角---------------------------->
  <div id="find">
  <b class="xb1"></b>
<b class="xb2"></b>
<b class="xb3"></b>
<b class="xb4"></b>
<b class="xb5"></b>
<b class="xb6"></b>
<b class="xb7"></b>
   <!-----------------登陆---------------------------->
    <div id="find_main">
 <h1>用户登陆</h1>
 <h2>请填写登陆信息</h2>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/js/jquery.valiArticleLogin.js"></script>
      <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
	'htmlOptions'=>array('enctype'=>'multipart/form-data','onSubmit'=>'return change()'),
)); ?>
        <table>
          <tr>
            <td align="right">用户名：</td>
            <td><?php echo $form->textField($model,'username',array('class'=>'text')); ?></td>
          </tr>
          <tr>
            <td align="right">密码：</td>
            <td><?php echo $form->passwordField($model,'password',array('class'=>'text')); ?></td>
          </tr>
          <tr>
            <td align="right">验证码：</td>
            <td>

				<?php echo CHtml::activeTextField($model,'verifyCode',array('size'=>10,'maxlength'=>10,'autocomplete'=>'on','class'=>'text1'));
					  $this->widget('CCaptcha',array('showRefreshButton'=>array('style'=>'padding-left:-20px;'),'clickableImage'=>true,'imageOptions'=>array('alt'=>'点击换图','title'=>'点击换图','style'=>'cursor:pointer;')));
				?>
			</td>
          </tr>
           <tr>
            <td align="right"></td>
            <td><?php echo CHtml::activeCheckBox($model,'rememberMe').$form->label($model,'rememberMe');?>  <a href="<?php echo Yii::app()->request->baseUrl; ?>/email/">忘记密码</a></td>
          </tr>
          <tr>
            <td></td>
            <td><a href="<?php echo Yii::app()->request->baseUrl; ?>/site/register/"><img  src="<?php echo Yii::app()->request->baseUrl; ?>/images/zc.png" value="注册" /></a><input class="dl" type="image" src="<?php echo Yii::app()->request->baseUrl; ?>/images/dl.png" /></td>
          </tr>
        </table>
      <?php $this->endWidget(); ?>
    </div>
    <!-----------------下圆角---------------------------->
     <b class="xb7"></b>
 <b class="xb6"></b>
 <b class="xb5"></b>
 <b class="xb4"></b>
 <b class="xb3"></b>
 <b class="xb2"></b>
 <b class="xb1"></b> 
    
  </div>
</div>
<!-----------------登陆 end---------------------------->
 <div id="tishi" style="display:none;">
  <h1><span>错误提示</span><a href="#"></a></h1>
  <p>您的用户名或密码填写错误！</p>
  </div>


