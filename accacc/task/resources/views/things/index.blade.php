@extends('layouts.app')
<script src="{{'/js/My97DatePicker/WdatePicker.js'}}"></script>

@section('content')
<script type="text/javascript">
$(document).ready(function () {

	$(".delete_thing").click(function(){
		thing_value = $(this).attr("thing_value");
		thing_token = $(this).attr("thing_token");
		thing_type = $(this).attr("thing_type");

		if (thing_type == 'delete' && !confirm("确认要删除此事情咩？")) {
			return false;
		}
		
		$.ajax({
		    url: "{{ url('thing') }}"+"/"+thing_value,
		    type: 'DELETE',
		    data: {type:thing_type,_token:thing_token},
		    success: function(result) {
		    	result_arr = JSON.parse(result);
				if(result_arr.code != 9999){
					alert(result_arr.msg);
				} else {
					$('#'+thing_value).remove();
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
                    	新增事情记录
                    	<div style="float:right">
                    		<a href="{{'/index'}}">[返回]</a>
                    	</div>
                </div>

                <div class="card-body">
                    <!-- Display Validation Errors -->
                    @include('common.errors')

                    <!-- New thing Form -->
                    <form action="{{ url('thing') }}" method="POST" class="form-horizontal">
                        {{ csrf_field() }}

                        <!-- thing Name -->
                        <div class="form-group row">
                            <label for="thing-name" class="col-md-3 control-label">完事内容</label>
								
                            <div class="col-md-8">
	                               <input type="text" name="name" id="name" class="form-control" value="{{ old('thing') }}">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="thing-name" class="col-md-3 control-label">完事时间</label>
								
                            <div class="col-md-8">
	                               <input type="text" name="start_time" id="task-remindtime" class="form-control" value="{{ date('Y-m-d H:i:00', strtotime("-15 minute"))}}" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',maxDate:'%y-%M-%d'})">
	                               -
	                               <input type="text" name="end_time" id="task-deadline" class="form-control" value="{{ date('Y-m-d H:i:00')}}" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',maxDate:'%y-%M-%d'})">
                            </div>
                        </div>

                        <!-- Add thing Button -->
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
            
            <div class="card">
                <div class="card-header">
                    	新完成事情记录
                </div>

                <div class="card-body">
                	@if (count($things) > 0)
                    
                    	<div>
		                    @foreach ($things as $thing)
			                    <div style="height:40px">
				                    <img alt=""     style="width: 15px;" src="/img/icon/thing{{ $thing->type }}.png">
				                    {{ $thing->name }}  
				                    <a href="{{ url('thing/'.$thing->id)}}" style="color:blue">
				                    	<img alt=""     style="width: 15px;" src="/img/icon/edit.png">
				                    </a>
				                    <a href="javascript:void(0)" class="delete_thing" thing_type="delete" thing_value="{{ $thing->id }}" thing_token="{{ csrf_token() }}"  style="cursor:pointer;">
				                    	<img alt=""     style="width: 15px;" src="/img/icon/delete.png">
				                    </a>
				                    <div style="float:right">
					                    {{ \App\Http\Utils\CommonUtil::formatTime($thing->start_time, $thing->end_time)}}
				                    </div>
			                    </div>
		                    @endforeach
                    	</div>
                    
                        {!! $things->links() !!}
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
