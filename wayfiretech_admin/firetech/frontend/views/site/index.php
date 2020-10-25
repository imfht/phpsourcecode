<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-22 15:56:49
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-10-08 16:04:44
 */
use common\helpers\ImageHelper;
use richardfan\widget\JSRegister;
use yii\helpers\Url;

/* @var $this yii\web\View */
$settings = Yii::$app->settings;
$title = $settings->get('Website', 'name');
$intro = $settings->get('Website', 'intro');
$statcode = $settings->get('Website', 'statcode');
$this->title = $title.'-'.$intro;

?>

		<!-- Banner
		============================================= -->
		<section id="banner" data-scroll-index="0">
		
			<div class="banner-parallax" data-banner-height="750">
				<div class="slider-banner">
					<ul class="slick-slider slider-img-bg">
						<?php foreach ($slides as $item => $val):?>
							<li>
								<div class="overlay-colored color-bg-dark opacity-70"></div><!-- .overlay-colored end -->
								<div class="slide">
									<img src="<?php echo $val['images']; ?>" alt="">
									<div class="slide-content">
										<div class="container">
											<div class="row">
												<div class="col-md-10 col-md-offset-1">
							
													<div class="banner-center-box text-white text-center">
														<h5><?php echo $val['title']; ?></h5>
														<h1>
														<?php echo $val['description']; ?>
														</h1>
														<a class="btn colorful large hover-dark mt-30 move-top"  target="_blank" href="<?php echo $val['menuurl']; ?>"><?php echo $val['menuname']; ?></a>
													</div><!-- .banner-center-box end -->
							
												</div><!-- .col-md-10 end -->
											</div><!-- .row end -->
										</div><!-- .container end -->
									</div><!-- .slide-content end -->
								</div><!-- .slide end -->
							</li>
						<?php endforeach; ?>
					</ul><!-- .slick-slider end -->
				</div><!-- .slider-banner end -->
			</div><!-- .banner-parallax end -->
		
		</section><!-- #banner end -->
	
		<!-- Content
		============================================= -->
		<section id="content">

			<div id="content-wrap">

				<!-- === What We Do =========== -->
				<div id="what-we-do" class="section-flat" data-scroll-index="1">

					<div class="section-content">

						<div class="container">
							<div class="row">
								<div class="col-md-8 col-md-offset-2">

									<div class="section-title text-center">
										<h2>方案介绍</h2>
									</div><!-- .section-title end -->

								</div><!-- .col-md-8 end -->
								<?php foreach ($program as $kk => $list):?>
									<div class="col-md-4">

										<div class="box-info box-service-1">
											<div class="box-icon">
												<i class="<?= $list['icon']; ?>"></i>
											</div><!-- .box-icon end -->
											<div class="box-content">
												<h4><a href="javascript:;"><?php echo $list['title']; ?></a></h4>
												<p>
													<?php echo $list['description']; ?>
		
												</p>
											</div><!-- .box-content end -->
										</div><!-- .box-info box-service-1 end -->

									</div><!-- .col-md-4 end -->

								<?php endforeach; ?>	
								<div class="col-md-12">
									
									<a class="btn colorful medium hover-dark mt-50 center-horizontal" href="#">查看完整方案</a>
									
								</div><!-- .col-md-12 end -->
							</div><!-- .row end -->
						</div><!-- .container end -->
						
					</div><!-- .section-content end -->
					
				</div><!-- .section-flat end -->

				<!-- === About Us =========== -->
				<div id="about-us" class="section-flat" data-scroll-index="1">
				
					<div class="section-content">
				
						<div class="container">
							<div class="row">
								<div class="col-md-6 pt-60 pt-md-0">
				
									<div class="section-title">
										<h2><?= $about[0]['title']; ?></h2>
										<p>
											<?= $about[0]['description']; ?>
										</p>
										<a class="btn-rm" href="javascript:;">了解更多<i class="fas fa-long-arrow-alt-up"></i></a>
									</div><!-- .section-title end -->
				
								</div><!-- .col-md-6 end -->
								<div class="col-md-6 mt-md-60">

									<div class="img-preview img-featured">
										<img class="img-1" src=" <?= ImageHelper::tomedia($about[0]['thumb']); ?>" alt="">
									</div><!-- .img-preview end -->

								</div><!-- .col-md-6 end -->

							</div><!-- .row end -->
						</div><!-- .container end -->
				
					</div><!-- .section-content end -->
				
				</div><!-- .section-flat end -->

				<!-- === Watch Video =========== -->
				<div id="watch-video" class="section-parallax" data-scroll-index="2">
				
					<img src="/assets/bchduerh/images/files/parallax-bg/img-2.jpg" alt="">
					<div class="overlay-colored color-bg-dark opacity-80"></div><!-- .overlay-colored end -->
					<div class="section-content">
				
						<div class="container">
							<div class="row">
								<div class="col-md-8 col-md-offset-2">
								
									<div class="section-title text-center text-white">
										<h2>基于人脸的会员管理与客流分析</h2>
									</div><!-- .section-title end -->
								
								</div><!-- .col-md-8 end -->
								<div class="col-md-12 text-center">

									<a href="https://vimeo.com/45830194" class="btn-video lightbox-iframe"><i class="fa fa-play"></i></a>

								</div><!-- .col-md-12 end -->
				
							</div><!-- .row end -->
						</div><!-- .container end -->
				
					</div><!-- .section-content end -->
				
				</div><!-- .section-parallax end -->

				<!-- === Our Services =========== -->
				<div id="our-services" class="section-flat" data-scroll-index="2">
				
					<div class="section-content">
				
						<div class="container">
							<div class="row">
								<div class="col-md-12">

									<div class="slider-services-2">
										<ul class="slick-slider">
											<?php foreach ($scene as $kk => $list):?>
												<li>
												<div class="box-info box-service-2">
													<div class="box-content">
														<h4><a href="javascript:;"><?= $list['title']; ?></a></h4>
														<p>
															<?= $list['description']; ?>
														</p>
														<a class="btn-rm" href="<?= $list['linkurl']; ?>">了解更多<i class="fas fa-long-arrow-alt-up"></i></a>
													</div><!-- .box-content end -->
												</div><!-- .box-info box-service-2 end -->
											</li>
											<?php endforeach; ?>	
											
										</ul><!-- .slick-slider end -->
									</div><!-- .slider-services-2 end -->
								
								</div><!-- .col-md-12 end -->
								
							</div><!-- .row end -->
						</div><!-- .container end -->
				
					</div><!-- .section-content end -->
				
				</div><!-- .section-flat end -->

				<!-- === Our Projects =========== -->
				<div id="our-projects" class="section-flat" data-scroll-index="3">
				
					<div class="section-content">
				
						<div class="container">
							<div class="row">
								<div class="col-md-8 col-md-offset-2">
								
									<div class="section-title text-center">
										<h2>智能硬件</h2>
									</div><!-- .section-title end -->
								
								</div><!-- .col-md-8 end -->
							</div><!-- .row end -->
						</div><!-- .container end -->
						<div class="container-fluid">
							<div class="row">
								<div class="col-md-12">
						
									<div class="slider-projects">
										<ul class="slick-slider">
											<?php foreach ($facility as $kk => $list):?>
											
											<li>
												<div class="box-preview box-project">
													<div class="box-img img-bg">
														<a href="javascript:;"><img src="<?= ImageHelper::tomedia($list['thumb']); ?>" alt=""></a>
														<div class="overlay">
															<div class="overlay-inner">
															
																<h4><a href="javascript:;"><?= $list['title']; ?></a></h4>
						
																<ul class="project-categories">
																	<li><a href="javascript:;">店滴AI</a>,</li>
																	<li><a href="javascript:;">邀您体验，可代理产品</a></li>
																</ul><!-- .project-categories end -->
															</div><!-- .overlay-inner end -->
														</div><!-- .overlay end -->
													</div><!-- .box-img end -->
												</div><!-- .box-preview end -->
											</li>
											<?php endforeach; ?>	
										</ul><!-- .slick-slider end -->
									</div><!-- .slider-projects end -->
									
						
								</div><!-- .col-md-12 end -->
			
								<a class="btn colorful medium hover-dark mt-50 center-horizontal" href="#">联系购买</a>

									
							</div><!-- .row end -->
						</div><!-- .container-fluid end -->
				
					</div><!-- .section-content end -->
				
				</div><!-- .section-flat end -->
				
				<!-- === Pricing Plans =========== -->
				<div id="pricing-plans" class="section-flat" data-scroll-index="4">
				
					<div class="section-content">
				
						<div class="container">
							<div class="row">
								<div class="col-md-8 col-md-offset-2">
				
									<div class="section-title text-center">
										<h2>特色优势</h2>
									</div><!-- .section-title end -->
				
								</div><!-- .col-md-8 end -->
								<div class="col-md-12">
									  <div class="row"> 
								<?php foreach ($superiority as $kk => $list):?>
										<div class="col-lg-4 col-md-6 single-service"> 
												<div class="inner"> 
												<div class="title"> 
												<div class="icon"> 
												<i class="<?= $list['icon']; ?>"></i> 
												</div> 
												<h4><?= $list['title']; ?></h4> 
												</div> 
												<div class="content"> 
												<p><?= $list['description']; ?></p> 
												</div> 
												</div> 
											</div> 

								<?php endforeach; ?>	
											</div>
									
								</div><!-- .col-md-12 end -->
							</div><!-- .row end -->
						</div><!-- .container end -->
				
					</div><!-- .section-content end -->
				
				</div><!-- .section-flat end -->

				
				<!-- === Newsletter Subscribe =========== -->
				<div id="newsletter-subscribe" class="section-parallax" data-scroll-index="6">
				
					<!-- <img src="/assets/bchduerh/images/files/parallax-bg/img-2.jpg" alt=""> -->
					<div class="overlay-colored color-bg-gradient-1 opacity-50"></div><!-- .overlay-colored end -->
					<div class="section-content">
				
						<div class="container">
							<div class="row">
								<div class="col-md-8 col-md-offset-2">
				
									<div class="section-title text-center text-white">
										<h2>联系我们</h2>
										<p>
											留下您的联系方式，我们给您回电
										</p>
									</div><!-- .section-title end -->
				
								</div><!-- .col-md-8 end -->
								<div class="col-md-6 col-md-offset-3">
									<form id="contact-form" action="relation" method="post">
										<div class="form-group">
											<label for="email">姓名:</label>
											<input type="text" class="form-control" id="name">
										</div>
										<div class="form-group">
											<label for="pwd">电话:</label>
											<input type="text" class="form-control" id="contact">
										</div>
										<div class="form-group">
											<label for="pwd"></label>
											<button type="button"  class="btn btn-primary form-control resg" id="relations" style="border-radius: 50px;">
												提交
											</button>

										</div>
										</form>
				
								</div><!-- .col-md-6 end -->
				
							</div><!-- .row end -->
						</div><!-- .container end -->
				
					</div><!-- .section-content end -->
				
				</div><!-- .section-parallax end -->

			</div><!-- #content-wrap -->
			
		</section><!-- #content end -->
<?php JSRegister::begin([
    'key' => '3445',
]); ?>
<script>
	$('#relations').click(function(){
		let name = $('#name').val(),
		csrfToken = "<?= Yii::$app->request->csrfToken; ?>",
		contact = $('#contact').val();
		if(!name){
			alert('请输入姓名')
		}
		if(!contact){
			alert('请输入手机号')

		}

		$.post("<?= Url::to(['site/relations']); ?>",{'_csrf-frontend':csrfToken,name:name,contact:contact},function(res){
			if(res.code==0){
			console.log(res)
            alert(res.msg);

			}
		},'json');
		console.log('565655')
	})
	
	<?= $statcode ?>

</script>
<?php JSRegister::end([]);