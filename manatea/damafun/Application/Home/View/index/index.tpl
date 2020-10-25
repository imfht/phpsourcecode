<{include file="public/header.tpl"}>
<script>
$(function(){
   $(".case_li").hover(function(){
      $(".case_li_txt",this).stop().animate({top:"80px"},{queue:false,duration:160});
	  $(".case_li_txt",this).css("background-color","#000000");
	  $(".case_li_txt .span_mr_txt",this).attr("class","span_font");
   },function(){
      $(".case_li_txt",this).stop().animate({top:"95px"},{queue:false,duration:160});
	  $(".case_li_txt",this).css("background-color","#eee");
	  $(".case_li_txt .span_font",this).attr("class","span_mr_txt");
   })
})
</script>
	<div class="container">

		<!-- 缩略图的形式显示下面的图片以及轮播效果 分为左中右三个部分 -->
		<div class="row">
			<div class="col-md-6">
				<div class="jumbotron" style="margin-bottom:0px;padding-top:30px;padding-bottom:30px;">
					<!-- 此处为轮播效果  用法见http://v3.bootcss.com/javascript/#carousel-examples-->
					<div id="carousel-example-generic" class="carousel slide"
						data-ride="carousel">
						<!-- Indicators -->
						<ol class="carousel-indicators">
							<li data-target="#carousel-example-generic" data-slide-to="0"
								class="active"></li>
							<li data-target="#carousel-example-generic" data-slide-to="1"></li>
							<li data-target="#carousel-example-generic" data-slide-to="2"></li>
						</ol>
						<!-- 轮播图片 -->
							<div id="myCarousel" class="carousel-inner">
								<div class="item active">
									<img src="<{$smarty.const.APP_RES}>/images/Hydrangeas.jpg"></img>
								</div>
								<div class="item">
									<img src="<{$smarty.const.APP_RES}>/images/Koala.jpg"></img>
								</div>
								<div class="item ">
									<img src="<{$smarty.const.APP_RES}>/images/Tulips.jpg"></img>
								</div>
							</div>
							<a class="carousel-control left" href="#carousel-example-generic" data-slide="prev">&lsaquo;</a>
  							<a class="carousel-control right" href="#carousel-example-generic" data-slide="next">&rsaquo;</a>
					</div>
				</div>
				<{section name=row loop=$video start=0 max=4 step=1}>
				<div class="col-md-5 col-md-offset-1 ">
					<div class="jumbotron case_li" style="padding:0px; ">
						<a href="<{$smarty.const.__MODULE__}>/video/index/vid/<{$video[row].id}>"><img src="<{$smarty.const.APP_RES}>/uploads/images/<{$video[row].pic}>" /></a>
						<!-- 视频标题求特技 -->
						<div class="case_li_txt">
					      <div class="span_mr_txt"><{$video[row].name}></div>
						  <div class="span_mr_txt">点击量：<{$video[row].hot}>&nbsp;&nbsp;&nbsp;回复：<{$video[row].comnumber}></div>
						</div>
					</div>
				</div>

				<{/section}>
<!-- 				<div class="col-md-6">
					<div class="jumbotron">
						<h3>Hello, world</h3>
					</div>
				</div> -->
			</div>
			<div class="col-md-3">
				<{section name=row loop=$video start=4 max=4 step=1}>
					<div class="jumbotron case_li" style="padding:0px;" >
						<a href="<{$smarty.const.__MODULE__}>/video/index/vid/<{$video[row].id}>"><img src="<{$smarty.const.APP_RES}>/uploads/images/<{$video[row].pic}>" /></a>
						<!-- 视频标题求特技 -->
						 <div class="case_li_txt">
					      <div class="span_mr_txt"><{$video[row].name}></div>
						  <div class="span_mr_txt">点击量：<{$video[row].hot}>&nbsp;&nbsp;&nbsp;&nbsp;回复数：<{$video[row].comnumber}></div>
						</div>
					</div>
				<{/section}>
			</div>
			<div class="col-md-3">

				<div class="col-md-9">
				<span class="span_mr_txt"><b>猜你喜欢</b></span>
				<{foreach $recom as $row}>
				<div class="jumbotron case_li" style="padding:0px;">
						<a href="<{$smarty.const.__MODULE__}>/video/index/vid/<{$row.id}>"><img src="<{$smarty.const.APP_RES}>/uploads/images/<{$row.pic}>" /></a>
						<!-- 视频标题求特技 -->
						 <div class="case_li_txt">
					      <div class="span_mr_txt"><{$row.name}></div>
						  <div class="span_mr_txt">点击量：<{$row.hot}>&nbsp;&nbsp;&nbsp;&nbsp;回复数：<{$row.comnumber}></div>
						</div>
					</div>
				<{/foreach}>
				</div>
			</div>

		</div>
	</div>

</body>
<{include file="public/footer.tpl"}>