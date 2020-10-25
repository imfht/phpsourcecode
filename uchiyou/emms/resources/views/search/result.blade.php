@extends('layouts.adminFrame')
@section('content')
<div class="container">
	<div class="ibox-content">
		<table class="table">
			<!--  $materials 的值有问题 -->
			<h3>为您找到如下物资记录 ：</h3>
		
			<thead>
				<tr>
					<th>记录条目</th>
					<th>图片</th>
					<th>物资名称</th>
					<th>物资编号</th>
					<th>状态</th>
					<th>类别</th>
					<th>描述</th>
					<th>操作</th>
				</tr>
			</thead>
			@foreach ($materials as $material)
			<tr>
				<td>记录 {{$loop->index}}</td>
				<td>
				 @if(isset($material['picture_url']))
					<img alt="" src="/picture/download/{{ $material['picture_url'] }}"
					 data-action="zoom" class="stayShape" height="50" width="50">
				@else
					暂无图片
				@endif
				</td>
				<td>
				<a href="/admin/material/{!! $material['id'] !!}/show">
				{!! $material['name'] !!}
				</a>
				</td>
				<td>{!! $material['material_number'] !!}</td>
				<td>
				
				@if($material['status'] == 1)
					可用
				@elseif($material['status'] == 2)
					已借出
				@elseif($material['status'] ==3 )
					故障中
				@elseif($material['status'] ==4 )
					即将报废
				@endif
				
				</td>
				<td>{!! $material['type'] !!}</td>
				<td>{!! $material['description'] !!}</td>
				<td>
					<a href="/admin/material/{!! $material['id'] !!}/show">详情</a>
				</td>
			</tr>
			@endforeach
		</table>
	</div>
</div>
@endsection
