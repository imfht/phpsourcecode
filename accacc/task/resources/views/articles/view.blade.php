@extends('layouts.app')

@section('title', $article->subject.' - 蒙太奇')

@section('content')
<link rel="stylesheet" href="/css/share.min.css">
<style>
		  
		  .post-text{
			padding: 10px;
			line-height: 1.8;
			//font-size: 20px;
		  }
		  
		  img{
			      max-width: 75%;
		  }
		  
		  #mark{position: absolute;display:none}
</style>
<script src="/js/lazyload.min.js"></script>
<script type="text/javascript" charset="utf-8">
  $(function() {
	//lazy img
	$("img.lazy").lazyload();
  });
</script>
<script type="text/javascript">
$(document).ready(function () {

	$(".feed_quick_sub").click(function(){
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
	
	$(".icon-heart").click(function(){
		console.log(123);
		var title = $(this).attr('data-title');
		var url = $(this).attr('data-url');
		location.href='/notes?add_content='+url;
	});
	
	var img = document.getElementById("mark")
	
	$(document).mouseup(function(ev){//设定一个onmouseup事件
		
		   var ev = ev || window.event;
		
		   var scrollX = document.documentElement.scrollLeft || document.body.scrollLeft;
           var scrollY = document.documentElement.scrollTop || document.body.scrollTop;
		   
		   var pagex = ev.pageX||ev.clientX+scrollLeft();
           var pagey = ev.pageY||ev.clientY+scrollTop();

           var left = pagex - img.offsetWidth/2+"px";
           var top = pagey - img.offsetHeight/2+"px"
		
			if(selectText().length>10){
				setTimeout(function(){//设定一个定时器
					//$("#mark").css('display','block'); 
					//$("#mark").css('left', left + 'px'); 
					//$("#mark").css('top',top + 'px'); 
					img.style.display="block";//鼠标的坐标-图片的坐标除2那就使图片中间
					img.style.left=pagex-img.offsetWidth/2+"px";//鼠标的坐标-图片的坐标除2那就使图片中间
					img.style.top=pagey-img.offsetHeight/2+"px"//鼠标的坐标-图片的坐标除2那就使图片中间
				},100);
			}
		else{
			$("#mark").css('display','none'); 
		}
	});
	
	$('.card-text').click(function(ev){
		var ev = ev || window.event;
		ev.cancelBubble = true;
	});
	
	$(document).click(function(){
		$("#mark").css('display','none'); 
	});

	$("#mark").click(function(){
		$.post("{{ url('/article/mark') }}",{"article_id":{{ $article->id }},content:selectText(),"_token":"{{ csrf_token() }}"},function(result){
			result_arr = JSON.parse(result);
			if(result_arr.code != 9999){
				alert(result_arr.msg);
			} else {
				alert(result_arr.msg);
				$("#mark").css('display','none'); 
			}
		});
	});
	
});

function selectText(){
		if(document.selection){ //IE浏览器下
			return document.selection.createRange().text;//返回选中的文字
		}
		else{  //非IE浏览器下
			return window.getSelection().toString();//返回选中的文字
		}
	}

/**
window.onload=function(){
            //需求：鼠标在文档上移动，小天使就跟随移动;
            //思路：获取鼠标在页面上的坐标，赋值给小天使，用鼠标移动事件;
            //有了鼠标移动事件(onmousemove)，一般会替代定时器一部分功能；

            var img = document.getElementById("mark")

            document.onmousemove=function(event){
                var event=event||window.event//获取鼠标坐标  兼容写法；
                var pagex=event.pageX||event.clientX+scrollLeft();
                var pagey=event.pageY||event.clientY+scrollTop();
				
				console.log(pagex + ' ' + pagey);

                img.style.display="block";//鼠标的坐标-图片的坐标除2那就使图片中间
                img.style.left=pagex-img.offsetWidth/2+"px";//鼠标的坐标-图片的坐标除2那就使图片中间
                img.style.top=pagey-img.offsetHeight/2+"px"//鼠标的坐标-图片的坐标除2那就使图片中间

            }
        }
*/
</script>
	
	
    <div class="container">
    
        <div class=" mx-auto col-md-8">
            <div class="card">
                <div class="card-header">
                	<!-- 
                	
                		<span style="color: yellow">
                    	<a href="{{ url('article/star/'.$article->id) }}">★</a>
                    	<a href="{{ url('article/star/'.$article->id) }}">☆</a>
                    	</span>
                    	<a href="{{ $article->url }}" target="">
                    		全文
                    	</a>
                     -->
                     <a href="{{ url('article/list') }}?feed_id={{$article->feed->id}}"> 
                     {{$article->feed->feed_name}}
                     </a>
                    	<div style="float:right">
							<a href="#share" >[分享]</a>
                    		@if(!$is_feed)
                    		<a href="javascript:void(0)" feed_id="{{ $article->feed->id }}" class="feed_quick_sub">[添加订阅]</a>
                    		@endif
                    		<a href="{{'/articles'}}">[继续阅读]</a>
                    	</div>
                </div>
                
                <div class="card" style="margin-bottom:10px">
					<div class="card-block" style="padding: 10px;">
					  @if(!empty($article->subject))
					  <h4 class="card-title">
							<a href="{{ $article->url }}">[原文]</a>
							{{ $article->subject }}
					  </h4>
				      @endif
					  <p class="card-text"><small class="text-muted">来源：<a href="{{ App\Http\Utils\CommonUtil::hostUrl($article->feed->url) }}" target="_blank">{{ $article->feed->feed_name}}</a> * {{$article->published}}</small></p>
					  
					  <div class="card-text post-text" id="card-text">
						<?php echo App\Http\Utils\CommonUtil::formatContentHtml($article->content); ?>
					  </div>
					  
					  

					  <p class="card-text text-right">
						  <a id="share" name="share"></a>
						  <div class="social-share" style="float:right" data-mode="prepend" data-weibo-title="{{ $article->subject }}" data-weibo-appKey="567683707" data-weibo-ralateUid="1671353227" data-title="{{ $article->subject }}" data-url="http://{{$_SERVER['SERVER_NAME']}}/article/view/{{$article->id}}" data-image="{{ $article->image_url }}" data-sites="facebook,twitter,google,wechat,weibo"  data-mobile-sites="facebook,twitter,google,wechat,weibo"  data-wechat-qrcode-title="请打开微信扫一扫">
							<a href="javascript:void(0);" class="social-share-icon icon-heart" class="" data-title="{{ $article->subject }} From:http://task.congcong.us/article/view/{{$article->id}}" data-url="http://{{$_SERVER['SERVER_NAME']}}/article/view/{{$article->id}}"></a>
						  </div>
					  </p>
					</div>
				  </div>
				  
				 

            </div>

        </div>
		<!-- 标注 -->
						<img src="/img/icon/mark.png" width="30px" alt="mark" id="mark">
		
    </div>
	<script src="/js/social-share.js"></script>
	<script src="/js/qrcode.js"></script>
@endsection
