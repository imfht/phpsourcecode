@extends('layouts.app')

@section('title', '做番茄 - 蒙太奇')
@section('description', '蒙太奇做番茄这里通过番茄工作法合理的安排工作与休息，极大提高你的工作效率，另外这里有完善的待办管理，你可以定义优先级，还可以设置deadline、设置提醒时间，让你每个任务都不落下，每个任务都顺利完成！')


@section('content')
    <div class="container">
    
        <div class=" col-md-12">
            <div class="card">
                <div class="card-header">
               		 做番茄
                </div>

                <div class="card-body">
                	<div style="margin-bottom: 30px;margin-top: 10px;">
                		<p>
                			<b>做番茄</b>是蒙太奇的一项子栏目，这里通过番茄工作法合理的安排工作与休息，极大提高你的工作效率，另外这里有完善的待办管理，你可以定义优先级，还可以设置deadline、设置提醒时间，让你每个任务都不落下，每个任务都顺利完成！<a rel="nofollow" href="{{url('/index')}}">马上去体验！</a>
                		</p>
                	</div>
					<img alt="" src="/img/pomo.png" class="col-md-offset-1 col-md-10">
                </div>
            </div>

        </div>
    </div>
@endsection
