@extends('layouts.app')

@section('content')
    <div class="container">
    		@include('common.success')
            <!-- Current Tasks -->
                <div class="card">
                    <div class="card-header">
                        	待办列表
                        	<div style="float:right">
                    		<a href="{{'/tasks'}}?status=1&need_page=1">[进行中]</a>
                    		<a href="{{'/tasks'}}?status=2&need_page=1">[已完成]</a>
                    		<a href="{{'/tasks'}}?status=3&need_page=1">[已折叠]</a>
                    		<a href="{{'/index'}}">[返回]</a>
                    	</div>
                    </div>

                    <div class="card-body">
                    	
			            @if (count($tasks) > 0)
                        <table class="table table-striped task-table">
                            <thead>
                                <th>待办事项</th>
                                <th>最后时间</th>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                    <tr
                                    	@if($task->priority == 4) 
	                                    	class="danger" title="重要紧急事项" 
	                                    @elseif($task->priority == 3) 
	                                    	class="warning" title="重要不紧急事项"  
	                                    @elseif($task->priority == 2) 
	                                    	class="info" title="不重要紧急事项" 
	                                    @else
	                                    	title="不重要不紧急事项" 
	                                    @endif
                                    >
                                        <td class="table-text"  width="80%">
                                        	<div>
                                        		<a href="/task/{{ $task->id }}">[更新]</a>
                                        		<a href="/notes?add_content=%23记录待办%23{{ urlencode($task->name)}}&task_id={{$task->id}}">[记录]</a>
                                        		@if($task->status == 1) 
			                                    	[进行中]
			                                    @elseif($task->status == 2) 
			                                    	[已完成]
			                                    @elseif($task->status == 3) 
			                                    	[已折叠]
			                                    @endif
                                        		{{ $task->name }}
                                        		
                                        		@if(isset($task->parentTask->name))
                                        			#{{ $task->parentTask->name}}#
                                        		@endif
                                        		@if($task->mode == 1)
                                        			#work#
                                        		@else 
                                        			#life#
                                        		@endif
                                        	</div>
                                        </td>
                                        
                                        <td  width="20%" align="right">
                                        	{{ date('y-m-d H:i', strtotime($task->updated_at)) }}
                                        </td>

                                        <!-- Task Delete Button -->
                                        <!-- 
                                        <td  width="20%" align="right">
                                            <form action="{{url('task/' . $task->id)}}" method="POST">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}

                                                <button type="submit" id="delete-task-{{ $task->id }}" class="btn btn-danger">
                                                    <i class="fa fa-btn fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                         -->
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                         {!! $tasks->links() !!}
                        @else
                    	暂时还没有完成哦，快去<a href="{{url('/index')}}">开始第一个任务</a>吧！
			            @endif
                    </div>
                </div>
        </div>
    </div>
@endsection
