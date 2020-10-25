@extends('layouts.app')

@section('title', '去阅读 - 蒙太奇')
@section('description', '蒙太奇去阅读这里支持你订阅各大个人博客、科技媒体，甚至都可以定时发送您订阅的文章到kindle阅读器，每天回家打开kindle即可享受阅读好时光！')


@section('content')
    <div class="container">
    
        <div class=" col-md-12">
            <div class="card">
                <div class="card-header">
               		 去阅读
                </div>

                <div class="card-body">
                	<div style="margin-bottom: 30px;margin-top: 10px;">
                		<p>
                			<b>去阅读</b>是蒙太奇的一项子栏目，这里支持你订阅各大个人博客、科技媒体，甚至都可以定时发送您订阅的文章到kindle阅读器，每天回家打开kindle即可享受阅读好时光！<a rel="nofollow" href="{{url('/articles')}}">马上去体验！</a>
                		</p>
                	</div>
					<img alt="" src="/img/read.png" class=" mx-auto col-md-10">
                </div>
            </div>

        </div>
    </div>
@endsection
