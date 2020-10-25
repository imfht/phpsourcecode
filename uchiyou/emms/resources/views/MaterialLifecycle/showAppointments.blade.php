@extends('layouts.adminFrame')
@section('content')
<div class="container">
	<div style="margin-bottom: 60px;">
		<h3>
			<a href="/admin/material/appointment/history/{{ $where }}/appointed"
				@if($type== 'appointed')
					style="font-size: 40px;"
				 @endif>
				 我的预约
			</a> 
			<a href="/admin/material/appointment/history/{{ $where }}/all"
				@if($type== 'all')
					style="font-size: 40px;"
				 @endif>
				 历史记录
			</a>
		</h3>
<?php
$url = '/admin/material/appointment/history/search/' . $where . '/' . $type;
?>
<div class="pull-right">
@include('elements/historySearchForm')
</div>
	</div>
{{ $appointmentRecords->links() }}
	<div class="table-responsive" id="print">
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th>记录号</th>
					<th>物资名称</th>
					<th>物资状态</th>
					<th>图片</th>
					<th>预约人</th>
					<th>电话</th>
					<th>编号</th>
					<th>类别</th>
					<th>预约时间</th>
					<th>预约状态</th>
					<th>操作</th>
				</tr>
			</thead>
			@foreach ($appointmentRecords as $record)
			<tr>
				<td>{{$loop->index}}</td>
				<td><a href="/admin/material/{{ $record->id }}/show"> {!!
						$record->name !!} </a></td>
				<td>@if($record->status == 1) 可用 @elseif($record->status ==2) 租用中
					@elseif($record->status ==3) 故障中 @elseif($record->status ==4) 即将报废
					@endif</td>
				<td>@if( isset($record->picture_url)) <img alt=""
					src="/picture/download/{{ $record->picture_url }}"
					class="stayShape" data-action="zoom" height="50" width="50"> @else
					暂无图片 @endif
				</td>
				<td>{!! $record->userName !!}</td>
				<td>{!! $record->phone !!}</td>
				<td>{!! $record->material_number !!}</td>
				<td>{!! $record->type !!}</td>
				<td>{!! $record->start_time !!}</td>
				<td>
				@if( $record->appointStatus == 1)
					 预约中
				@elseif($record->appointStatus == 2) 
					已取消
				@endif
				</td>
				<td>
				@if( $record->appointStatus == 1) 
				<a class="myrequest" href="/admin/material/disappointment/{!! $record->recordId !!}">
				取消预约</a>
				@elseif( $record->appointStatus == 2) 
				<a class="myrequest"
					href="/admin/delete/appointment/{!! $record->recordId !!}/{{ $where }}">删除</a>
				@endif
				</td>
			</tr>
			@endforeach

		</table>
	</div>
	@if($appointmentRecords->count()>0)
		<input type="button" onclick=" print()" class="pull-right" value="打印当前页"/>
	@endif
</div>
@endsection
