<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="renderer" content="webkit">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>物资管家</title>

<meta name="keywords" content="">
<meta name="description" content="">

<link rel="shortcut icon" href="favicon.ico">
<link href="{{ asset('css/bootstrap.min.css?v=3.3.6') }}"
	rel="stylesheet">

</head>

<body class="fixed-sidebar full-height-layout gray-bg"
	style="overflow: auto">
	
<div class="container">
	<div id="resultMessage"></div>
	<div class="row">
	@if(isset($material->picture_url))
		<div class="col-md-4">
		<img id='showImage' src="/picture/download/{{$material->picture_url or ''}}"
					  data-action="zoom" class="pull-right stayShape" height="500px" width="500px" />      	
		</div>
	@endif
		<div class="col-md-3" style="margin-top: 50px;">

			<p class="lead" style="color: #499">名称 ：{{ $material->name or '' }}</p>
			<p class="lead" style="color: #499">编号：{{ $material->material_number or '' }}</p>
	 		<p class="lead" style="color: #499">所在部门：{{ $material->departmentName or '' }}</p>
			<p class="lead" style="color: #499">价格：{{ $material->price or '' }} 元</p>
			<p class="lead" style="color: #499">资产类型：
			@if($material->main_type == 1)
			固定资产
			@elseif($material->main_type == 2)
			耗材
			@endif
			
			</p>
			<p class="lead" style="color: #499">类别：{{ $material->type or '' }}</p>
			<p class="lead" style="color: #499">描述：{{ $material->description or '' }}</p>
			<p class="lead" style="color: #499">状态：
					@if( $material->status == 1) 可用
				@elseif($material->status == 2) 已借出
					 @if( $appointNumbers != 0) ,
						有{{$appointNumbers}} 人在预约。 
					 @endif 
				@elseif($material->status == 15)
					正在您的租用期中，等待归还
				@elseif($material->status == 3)
					故障中
				@elseif($material->status == 4)
					即将报废
				@endif
			</p>
			<p>
				@if( $material->status == 1)  
				<!-- <a role="button" href="#"
				class="btn btn-success">报废</a>  -->
			@elseif( $material->status == 15)
			@elseif( $material->status == 2)
				@if( $hasAppoint )
				<a role="button"
				href="/admin/material/appointment/history/person/appointed"
				class="btn btn-success"> 查看预约 </a>
				@else 
				<a role="button"
				href="/admin/material/appointment/{{ $material->id or '' }}"
				class="btn btn-success appointment"> 预约 </a> 
				@endif
			@endif
			</p>
		</div>
		@if( $material->status == 1) 
		<div class="col-md-5" style="margin-top: 50px;">
		<form class="form-horizontal" role="form" method="post"
		action="/admin/material/rent/{{ $material->id or '' }}" id="materialFormId">
		<input type="hidden" name="_token" value="{{csrf_token()}}"> 
		<input type="hidden" name="materialId" value="{{ $material->id or '' }}">
		<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-md-3 control-label"
					id="inputTitle">归还日期</label>
				<div class="col-sm-9">
					<input type="date" name="time" class="form-control"
						id="material-number" value="{{ $deadline or ''}}">
				</div>
			</div>
		</div>
		<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-md-3 control-label"
					id="inputTitle">送货上门</label>
				<div class="col-sm-9">
					<label>是<input
						style="margin-right: 30px; margin-left: 20px;"
						type="radio" name="deliver" value="yes" /></label> 
					<label>否<input
						style="margin-left: 20px;" type="radio"
						name="deliver" checked="checked" value="no" /></label>
				</div>
			</div>
		</div>
		<div class="modal-body delivered">
			<div class="form-group">
				<label for="documentName" class="col-md-3 control-label"
					id="inputTitle">联系电话</label>
				<div class="col-sm-9">
					<input type="number" name="phone" class="form-control"
						id="material-approver" placeholder="联系电话"
						value="{{$user->phone or ''}}">
				</div>
			</div>
		</div>
		<div class="modal-body delivered">
			<div class="form-group">
				<label for="documentName" class="col-md-3 control-label"
					id="inputTitle">联系地址</label>
				<div class="col-sm-9 form-inline">
						<div data-toggle="distpicker">
							<div class="col-sm-4 form-group">
								<label class="sr-only" for="province1">省</label> <select
									name="province" class="form-control"  id="province1"
									data-province="{{$user->address[0] or '' }}"></select>
							</div>
							<div class="col-sm-4 form-group">
								<label class="sr-only" for="city1">市</label> <select
									name="city" class="form-control" data-city="{{$user->address[1] or '' }}" id="city1"></select>
							</div>
							<div class="col-sm-4 form-group">
								<label class="sr-only" for="district1">县/区</label> <select
									name="district" class="form-control" data-district="{{$user->address[2] or '' }}" id="district1"></select>
							</div>
						</div>
					</div>
					
			</div>
		</div>
		<div class="modal-body delivered">
			<div class="form-group">
				<label for="documentName" class="col-md-3 control-label"
					id="inputTitle">详细地址</label>
				<div class="col-sm-9">
					<input type="text" name="detailAddress" class="form-control"
						id="material-approver" placeholder="详细地址，如街道，楼号，几楼和门号等"
						value="{{ $user->address[3] or '' }}">
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<span id="error-message" style="color: #919191; font-size: 13px;"></span>
			<button type="submit" class="btn btn-success"
				id="btn-material-action" data-loading-text="提交中...">租借</button>
		</div>
		</form>
		</div>
		@endif
		<div class="row">
		<div class="col-md-4" style="margin-left:auto;margin-right:auto;width:50%;">
		
			@if ($place == 'showNodeInfo' && ($material->status == 15 || $material->status == 1 || 
			(Auth::user()->job_type==1 && $material->status != 3 && $material->status != 4)))
				@include('elements/faultDescription',['materialId'=>$material->id])
			@elseif($material->status == 3 && Auth::user()->job_type==1 )
			<a class="btn btn-primary" role="button"
			 href="/admin/material/repaire/history/applys">故障列表</a>
			@elseif($material->status == 4 && Auth::user()->job_type==1 )
			<a class="btn btn-primary" role="button"
			 href="/admin/material/repaire/history/all">故障列表</a>
			@endif
		</div>
		</div>
	</div>
</div>
	
	<!-- 全局js -->
	<script src="{{ asset('js/jquery.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap.min.js?v=3.3.6') }}"></script>
	<script src="{{ asset('js/plugins/layer/layer.min.js') }}"></script>
	
<script src="{{ asset('js/plugins/choosePlace/distpicker.data.min.js') }}"></script>
<script src="{{ asset('js/plugins/choosePlace/distpicker.min.js') }}"></script>

<script type="text/javascript">
	$(function(){
		$content = $(".delivered");
		$content.hide();
		  $(":radio").click(function(){
			 $deliver = $(this).val();
			 if($deliver == "yes"){
				 $content.show();
			 }else{
				 $content.hide();
			 }
		  });

		 });
	</script>

	<div style="text-align: center;"></div>
</body>

</html>