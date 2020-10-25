<?php

use backend\assets\AppAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
	<head>
		<meta charset="<?= Yii::$app->charset ?>"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?= Html::csrfMetaTags() ?>
		<title><?= Html::encode($this->title) ?></title>
		<link href="/css/site.css" rel="stylesheet">
		<link href="/zui/css/zui.css" rel="stylesheet">
		<?php $this->head() ?>
	</head>
	<body>
		<?php $this->beginBody() ?>
		<div class="wrap">
			<!-- 头部导航 -->
			<nav id="w1" class="navbar-inverse navbar" role="navigation">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#w1-collapse"><span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button><a class="navbar-brand" href="/">yii-admin2 运营管理平台</a>
				</div>
				<div id="w1-collapse" class="collapse navbar-collapse">
					<ul id="w2" class="navbar-nav navbar-right nav">
						<li><a href="/site/index">首页</a></li>
						<?php if (\Yii::$app->user->isGuest): ?>
							<li><a href="/site/login" data-method="post">登录</a></li>
						<?php else: ?>
							<li><a href="/site/logout" data-method="post">注销 (<?php echo \Yii::$app->user->identity->username; ?>)</a></li>
						<?php endif; ?>
					</ul>
				</div>
			</nav>

			<!-- 主体内容 -->
			<div class="container">
				<div class="col-xs-12 col-sm-10 col-md-10">
					<?php echo $content ?>
				</div>
			</div>
		</div>
		<!-- 底部 -->
		<footer class="footer">
			<p>&copy; yii-admin2 开发者小组 <?php echo date('Y') ?> Powered by yii-admin2.cn</p>
		</footer>
		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>
