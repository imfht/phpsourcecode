<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-13 09:20:33
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-10-07 17:43:17
 */
use common\helpers\ImageHelper;
use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$settings = Yii::$app->settings;

AppAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language; ?>">
<head>
    <meta charset="<?= Yii::$app->charset; ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="IT Geeks">
    <!-- description -->
    <meta name="description" content="<?php echo $settings->get('Website', 'description'); ?>">
    <!-- keywords -->
    <meta name="keywords" content="<?php echo $settings->get('Website', 'keywords'); ?>">
    <?php $this->registerCsrfMetaTags(); ?>
   	<!-- Favicon
	============================================= -->
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/assets/bchduerh/images/general-elements/favicon/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/assets/bchduerh/images/general-elements/favicon/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/assets/bchduerh/images/general-elements/favicon/apple-touch-icon-114x114.png">


    <title><?= Html::encode($this->title); ?></title>
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>

<body class="homepage">

	<!-- Website Loading
	============================================= -->
	<div id="website-loading">
		<a class="logo logo-loader" href="index-default.html">
			<img src="/assets/bchduerh/images/files/logo-header-alt.png" alt="">
			<h3><span class="colored">IT Geeks</span></h3>
			<span>Web Services</span>
		</a><!-- .logo end -->
		<div class="loader">
			<div class="la-ball-pulse la-2x">
				<div></div>
				<div></div>
				<div></div>
			</div>
		</div><!-- .loader end -->
	</div><!-- .website-loading end -->

	<!-- Document Full Container
	============================================= -->
	<div id="full-container">

		<!-- Header
		============================================= -->
		<header id="header">

			<div id="header-bar-1" class="header-bar sticky">
		
				<div class="header-bar-wrap">
		
					<div class="container">
						<div class="row">
							<div class="col-md-12">
		
								<div class="hb-content">
									<a class="logo logo-header" href="index.html">
										<img src="<?= ImageHelper::tomedia($settings->get('Website', 'flogo')); ?>" data-logo-alt="/assets/bchduerh/images/files/logo-header-alt.png" alt="">
										<h3><span class="colored">IT Geeks</span></h3>
										<span>Web Services</span>
									</a><!-- .logo end -->
									<ul id="menu-main" class="menu-main">
										<li><a href="#banner" data-scroll-nav="0" class="current">首页</a></li>
										<li><a href="#what-we-do" data-scroll-nav="1">方案介绍</a></li>
										<li><a href="#video-watch" data-scroll-nav="2">应用场景</a></li>
										<li><a href="#our-projects" data-scroll-nav="3">智能设备</a></li>
										<li><a href="#pricing-plans" data-scroll-nav="4">特色优势</a></li>
										<li><a href="https://www.hopesfire.com/" target="_block">开源社区</a></li>
										<li><a href="#newsletter-subscribe" data-scroll-nav="6">联系我们</a></li>
										<li><a href="/backend">登录</a></li>
										<li><a href="/backend/site/signup">注册</a></li>
                                    </ul><!-- #menu-main end -->
									<div class="menu-mobile-btn">
										<div class="hamburger hamburger--slider">
											<span class="hamburger-box">
												<span class="hamburger-inner"></span>
											</span>
										</div>
									</div>
								</div><!-- .hb-content end -->
		
							</div><!-- .col-md-12 end -->
						</div><!-- .row end -->
					</div><!-- .container end -->

				</div><!-- .header-bar-wrap -->
		
			</div><!-- #header-bar-1 end -->

		</header><!-- #header end -->


        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]); ?>
        <?= Alert::widget(); ?>
        <?= $content; ?>
  
<!-- Footer
		============================================= -->
		<footer id="footer">

			<div id="footer-bar-2" class="footer-bar text-white">
			
				<div class="footer-bar-wrap">
			
					<div class="container">
						<div class="row">
							<div class="col-md-12">
			
								<div class="fb-row">
									<div class="col-xs-6 col-xs-offset-1">
										<?= $settings->get('Website', 'footerleft'); ?>
									</div>
									<div class="col-xs-6 col-xs-offset-1 text-right">
										<?= $settings->get('Website', 'footerright'); ?>
									</div>
								</div><!-- .fb-row end -->
			
							</div><!-- .col-md-12 end -->
						</div><!-- .row end -->
					</div><!-- .container end -->
			
				</div><!-- .footer-bar-wrap -->
			
			</div><!-- #footer-bar-2 end -->
		
		</footer><!-- #footer end -->
		
		<div class="side-panel-menu">
			<div class="mobile-side-panel-menu">
				<ul id="menu-mobile" class="menu-mobile">
		
				</ul><!-- .mobile-menu-categories end -->
			</div><!-- .mobile-side-panel-menu end -->
		</div><!-- .side-panel-menu end -->
		
	</div><!-- #full-container end -->

	<a class="scroll-top-icon scroll-top" href="javascript:;"><i class="fa fa-angle-up"></i></a>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
