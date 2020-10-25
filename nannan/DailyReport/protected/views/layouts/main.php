<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<?php Yii::app()->bootstrap->register();?>
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="mainmenu">
		<?php $this->widget('bootstrap.widgets.TbNavbar',array(
			'type'=>'inverse',
			'brand'=>'信息管理网',
			'brandUrl'=>array('/dailyreport/index'),
			'collapse'=>true,
			'items'=>array(
				array(
					'class'=>'bootstrap.widgets.TbMenu',
					'items'=>array(
						array('label'=>'日报','icon'=>'book','url'=>array('dailyreport/index')),
						array('label'=>'重要信息','icon'=>'heart','url'=>array('post/index')),
						array('label'=>'培训信息','icon'=>'file','url'=>array('file/index')),
						array('label'=>'用户管理','url'=>array('user/admin'),'visible'=>Yii::app()->user->name==='admin'),
					)),
				array(
					'class'=>'bootstrap.widgets.TbMenu',
					'htmlOptions'=>array('class'=>'pull-right'),
					'items'=>array(
						array('label'=>'注册','url'=>array('user/create'),'visible'=>Yii::app()->user->isGuest),
						array('label'=>'登录','url'=>array('site/login'),'visible'=>Yii::app()->user->isGuest),
						array('label'=>'欢迎您'.Yii::app()->user->name,'visible'=>!Yii::app()->user->isGuest),
						array('label'=>'个人信息','url'=>array('user/view'),'icon'=>'user','visible'=>!Yii::app()->user->isGuest),
						array('label'=>'退出','url'=>array('site/logout'),'visible'=>!Yii::app()->user->isGuest),
					)),
			),
		));?>
	</div><!-- mainmenu -->
	</br>
	</br>
	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by <a href="http://chuangxin.dlut.edu.cn" target="_blank">创新实验学院.</a><br/>
		All Rights Reserved.<br/>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
