@extends('layouts.app')



@section('content')

<script type="text/javascript">
$(document).ready(function () {

	$("#check_url").unbind("click").click(function(){
		$("#processTips").text("处理中");
		
		url = $("#url").val();
		$.get("{{ url('feed/checkFeedUrl') }}",{url:url},function(result){
			result_arr = JSON.parse(result);
			if(result_arr.code != 9999){
				alert('该url未检测到内容，请确认！');
			}
			$("#feed_name").val(result_arr.result.title);
			$("#processTips").text("处理完成");
		});
	});

	$(".delete_feed").click(function(){
		feed_value = $(this).attr("feed_value");
		feed_token = $(this).attr("feed_token");
		feed_type = $(this).attr("feed_type");

		if (feed_type == 'delete' && !confirm("确认要删除此订阅咩？")) {
			return false;
		}
		
		$.ajax({
		    url: "{{ url('feed') }}"+"/"+feed_value,
		    type: 'DELETE',
		    data: {type:feed_type,_token:feed_token},
		    success: function(result) {
		    	result_arr = JSON.parse(result);
				if(result_arr.code != 9999){
					alert('处理失败，请稍后再试');
				} else {
					$('#'+feed_value).remove();
				}
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
                    	新的订阅
                    	<div style="float:right">
                    		[<a href="{{ url('feeds/weiborss') }}">微博订阅</a>]
                    		[<a href="{{ url('feeds/opml') }}">opml导入</a>]
                    		[<a href="{{ url('feeds/weixinrss') }}">公众号订阅</a>]
                    		[<a href="{{ url('categorys') }}">分类设置</a>]
                    	</div>
                </div>

                <div class="card-body">
                    <!-- Display Validation Errors -->
                    @include('common.errors')

                    <!-- New Task Form -->
                    <form action="{{ url('feed') }}" method="POST" class="form-horizontal">
                        {{ csrf_field() }}

                        <!-- Task Name -->
                        <div class="form-group row">
                            <label for="task-name" class="col-md-3 control-label">订阅地址</label>
								
                            <div class="col-md-8">
	                                <input type="text" name="url" id="url" class="form-control" value="{{ $url }}">
	                                <a href="javascript:void(0)" id="check_url">检测地址!</a><span id="processTips"></span>
                            </div>
                        </div>
                        
                        <div class="form-group row" id="task_form_div1" >
                            <label for="task-name" class="col-md-3 control-label">订阅名称</label>
                            
                            <div class="col-md-8">
                            	<input type="text" name="feed_name" id="feed_name" class="form-control" value="{{  $title }}">
                            </div>
							
                        </div>
                        
                        <div class="form-group row" "form-group row" id="task_form_div4" >
                            <label for="task-name" class="col-md-3 control-label">所属分类</label>

                            <div class="col-md-6">
                            	@if(count($categorys) == 0)
                            	所有订阅必须有分类，您当前尚未建立分类，请前往建立后再新增订阅！[<a href="{{ url('categorys') }}" target="_blank">分类设置</a>]
                            	@else
	                            <select class="form-control" name="category_id">
		                              @foreach ($categorys as $category)
									  	<option value="{{ $category->id }}">{{ $category->name }}</option>
									  @endforeach
								</select>
								@endif
                            </div>
                        </div>

                        <!-- Add Task Button -->
                        <div class="form-group row">
                            <div class="col-md-offset-3 col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-plus"></i>提交！
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    
                    @if (count($feedSubs) > 0)
                    <table class="table table-striped task-table">
                            <thead>
                                <th>订阅列表</th>
                                <th>&nbsp;</th>
                            </thead>
                            <tbody>
                                @foreach ($feedSubs as $feedSub)
                                	@if(!empty($feedSub->feed))
                                    <tr id="{{$feedSub->id}}">
                                        <td class="table-text"  width="90%">
                                        	<div class="preprepre">
                                        	
                                        	<a href="{{ $feedSub->feed->url }}" title="{{ $feedSub->feed->feed_desc }}">{{ $feedSub->feed->feed_name }}</a>
                                        	
                                        	</pre>
                                        </td>

										<td  width="1"  align='right'>
                                            <a href="{{ url('feed/'.$feedSub->id)}}" style="color:blue"><img alt=""     style="width: 15px;" src="/img/icon/edit.png"></span>
                                        
                                        	<a href="javascript:void(0)" class="delete_feed" task_type="delete" feed_value="{{ $feedSub->id }}" feed_token="{{ csrf_token() }}"  style="cursor:pointer;">
                                        		<img alt=""     style="width: 15px;" src="/img/icon/delete.png">
                                        	</a> 
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                         {!! $feedSubs->links() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
