@extends('layouts.app')

@section('title', '蒙太奇 - 但行好事，用心生活')
@section('description', '利用番茄工作法结合待办列表来高效完成每一件事，实时统计，笔记记录，RSS阅读，思维导图，订阅推送到kindle来帮助你记录更多想法，希望它可以帮你更多')

@section('content')
 <div class="container">
      <div class="jumbotron text-center" style="color: white;    background-position: -1501px -266px;    background-image: url('/img/index_background.jpg');">
        <h3 style="padding-bottom: 20px;">蒙太奇 - 但行好事，用心生活</h3>
        <p class="lead"  style="padding-bottom: 100px;">不止于GTD，陪伴你做每一件事，绘制更多自己的精彩瞬间。</p>
        <p>
	        <a class="btn btn-lg btn-primary" href="{{url('/login')}}" role="button">现在就开始吧</a>
        </p>
      </div>
      <!-- 
      <div class="row">
        <div class="col-md-3">
          <img alt="" src="/img/time-is-money.png" class="">
          <div class="">
	        <h4>番茄工作法</h4>
	        <p>基于番茄工作法，帮助你集中注意力完成每一项待办。试过就知道这是有效的时间管理方法。</p>
          </div>
        </div>
        
        <div class="col-md-3">
          <img alt="" src="/img/list.png" class="">
          <div class="">
	        <h4>待办清单</h4>
          	<p>轻量级的待办列表功能，同时通过特殊语法提供 #标签、四象限重要程度、快速置顶等功能。</p>
          </div>
         </div>
         
        <div class="col-md-3">
          <img alt="" src="/img/newspaper.png" class="">
          <div class="">
	        <h4>思想广场</h4>
          	<p>从收集想法、规划任务到专注工作、归纳分析，这里提供了完整的工作流效率管理。</p>
          </div>
          
         </div>
        <div class="col-md-3">
          <img alt="" src="/img/monitor.png" class="">
          <div class="">
	          <h4>RSS阅读</h4>
          	<p>汇总你的碎片化阅读，在这里高效完成对它的思考与记录！</p>
          </div>
        </div>
        
      </div>
       -->
	  
    </div> <!-- /container -->
@endsection
