@extends('layouts.app')

@section('content')
    <div class="container">
    
        <div class="col-md-offset-2 col-md-8">
            <div class="card">
                <div class="card-header">
                    	修改分类
                </div>

                <div class="card-body">
                    <!-- Display Validation Errors -->
                    @include('common.errors')

                    <!-- New Task Form -->
                    <form action="{{ url('category/'.$category->id) }}" method="POST" class="form-horizontal">
                        {{ csrf_field() }}

                        <!-- Task Name -->
                        <div class="form-group row">
                            <label for="task-name" class="col-md-3 control-label">分类名称</label>
								
                            <div class="col-md-8">
	                               <input type="text" name="name" id="name" class="form-control" value="{{ $category->name }}">
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
