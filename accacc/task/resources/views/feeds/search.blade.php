@extends('layouts.app')

@section('content')
<style>
	      
	      .rowone{
	        overflow: hidden;
		    text-overflow: ellipsis;
		    display: -webkit-box;
		    -webkit-box-orient: vertical;
		    -webkit-line-clamp: 1;
	      }
	      
	      .rowtwo{
	        overflow: hidden;
		    text-overflow: ellipsis;
		    display: -webkit-box;
		    -webkit-box-orient: vertical;
		    -webkit-line-clamp: 1;
	      }
</style>

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
	
});
</script>
    <div class="container">
    
        <div class=" col-md-12">
        	@include('common.success')
            <div class="card">
                <div class="card-header">
                    	发现新的订阅源
                    	<div style="float:right">
                    		[<a href="{{ url('feeds/explorer') }}">返回发现</a>]
                    		[<a href="{{ url('articles') }}">返回阅读</a>]
                    	</div>
                </div>

                <div class="card-body">
                    <!-- Display Validation Errors -->
                    @include('common.errors')
                    
                    @if (count($feeds) > 0)
                    
                    	<div class="row">
		                    @foreach ($feeds as $feed)
			                    <div class="col-md-4" style="padding:10px">
			                    	<div class="card card-block">
								      <b class="card-title rowone"> 
								      	<img alt="" style="width: 15px;" src="{{ $feed->favicon }}">
				                    	<a href="{{ url('article/list') }}?feed_id={{$feed->id}}" >{{ $feed->feed_name }}</a>
				                      </b>
								      <p class="card-text rowtwo">{{ $feed->feed_desc }} &nbsp;</p>
  									  <a class="card-link text-right" href="javascript:void(0)" feed_id="{{ $feed->id }}" class="feed_quick_sub">直接订阅</a>
								    </div>
			                    </div>
		                    @endforeach
                    	</div>
                    	<br>
                        {!! $feeds->links() !!}
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
