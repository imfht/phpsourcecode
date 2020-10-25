@extends('layouts.adminFrame') 
@section('importCss')
<link href="{{ asset('css/styles/userModify.css') }}"
	rel="stylesheet">
@endsection
@section('content')
<div id="page-wrapper" class="center">
	<div class="row">
		<div class="col-lg-12">
			<h1>个人信息</h1>
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
					<li><a href="#updatePassword" data-toggle="tab">密码</a></li>
				</ul>

				<div id="myTabContent" class="tab-content">
					<div class="tab-pane active in" id="home">
						<form class="form-horizontal" id="baseInfoForm" role="form"
							method="post" action="user/baseInfo/update">
							<div class="form-group">
								<input type="hidden" name="_token" value="{{csrf_token()}}"> <label
									class="text-info">用户名</label> <input type="text"
									class="form-control" value="{{ $user->name }}" name="name"
									required>
							</div>

							<div class="form-group">
								<label class="text-info">邮箱</label> <input type="email"
									class="form-control" value="{{ $user->email }}" name="email"
									required>
							</div>
							<div class="form-group">
								<label class="text-info">电话</label> <input type="number"
									class="form-control" value="{{ $user->phone }}" name="phone"
									required>
							</div>
							<div class="form-group">
									<label for="documentName" class="text-info">联系地址</label><br>
									<div class="col-sm-9 form-inline">
										<div data-toggle="distpicker">
											<div class="col-sm-4 form-group">
												<label class="sr-only" for="province1">Province</label> <select
													name="province" class="form-control" id="province1"
													data-province="{{ $user->address[0] or '' }}"></select>
											</div>
											<div class="col-sm-4 form-group">
												<label class="sr-only" for="city1">City</label> <select
													name="city" class="form-control"
													data-city="{{ $user->address[1] or '' }}" id="city1"></select>
											</div>
											<div class="col-sm-4 form-group">
												<label class="sr-only" for="district1">District</label> <select
													name="district" class="form-control"
													data-district="{{ $user->address[2] or '' }}"
													id="district1"></select>
											</div>
										</div>
									</div>

								</div>
								<div class="form-group">
									<label for="documentName" class="text-info">详细地址</label><br>
									<div class="col-sm-9">
										<input type="text" name="detailAddress" class="form-control"
											id="material-approver" placeholder="详细地址，如街道，楼号，几楼和门号等"
											value="{{ $user->address[3] or '' }}">
									</div>
								</div>


							<button type="submit" class="btn btn-primary"
								id="info_update_submit">重置</button>
						</form>
					</div>



					<div class="tab-pane fade" id="updatePassword">
						<br>
						<form class="form-horizontal" id="passwordForm" role="form"
							method="post" action="user/password/update">
							<input type="hidden" name="_token" value="{{csrf_token()}}"> <label
								class="text-info">原密码</label> <input type="password"
								class="form-control" name="oldPassword" placeholder="原密码"
								required> <label class="text-info">新密码</label> <input
								type="password" class="form-control" name="newPassword"
								id="newPassword" placeholder="新密码" required> <label
								class="text-info">确认新密码</label> <input type="password"
								class="form-control" id="confirmPassword" placeholder="重输一次新密码"
								required>
							<div>
								<button class="btn btn-primary" id="password_update_submit">重置密码</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /.row -->
</div>
<!-- /#page-wrapper -->

@endsection @section('importJs')
<script src="{{ asset('js/jquery.form.js') }}"></script>
<script src="{{ asset('js/plugins/choosePlace/distpicker.data.min.js') }}"></script>
<script src="{{ asset('js/plugins/choosePlace/distpicker.min.js') }}"></script>
<script src="{{ asset('js/userManage.js') }}"></script>
@endsection
