@extends('layouts.app')

@section('content')
<script type="text/javascript">
$(document).ready(function () {

	$("#check_url").click(function(){
		url = $("#url").val();
		$.get("{{ url('feed/checkFeedUrl') }}",{url:url},function(result){
			result_arr = JSON.parse(result);
			if(result_arr.code != 9999){
				alert('该url未检测到内容，请确认！');
			} else {
				alert('检测成功');
			}
			$("#feed_name").val(result_arr.result.title);
		});
	});
});
</script>
    <div class="container">
    
        <div class="col-md-offset-2 col-md-8">
            <div class="card">
                <div class="card-header">
                    	修改订阅
                    	<div style="float:right">
                    		[<a href="{{ url('categorys') }}" target="_blank">分类设置</a>]
                    	</div>
                </div>

                <div class="card-body">
                    <!-- Display Validation Errors -->
                    @include('common.errors')

                    <!-- New Task Form -->
                    <form action="{{ url('feed/'.$feedSub->id) }}" method="POST" class="form-horizontal">
                        {{ csrf_field() }}

                        <div class="form-group row" id="task_form_div1" >
                            <label for="task-name" class="col-md-3 control-label">订阅名称</label>
                            
                            <div class="col-md-8">
                            	<input type="text" name="feed_name" id="feed_name" class="form-control" value="{{ $feedSub->feed_name }}">
                            </div>
							
                        </div>
                        
                        <div class="form-group row">
                            <label for="task-name" class="col-md-3 control-label">订阅排序</label>
								
                            <div class="col-md-8">
	                               <input type="text" name="feed_order" id="feed_order" class="form-control" value="{{ $feedSub->feed_order }}">
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
									  	<option value="{{ $category->id }}" @if($feedSub->category_id == $category->id) checked @endif>{{ $category->name }}</option>
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
                    
                    
                </div>
            </div>

        </div>
    </div>
@endsection
