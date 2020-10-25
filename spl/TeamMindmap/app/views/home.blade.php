@extends('layout.master')


{{--add css file here--}}
@section('styles')
{{-- add fullpage css --}}
{{HTML::style('css/home-style.css')}}
@stop

{{--add script file here--}}
@section('scripts')
{{-- add require main script --}}
{{HTML::script('packages/bower/requirejs/require.js',array('data-main'=>'../js/home-main.js'))}}

@stop

@section("title")
主页
@stop

@section("content")
    <div class="container">
        <div id="main-jumbotron" class="jumbotron">
                <div class="row">
                    <div class="col col-lg-7 col-md-7 col-sm-7">
                        {{HTML::image('image/mac.png','mac image',array('class'=>'jumbotron-image'))}}

                    </div>

                    <div class="col col-lg-5 col-md-5 col-sm-5">
                        <h1 class="text-center">简单绘图<br/>协作思维</h1>
                        <center><a href="{{ URL::to('authority/signin') }}" class="btn btn-primary btn-lg">立即体验</a></center>
                    </div>

                </div>
            </div>
    </div>


    <div id="feature" class="container">
        <div class="row">
            <div id="mindmap" class="col col-lg-4 col-md-4 col-sm-4 feature-part">
                <div class="feature-box">
                    {{HTML::image('image/mindmap.png','mindmap image',array('class'=>'feature-image'))}}
                </div>
                <h2>基于思维导图</h2>
                <p>心智图是使用一个中央关键词或想法引起形象化的构造和分类的想法;它用一个中央关键词或想法以辐射线形连接所有的代表字词、想法、任务或其它关联项目的图解方式。它可以利用不同的方式去表现人们的想法，如引题式，可见形象化式，建构系统式和分类式。它是普遍地用作在研究、组织、解决问题和政策制定中。</p>
            </div>
            <div id="cooperation" class="col col-lg-4 col-md-4 col-sm-4 feature-part">
                <div class="feature-box">
                    <i class="fa fa-group feature-fa"></i>
                </div>
                <h2>项目协作</h2>
                <p>心智图是使用一个中央关键词或想法引起形象化的构造和分类的想法;它用一个中央关键词或想法以辐射线形连接所有的代表字词、想法、任务或其它关联项目的图解方式。它可以利用不同的方式去表现人们的想法，如引题式，可见形象化式，建构系统式和分类式。它是普遍地用作在研究、组织、解决问题和政策制定中。</p>
            </div>
            <div id="tasks" class="col col-lg-4 col-md-4 col-sm-4 feature-part">
                <div class="feature-box">
                    <i class="fa fa-tasks feature-fa"></i>
                </div>
                <h2>简单任务</h2>
                <p>心智图是使用一个中央关键词或想法引起形象化的构造和分类的想法;它用一个中央关键词或想法以辐射线形连接所有的代表字词、想法、任务或其它关联项目的图解方式。它可以利用不同的方式去表现人们的想法，如引题式，可见形象化式，建构系统式和分类式。它是普遍地用作在研究、组织、解决问题和政策制定中。</p>

            </div>
        </div>
    </div>

@stop
