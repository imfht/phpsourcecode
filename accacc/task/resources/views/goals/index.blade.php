@extends('layouts.app')

@section('content')

<script type="text/javascript">
$(document).ready(function () {

	$(".delete_goal").click(function(){
		goal_value = $(this).attr("goal_value");
		goal_token = $(this).attr("goal_token");
		goal_type = $(this).attr("goal_type");

		if (goal_type == 'delete' && !confirm("确认要删除此目标咩？")) {
			return false;
		}
		
		$.ajax({
		    url: "{{ url('goal') }}"+"/"+goal_value,
		    type: 'DELETE',
		    data: {type:goal_type,_token:goal_token},
		    success: function(result) {
		    	result_arr = JSON.parse(result);
				if(result_arr.code != 9999){
					alert('处理失败，请稍后再试');
				} else {
					$('#'+goal_value).remove();
				}
		    }
		});
	});
});
</script>
    <div class="container">
            <!-- Current Goals -->
            	@include('common.success')
                <div class="card">
                    <div class="card-header">
                        	技能列表
                        	<div style="float:right">
	                    		<a href="{{'/index'}}">[返回]</a>
	                    	</div>
                    </div>

                    <div class="card-body">
                    <!-- Display Validation Errors -->
                    @include('common.errors')

                    <!-- New Task Form -->
                    <form action="{{ url('goal') }}" method="POST" class="form-horizontal">
                        {{ csrf_field() }}

                        <!-- Task Name -->
                        <div class="form-group row">
                            <label for="goal-name" class="col-md-3 control-label">技能名称:</label>

                            <div class="col-md-8">
	                                <input type="text" name="name" id="goal-name" class="form-control" value="{{ old('goal') }}">
                            </div>
                        </div>

                        <!-- Add Task Button -->
                        <div class="form-group row">
                            <div class="col-md-offset-3 col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-plus"></i>添加！
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    
                    @if (count($goals) > 0)
                    <table class="table table-striped goal-table">
                            <thead>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                            </thead>
                            <tbody>
                                @foreach ($goals as $goal)
                                    <tr id="{{$goal->id}}">
                                        <td class="table-text"  width="80%">
                                        	<div class="preprepre">
                                        	{{ $goal->name }}
                                        	</pre>
                                        </td>

                                        <td  width="10%"  align='right'>
                                        	<a href="{{ url('goal/'.$goal->id)}}" style="color:blue"><img alt=""     style="width: 15px;" src="/img/icon/edit.png"></a>
                                        	<a href="javascript:void(0)" class="delete_goal" task_type="delete" task_value="{{ $goal->id }}" goal_token="{{ csrf_token() }}"  style="cursor:pointer;">
                                        		<img alt=""     style="width: 15px;" src="/img/icon/delete.png">
                                        	</a> 
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                         {!! $goals->links() !!}
                    @endif
                </div>
                </div>
        </div>
    </div>
@endsection
