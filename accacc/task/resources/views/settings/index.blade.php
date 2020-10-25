@extends('layouts.app')

@section('content')
<script type="text/javascript">
$(document).ready(function () {

	$("#check_url").click(function(){
		
	});
});
</script>
    <div class="container">
    
        <div class="col-md-12">
            @include('common.success')
            <div class="card">
            	 
                <div class="card-header">
                    	设置
                </div>

                <div class="card-body">
                    <!-- Display Validation Errors -->
                    @include('common.errors')

                    <!-- New Task Form -->
                    <form action="{{ url('setting/'.$setting->id) }}" method="POST" class="form-horizontal">
                        {{ csrf_field() }}

                        <!-- Task Name -->
                        <div class="form-group row">
                            <label for="task-name" class="col-md-3 control-label">日目标</label>
								
                            <div class="col-md-8">
	                                <input type="text" name="day_pomo_goal" id="day_pomo_goal" class="form-control" value="{{ empty($setting->day_pomo_goal)?8:$setting->day_pomo_goal }}">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="task-name" class="col-md-3 control-label">周目标</label>
								
                            <div class="col-md-8">
	                                <input type="text" name="week_pomo_goal" id="week_pomo_goal" class="form-control" value="{{ empty($setting->week_pomo_goal)?40:$setting->week_pomo_goal }}">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="task-name" class="col-md-3 control-label">月目标</label>
								
                            <div class="col-md-8">
	                                <input type="text" name="month_pomo_goal" id="month_pomo_goal" class="form-control" value="{{ empty($setting->month_pomo_goal)?160:$setting->month_pomo_goal }}">
                            </div>
                        </div>
                        
                        <div class="form-group row" id="task_form_div1" >
                            <label for="task-name" class="col-md-3 control-label">番茄时间(10-60min 标准:25min)</label>
                            
                            <div class="col-md-8">
                            	<input type="text" name="pomo_time" id="pomo_time" class="form-control" value="{{ empty($setting->pomo_time)?25:$setting->pomo_time }}">
                            </div>
							
                        </div>
                        
                        <div class="form-group row" id="task_form_div1" >
                            <label for="task-name" class="col-md-3 control-label">番茄休息时间(1-10min 标准:5min)</label>
                            
                            <div class="col-md-8">
                            	<input type="text" name="pomo_rest_time" id="pomo_rest_time" class="form-control" value="{{ empty($setting->pomo_rest_time)?5:$setting->pomo_rest_time }}">
                            </div>
							
                        </div>
                        
                        <div class="form-group row" id="task_form_div1" >
                            <label for="task-name" class="col-md-3 control-label">Kindle订阅地址</label>
                            
                            <div class="col-md-8">
                            	<input type="text" name="kindle_email" id="kindle_email" class="form-control" value="{{ $setting->kindle_email }}">
                            </div>
							
                        </div>
                        
                        <div class="form-group row" id="task_form_div1" >
                            <label for="task-name" class="col-md-3 control-label">Ifttt通知Key</label>
                            
                            <div class="col-md-8">
                            	<input type="text" name="ifttt_notify" id="ifttt_notify" class="form-control" value="{{ $setting->ifttt_notify }}">
                            </div>
							
                        </div>
                        
                        <div class="form-group row" id="task_form_div1" >
                            <label for="task-name" class="col-md-3 control-label">是否开启推送</label>
                            
							<label class="radio-inline">
								  <input type="radio" name="is_start_kindle" id="inlineRadio1" value="0" {{ empty($setting->is_start_kindle) ?'checked':'' }}><span>不开启</span>
								</label>
								<label class="radio-inline">
								  <input type="radio" name="is_start_kindle" id="inlineRadio2" value="1" {{ $setting->is_start_kindle == 1 ?'checked':'' }}><span>开启</span>
								</label>
                        </div>
                        <div class="form-group row" id="task_form_div1" >
                            <label for="task-name" class="col-md-3 control-label">是否带图推送</label>
                            
							<label class="radio-inline">
								  <input type="radio" name="with_image_push" id="inlineRadio1" value="0" {{ empty($setting->with_image_push) ?'checked':'' }}><span>不开启</span>
								</label>
								<label class="radio-inline">
								  <input type="radio" name="with_image_push" id="inlineRadio2" value="1" {{ $setting->with_image_push == 1 ?'checked':'' }}><span>开启</span>
								</label>
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
