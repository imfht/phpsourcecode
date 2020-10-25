@extends('layouts.app')

@section('title', '思维导图 - 蒙太奇')
@section('description', '蒙太奇思维导图这里通过思维导图来总结你的每一个想法，发散思维，认真思考每一个想法！')


@section('content')
    <div class="container">
    
        <div class=" col-md-12">
            <div class="card">
                <div class="card-header">
               		 思维导图
                </div>

                <div class="card-body">
                	<div style="margin-bottom: 30px;margin-top: 10px;">
                		<p>
                			<b>思维导图</b>是蒙太奇的一项子栏目，这里通过思维导图来总结你的每一个想法，发散思维，认真思考每一个想法！<a rel="nofollow" href="{{url('/minds')}}">马上去体验！</a>
                		</p>
                	</div>
					<img alt="" src="/img/mind.png" class="col-md-offset-1 col-md-10">
                </div>
            </div>

        </div>
    </div>
@endsection
