@extends('layouts.app') @section('content')
<style>
.lazy {
	width: 100%;
	height: 0;
	background-size: 100%;
}

.product-container {
	width: 260px;
	margin: 5px auto;
	border-radius: 10px;
	background: #f6f8f7;
}

.name {
	border-bottom: 1px solid@gray-light;
	font-size: 20px;
	padding: 2px;
}

.interest {
	color: #1da427;
	font-size: 70px;
	font-weight: bold;
	padding: 0px;
	margin-bottom: -30px;
}

.percent {
	font-size: 31px;
}

.intro {
	padding: 5px;
}

.strong {
	padding: 3px;
	font-size: 17px;
	color: @white;
	background: #326c84;
	border-radius: 0 0 10px 10px;
}

.active {
	color: red;
}

.rowone {
	overflow: hidden;
	text-overflow: ellipsis;
	display: -webkit-box;
	-webkit-box-orient: vertical;
	-webkit-line-clamp: 1;
}

.post-text {
	padding: 10px;
	line-height: 1.8; //
	font-size: 20px;
}

img {
	max-width: 85%;
}

.h1, h1, .h2, h2, .h3, h3 {
	font-size: 1.5rem;
}
</style>
<link rel="stylesheet" href="/css/share.min.css">

<script src="/js/jquery.cookie.js"></script>
<script src="/js/lazyload.min.js"></script>
<script type="text/javascript" charset="utf-8">
  $(function() {
	//lazy img
	$("img.lazy").lazyload();
  });
</script>
<script type="text/javascript">
$(document).ready(function () {

	$(".set_star,.set_read,.set_read_later").on('click',function(){
		article_sub_id = $(this).attr('article_sub_id');
		active = $(this).hasClass("active");

		if($(this).hasClass("set_star")){
			status = active?"read":"star";
		} else if($(this).hasClass("set_read")){
			status = active?"unread":"star";
		} else if($(this).hasClass("set_read_later")){
			status = active?"unread":"read_later";
		} else {
			return '';
		}

		item = $(this);
		$.get("{{ url('/articles/status') }}"+"/"+article_sub_id,{"status":status},function(result){
			result_arr = JSON.parse(result);
			if(result_arr.code != 9999){
				alert("设置失败");
			} else {
				if(active){
					item.removeClass("active");
				} else {
					item.siblings().removeClass("active");
					item.addClass("active");
				}
			}
		});
	});

	$("#marked_all_read").on('click',function(){
		var ids = $(this).attr('ids');
		$(this).attr("disabled",true);
		$(this).text('Submit Article As Reading');
		$.get("{{ url('/articles/allstatus') }}",{"ids":ids,"status":"read"},function(result){
			result_arr = JSON.parse(result);
			if(result_arr.code != 9999){
				$("#marked_all_read").attr("disabled",false);
				$("#marked_all_read").text("Marked All Read");
				alert("设置失败");
			} else {
				location.href="";
			}
		});
	});

	$(".post-text").each(function(){
		height=$(this).height();
		if(height > 1000) {
			$(this).css("height","360");
			$(this).css("overflow","hidden");
			$(this).after("<p class=\"morecon\" style=\"align-text: right;text-align: right; color: #337ab7; cursor:pointer; font-size: 2em; \">click for more content...</p>");
			$(this).parent().find(".view-all").css("display","");
		}
	});

	$(".view-all").on('click',function(){
		$(this).parent().parent().children("div.post-text").css("height","auto");
		$(this).css("display","none");
		$(this).parent().parent().find(".morecon").css("display","none");
	});

	$(".morecon").on('click',function(){
		$(this).parent().children("div.post-text").css("height","auto");
		$(this).css("display","none");
		$(this).parent().parent().find(".view-all").css("display","none");
	});
	
	//处理屏蔽图片
	$("#unable_img").on('click',function(){
		if($("#unable_img").is(':checked')){
			$.cookie('unable_img', true);
		} else {
			$.cookie('unable_img', false);
		}
		location.href="";
	});

	$(".post-text img").on('click',function(){
		if($(this).attr("orignal_src") != null){
			$(this).attr("src", $(this).attr("orignal_src"));//修改图片路径
		}
	});
	if($.cookie("unable_img") != null && $.cookie("unable_img")=="true"){
		$("#unable_img").prop('checked', true);

		$(".post img").each(function(){
			if($(this).attr("src") != null && $(this).attr("src")!="" && $(this).attr("src") != "/img/unable_img.png") {
			    $(this).attr("orignal_src",$(this).attr("src"));//修改图片路径
			    $(this).attr("src","/img/unable_img.png");//修改图片路径
			}

		    $(this).parent("a").attr("orignal_href", $(this).parent("a").attr("href"));//修改链接路径
		    $(this).parent("a").attr("href","javascript:void(0)");//修改链接路径
		});
	}

	//处理一目十行
	$("#unable_desc").on('click',function(){
		if($("#unable_desc").is(':checked')){
			$.cookie('unable_desc', true);
		} else {
			$.cookie('unable_desc', false);
		}
		location.href="";
	});

	if($.cookie("unable_desc") != null && $.cookie("unable_desc")=="true"){
		$("#unable_desc").prop('checked', true);
	}

	var ua =  navigator.userAgent;
	isAndroid = /Android/i.test(ua);
	isBlackBerry = /BlackBerry/i.test(ua);
	isWindowPhone = /IEMobile/i.test(ua);
	isIOS = /iPhone|iPad|iPod/i.test(ua);
	isMobile = isAndroid || isBlackBerry || isWindowPhone || isIOS;
	if(isMobile){
		$("#navBody").css("display","none");
		
		$(".category_item").each(function(){
			$(this).css("display","none");
		});
	}

	$("#navHeader").on('click',function(){
		$("#navBody").toggle();
	});
	
	$(".unfold_category_item").on('click','.unfold_category_item',function(){
		$(this).parent().parent().find(".category_item").toggle();
	});
	
	$(".share_btn").on('click',function(){
		$(this).parent().find(".social-share").toggle();
	});

	$(".feed_quick_sub").on('click',function(){
		var feed_id = $(this).attr('feed_id');
		$.get("{{ url('/feeds/quickstore') }}",{"feed_id":feed_id},function(result){
			result_arr = JSON.parse(result);
			if(result_arr.code != 9999){
				alert(result_arr.msg);
			} else {
				alert(result_arr.msg);
			}
		});
	});

	//play audio
	$(".playaudio").on('click',function(){
		var article_sub_id = $(this).attr('article_sub_id');
		$("#audio").attr("src","/article/record/"+article_sub_id);
	});
	
	$(".icon-heart").on('click',function(){
		var title = $(this).attr('data-title');
		var url = $(this).attr('data-url');
		window.open('/notes?add_content='+url);
	});
	var status = '{{$status}}';
	$.ajax({
	    url: "{{ url('article/navinfo') }}",
	    type: 'GET',
	    data: {"_token":"{{ csrf_token() }}","status":status},
	    success: function(result) {
	    	result_arr = JSON.parse(result);
			if(result_arr.code != 9999){
				alert('处理失败，请稍后再试');
			} else {
				$.each( result_arr.result.nav_infos, function( navId, navInfo ){
					var li = '<li role="presentation"><span class="category_items"><img src="/img/icon/unfold.png" width="25px" class="unfold_category_item"/><a href="'+"{{ url('articles') }}?category_id="+navId+"&status="+status+'">'+navInfo.category_info.category_name+'['+Object.getOwnPropertyNames(navInfo.list).length+']</a></span>';
					if(Object.getOwnPropertyNames(navInfo.list).length > 0){
						li += '<ul class="category_item">';
						$.each(navInfo.list,function(index, item){
							var countInfo = item.feed_count > 99 ? '99+' : item.feed_count;
							li += '<li class="rowone">';
							li += '<a href="'+"{{ url('articles') }}?feed_id="+item.feed_id+"&status="+status+'">';
							li += '<span>[' + countInfo + ']' + item.feed_name + '</span>';
						});
						li += '</ul>';
					}
					li += '</li>'
					$("#nav").append(li);
				});
			}
	    }
	});
});
</script>
<div class="container">
	<div class="row">
		<div class=" col-md-4">
			@include('common.success')
			<div class="card card-default">
				<div class="card-header" id="navHeader">
					订阅分类
					<div style="float: right">
						<a href="{{ url('kindles') }}">[SendKindle]</a> <a
							href="{{'/feeds/setting'}}">[管理]</a>
					</div>
				</div>

				<div class="card-body" id="navBody">
					<ul class="nav nav-pills nav-stacked" id="nav">
					
					</ul>
				</div>
			</div>
		</div>

		<div class=" col-md-8">
			<div class="card card-default">
				<div class="card-header">
					@if($status == 'unread') 未读 
					@elseif($status == 'read') 已读
					@elseif($status == 'star') 收藏 
					@elseif($status == 'read_later') 稍后阅读
					@endif 文章
					[<a href="{{ url('articles?status=unread&feed_id='.$feed_id) }}">未读</a>]
					[<a href="{{ url('articles?status=read&feed_id='.$feed_id) }}">已读</a>]
					[<a href="{{ url('articles?status=star&feed_id='.$feed_id) }}">加星</a>]
					[<a href="{{ url('articles?status=read_later&feed_id='.$feed_id) }}">稍后阅读</a>]
					
					<div style="float: right">
						<input type="checkbox" value="" id="unable_desc" />一目十行 
						<input type="checkbox" value="" id="unable_img" />屏图 
						<a href="{{ url('feed/checkNewFeed')}}">
							<img alt="" src="/img/icon/refresh.png" style="width: 15px; margin-right: 10px;">
						</a> 
						<a href="{{ url('feeds/explorer')}}">
							[发现 <sup style="color: red;"> <img alt="" src="/img/icon/recommend.png" style="width: 25px;">推荐</sup>]
						</a> 
						
						<a href="{{ url('feeds')}}">[添加订阅]</a>
					</div>
				</div>
			</div>
			<!--<div class="card-body">-->
			<!-- Display Validation Errors -->
                    @include('common.errors')

                    		@if (count($article_subs) > 0)
                    			<?php $article_sub_ids = array();?>
	                    		@foreach ($article_subs as $articleSub)
	                    		<?php $article = $articleSub->article;if(empty($article)) continue;$article_sub_ids[] = $articleSub->id;?>
								<div class="card" style="margin-bottom: 10px">
				<div class="card-block" style="padding: 10px;">
				
					@if(!empty($article->subject))
					<h4 class="card-title">
						<img class="playaudio" article_sub_id="{{$articleSub->id}}" alt=""
							src="/img/icon/music.png" width="30px"> <a
							href="{{ $article->url }}">[原文]</a> <a
							href="{{ url('article/view/'.$article->id) }}">{{
							$article->subject }}</a>
					</h4>
					@endif
					
					<p class="card-text">
						<small class="text-muted">来源：<a
							href="{{ url('articles?status=unread&feed_id='.$article->feed->id) }}"
							target="_blank">{{ $article->feed->feed_name}}</a> *
							{{$article->published}}
						</small>
					</p>

					@if($unable_desc == "false")
					<div class="card-text post-text">
										<?php
										$content = $article->content;
										if ($unable_img == "true") {
											$content = str_replace ( 'src="', 'src="/img/unable_img.png" orignal_src="', $content );
											$content = str_replace ( "src='", "src='/img/unable_img.png' orignal_src='", $content );
										}
										echo App\Http\Utils\CommonUtil::formatContentHtml ( $content );
										?>
									  </div>
					<div class="card-text text-right">
						<!-- share start -->
						<p class="social-share" style="display: none" data-mode="prepend"
							data-weibo-title="{{ $article->subject }}"
							data-weibo-appKey="567683707" data-weibo-ralateUid="1671353227"
							data-title="{{ $article->subject }}"
							data-url="http://{{$_SERVER['SERVER_NAME']}}/article/view/{{$article->id}}"
							data-image="{{ $article->image_url }}"
							data-sites="facebook,twitter,google,weibo"
							data-mobile-sites="facebook,twitter,google,weibo"
							data-wechat-qrcode-title="请打开微信扫一扫">
							<a href="javascript:void(0);"
								class="social-share-icon icon-heart" class=""
								data-title="{{ $article->subject }} From:http://task.congcong.us/article/view/{{$article->id}}"
								data-url="http://{{$_SERVER['SERVER_NAME']}}/article/view/{{$article->id}}"></a>
						</p>
						<!-- share end -->
						<a href="javascript:void(0)" target="_blank"
							class="btn btn-outline-secondary btn-sm share_btn" title="分享"><img
							src="/img/icon/share.png" width="20px" />Share</a> <a
							href="javascript:void(0);" article_sub_id="{{$articleSub->id}}"
							class="btn btn-outline-secondary btn-sm set_read @if($articleSub->status == 'read') active @endif"
							title="已读"><img src="/img/icon/read_already.png" width="20px" />Read</a>
						<a href="javascript:void(0);" article_sub_id="{{$articleSub->id}}"
							class="btn btn-outline-secondary btn-sm set_read_later @if($articleSub->status == 'read_later') active @endif"
							title="稍后阅读"><img src="/img/icon/read_later.png" width="20px" />Later</a>
						<a href="javascript:void(0);" article_sub_id="{{$articleSub->id}}"
							class="btn btn-outline-secondary btn-sm set_star @if($articleSub->status == 'star') active @endif"
							title="加星"><img src="/img/icon/read_star.png" width="20px" />Star</a>
						<a href="javascript:void(0);" style="display: none"
							class="btn btn-outline-warning btn-sm view-all"><img
							src="/img/icon/read_more.png" width="30px" />View All</a>
					</div>
					@endif
				</div>
			</div>
			
			@endforeach
				<audio style="position: fixed; float: right"></audio>
				{!! $article_subs->appends($page_params)->links() !!}

				@if(!isset($_GET['status']) || $_GET['status'] == 'unread')
				<button class="col-md-12 btn btn-outline-info" id="marked_all_read"
					ids="{{ implode(',', $article_sub_ids) }}">Marked All Read</button>
				@endif 
			@endif 
			<!--</div>-->
		</div>

		<!-- audio -->
		<audio id="audio" style="position: fixed; float: right" autoplay src=""></audio>

	</div>
</div>

<script src="/js/social-share.js"></script>
<script src="/js/qrcode.js"></script>
@endsection