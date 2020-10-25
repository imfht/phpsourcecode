@extends('layouts.adminFrame') 
@section('content')
<div class="container">
	<div style="margin-bottom: 60px;">
		@if(Auth::user()->job_type == 1)
		<h3>
			<a href="/admin/material/purchase/history/manage/apply" 
			@if($type=='apply')
				style="font-size: 40px;" 
			@endif>待审批</a> 
			<a href="/admin/material/purchase/history/manage/agree" 
			@if($type== 'agree')
				style="font-size: 40px;" 
			@endif>待分配</a>
			<a href="/admin/material/purchase/history/manage/all" 
			@if($type== 'all')
				style="font-size: 40px;"
			@endif>历史记录</a>
		</h3>
		@else
		<h3>
			<a href="/admin/material/purchase/history/person/receive" 
			@if($type== 'receive')
				style="font-size: 40px;" 
			@endif>待接收</a> 
			<a href="/admin/material/purchase/history/person/all" 
			@if($type== 'all')
				style="font-size: 40px;" 
			@endif>历史记录</a> 
			<a role="button" class="btn btn-info" id="applyPurchaseId"
				style="margin-right: 100px;">申请购买新物资</a>
		</h3>
	@endif
<?php
$url = '/admin/material/purchase/history/search/' . $where . '/' . $type;
?>
<div class="pull-right">@include('elements/historySearchForm')</div>
	</div>
	{{ $purchaseRecords->links() }}
	<div class="table-responsive" id="print">
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th>序号</th>
					<th>物资名称</th>
					<th>申请人</th>
					<th>电话</th>
					<th>编号</th>
					<th>所在部门</th>
					<th>资产类型</th>
					<th>类别</th>
					<th>数量</th>
					<th>申请时间</th>
					<th>状态</th>
					<th>操作</th>
				</tr>
			</thead>
			@foreach ($purchaseRecords as $record)
			<tr>
				<td>{{$loop->index}}</td>
				<td>{!! $record->name !!}</td>
				<td>{!! $record->userName !!}</td>
				<td>{!! $record->phone !!}</td>
				<td>{!! $record->employeeNumber or ' ' !!}</td>
				<td>{!! $record->departmentNumber or session('company')->name !!}</td>
				<td>@if($record->main_type == 1) 固定资产 @elseif($record->main_type ==
					2) 耗材 @endif</td>
				<td>{!! $record->type !!}</td>
				<td>{!! $record->quantity !!}</td>
				<td>{!! $record->created_at !!}</td>
				<td>@if($record->statuses ==1)
					<h5>待审批</h5> @elseif($record->statuses == 3)
					<h5>已同意</h5> @elseif($record->statuses == 4)
					<h5>已拒绝</h5> @elseif($record->statuses == 5)
					<h5>已分派</h5> @elseif($record->statuses == 6)
					<h5>已接收</h5> @endif
				</td>
				<td>@if(Auth::user()->job_type == 1) @if($record->statuses ==1) <a
					class="myrequest"
					href="/admin/material/purchase/{{$record->id}}/approve/agree">同意</a>
					<a class="myrequest"
					href="/admin/material/purchase/{{$record->id}}/approve/reject">不同意</a>
					@elseif($record->statuses == 3) <a class="myrequest"
					href="/admin/material/purchase/{{$record->id}}/approve/allocation">分配</a>
					@endif <a class="myrequest"
					href="/admin/delete/purchase/{{ $record->id }}/manage">删除</a> @else
					@if($record->statuses == 5) <a class="myrequest"
					href="/admin/material/purchase/{!! $record->id !!}/approve/receive">确认接收</a>
					@else <a class="myrequest"
					href="/admin/delete/purchase/{{ $record->id }}/person">删除</a>
					@endif @endif
				</td>
			</tr>
			@endforeach
		</table>
	</div>
	@if($purchaseRecords->count()>0)
		<a href="/admin/material/purchase/excel/export/{{$where}}/{{$type}}">全部导出到 excel</a>
		<input type="button" onclick=" print()" class="pull-right" value="打印当前页"/>
	@endif
	<!--apply material Modal -->
	<div class="modal fade" id="apply_purchase_material_info" tabindex="-1"
		role="dialog" aria-labelledby="物资信息" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				@include('elements.materialTable',['place' =>
				'PurchaseMaterialApply','url'=>'/admin/material/purchase/apply'])</div>
		</div>
	</div>
	<!--end apply material Modal -->

	@endsection 
	@section('importJs')
	<script src="{{ asset('js/jquery.form.js') }}"> </script>
	<script src="{{ asset('js/purchaseApply.js') }}"> </script>		
	@endsection