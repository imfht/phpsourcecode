@extends('layouts.app')

@section('content')
    <div class="container">
            <!-- Current Goals -->
                <div class="card">
                    <div class="card-header">
                        	修改目标
                        	<div style="float:right">
	                    		<a href="{{'/goals'}}">[返回]</a>
	                    	</div>
                    </div>

                    <div class="card-body">
                    <!-- Display Validation Errors -->
                    @include('common.errors')

                    <!-- New Task Form -->
                    <form action="{{ url('goal/'.$goal->id) }}" method="POST" class="form-horizontal">
                        {{ csrf_field() }}

                        <!-- Task Name -->
                        <div class="form-group row">
                            <label for="goal-name" class="col-md-3 control-label">目标名称:</label>

                            <div class="col-md-8">
	                                <input type="text" name="name" id="goal-name" class="form-control" value="{{ $goal->name }}">
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
