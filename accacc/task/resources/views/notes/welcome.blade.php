@extends('layouts.app')

@section('title', '记想法 - 蒙太奇')
@section('description', '蒙太奇记想法这里支持通过插件快速分享chrome等浏览器所浏览的网站、图片与文字，同时可以你可以实时去记录你的想法，其更支持语音录入极大方便你的学习生活')


@section('content')
    <div class="container">
    
        <div class=" col-md-12">
            <div class="card">
                <div class="card-header">
               		 记想法
                </div>

                <div class="card-body">
                	<div style="margin-bottom: 30px;margin-top: 10px;">
                		<p>
                			<b>记想法</b>是蒙太奇的一项子栏目，这里支持通过插件快速分享chrome等浏览器所浏览的网站、图片与文字，同时可以你可以实时去记录你的想法，其更支持语音录入极大方便你的学习生活！<a rel="nofollow" href="{{url('/notes')}}">马上去体验！</a>
                		</p>
                	</div>
					<img alt="" src="/img/note.png" class="col-md-offset-1 col-md-10">
                </div>
            </div>

        </div>
    </div>
@endsection
