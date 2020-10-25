@extends('layouts.app') @section('title', '蒙太奇 - 但行好事，用心生活')
@section('description',
'利用番茄工作法结合待办列表来高效完成每一件事，实时统计，笔记记录，RSS阅读，思维导图，订阅推送到kindle来帮助你记录更多想法，希望它可以帮你更多')

@section('content')
<div class="container">
	<div class="card">
		<div class="card-header">
			待办四象限
			<div style="float: right">
				<a href="{{'/index'}}">[返回]</a>
			</div>
		</div>

		<div class="card-body">

			<div class="row">
				<div class="col-md-6">
					<div class="">
						<h4>不重要不紧急事项</h4>
						@if(empty($tasks[1]))
							<p>暂无待办</p>
						@else
							@foreach ($tasks[1] as $task)
							<p>{{$task->name}}</p>
							@endforeach
						@endif
					</div>
				</div>

				<div class="col-md-6">
					<div class="">
						<h4>不重要紧急事项</h4>
						@if(empty($tasks[3]))
							<p>暂无待办</p>
						@else
							@foreach ($tasks[3] as $task)
							<p>{{$task->name}}</p>
							@endforeach
						@endif
					</div>
				</div>

				<div class="col-md-6">
					<div class="">
						<h4>重要不紧急事项</h4>
						@if(empty($tasks[3]))
							<p>暂无待办</p>
						@else
							@foreach ($tasks[3] as $task)
							<p>{{$task->name}}</p>
							@endforeach
						@endif
					</div>

				</div>
				<div class="col-md-6">
					<div class="">
						<h4>重要紧急事项</h4>
						@if(empty($tasks[4]))
							<p>暂无待办</p>
						@else
							@foreach ($tasks[4] as $task)
							<p>{{$task->name}}</p>
							@endforeach
						@endif
					</div>
				</div>

			</div>
		</div>

	</div>
	<!-- /container -->
	@endsection