@extends('layouts.adminFrame') 
@section('importCss')
<link href="{{ asset('css/styles/userModify.css') }}"
	rel="stylesheet">
<link href="{{ asset('css/plugins/monthChoose/config.css') }}"
	rel="stylesheet">
<link href="{{ asset('css/plugins/monthChoose/jquery.range.css') }}"
	rel="stylesheet">
@endsection
@section('content')
<div id="page-wrapper" class="center">
	<div class="row">
		<div class="col-lg-12">
			<h1>企业信息</h1>
			<h4 style="color: #955" id="formError"></h4>
			<ol class="breadcrumb">
				<li class="active"><i class="fa fa-table"></i>基本信息</li>
			</ol>
		</div>
	</div>
	<!-- /.row -->

	<div class="row">
		<div class="col-lg-12">
			<div class="well">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#home" data-toggle="tab">基本</a></li>
					<li><a href="#serviceOrder" data-toggle="tab">服务时长</a></li>
				</ul>

				<div id="myTabContent" class="tab-content">
					<div class="tab-pane active in" id="home">
						<form class="form-horizontal" id="baseInfoForm" role="form"
							method="post" action="/admin/company/baseinfo/update">
							<div class="form-group">
								<input type="hidden" name="_token" value="{{csrf_token()}}"> <label
									class="text-info">名称</label> <input type="text"
									class="form-control" value="{{ $company->name }}" name="name"
									required>
							</div>			
							<div class="form-group">
								<label class="text-info">描述</label> <input type="text"
									class="form-control" value="{{ $company->description }}" name="description"
									required>
							</div>
											
							<button type="submit" class="btn btn-primary"
								id="info_update_submit">重置</button>
						</form>
					</div>
					<div class="tab-pane fade" id="serviceOrder">
						<br>
						<form class="form-horizontal" id="baseInfoForm" role="form"
							method="post" action="/admin/company/service/update">
							<input type="hidden" name="_token" value="{{csrf_token()}}">
							<div class="form-group">
								<p>
								<label class="text-info">有效期至</label>
								<label class="label-success">{{ $company->deadline }}</label>
								</p>
								 <!-- <input type="text"	class="form-control" value="{{ $company->deadline }}" name="deadline"> -->
								 
							</div>
							<div class="form-group">
								<label class="text-info">购买时长(30￥/月)</label>
								<div class="demo">
									<input type="hidden" class="single-slider" value="1" name="value"/>
								</div>
							</div>
							<div class="form-group">
								<label class="text-info">支付方式</label><br>
								<div class="pull-left" style="margin-left: 20px;">
								<input type="radio" name="payMethod" value="alipay" checked>支付宝</div>
								<div class="pull-left" style="margin-left: 100px;">
								<input type="radio" name="payMethod" value="wechat">微信</div>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-primary"
								id="service_submit">确认购买</button><h3>待完善</h3>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /.row -->
</div>

@endsection @section('importJs')
<script src="{{ asset('js/jquery.form.js') }}"></script>
<script src="{{ asset('js/plugins/monthChoose/jquery.range.js') }}"></script>
<script src="{{ asset('js/plugins/monthChoose/config.js') }}"></script>
@endsection