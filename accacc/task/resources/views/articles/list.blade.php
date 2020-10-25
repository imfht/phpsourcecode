@extends('layouts.app')

@section('content')
<style>

		  .post-text{
			padding: 10px;
			font-size: 20px;
		  }

		  img{
			      max-width: 75%;
		  }
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

	$(".post-text").each(function(){
		height=$(this).height();
		if(height > 1000) {
			$(this).css("height","360");
			$(this).css("overflow","hidden");
			$(this).after("<p class=\"morecon\" style=\"align-text: right;text-align: right; color: #337ab7; cursor:pointer; font-size: 2em; \">click for more content...</p>");
			//$(this).parent().find(".view-all").css("display","");
		}
	});

	$(".morecon").click(function(){
		$(this).parent().children("div.post-text").css("height","auto");
		$(this).css("display","none");
		//$(this).parent().parent().find(".view-all").css("display","none");
	});

	$(".post-text").each(function(){
		height=$(this).height();
		if(height > 1000) {
			$(this).css("height","360");
			$(this).css("overflow","hidden");
			$(this).parent().find(".view-all").css("display","");
		}
	});

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
});
</script>
    <div class="container">

        <div class=" col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    	{{ $feed->feed_name }}

                    	<div style="float:right">
                    		[<a href="javascript:void(0)"  feed_id="{{ $feed->id }}" class="feed_quick_sub">订阅此源</a>]
                    	</div>
                </div>

                    <!-- Display Validation Errors -->
                    @include('common.errors')

                    		@if (count($articles) > 0)
	                    		@foreach ($articles as $article)
								<div class="card" style="margin-bottom:10px">
									<div class="card-block" style="padding: 10px;">
									  <h4 class="card-title">
											<a href="{{ $article->url }}">[原文]</a>
											<a href="{{ url('article/view/'.$article->id) }}">{{ $article->subject }}</a>
									  </h4>
									  <p class="card-text"><small class="text-muted">来源：<a href="{{ App\Http\Utils\CommonUtil::hostUrl($article->feed->url) }}" target="_blank">{{ $article->feed->feed_name}}</a> * {{$article->published}}</small></p>

									  <div class="card-text post-text">
										<?php echo App\Http\Utils\CommonUtil::formatContentHtml($article->content); ?>
									</div>
								  </div>
								</div>
								@endforeach
                        		{!! $articles->appends($page_params)->links() !!}
                        @else
                        @endif

            </div>

        </div>
    </div>
@endsection