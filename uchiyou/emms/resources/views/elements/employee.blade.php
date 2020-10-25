<?php 
// 设置标题
	$disabled = '';
	if($place == 'showNodeInfo'){
		$disabled = 'disabled="disabled"';
	}
?>
<form class="form-horizontal" role="form" method="post"
	action="employee" id="userFormId">
	<input type="hidden" name="_token" value="{{csrf_token()}}"> <input
		type="hidden" name="parentId" value="" id="parentId"> <input
		type="hidden" name="id" value="" id="nodeId"> <input type="hidden"
		name="saveOrUpdate" value="" id="saveOrUpdateMark">
	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">员工信息</h4>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label for="documentName" class="col-sm-2 control-label"
				id="inputTitle">姓名</label>
			<div class="col-sm-10">
				<input type="text" name="name" class="form-control" id="user_name"
					placeholder="员工姓名" value="{{ $user->name or '' }}" {{ $disabled or ''}}>
			</div>
		</div>
	</div>
	@if('showNodeInfo' != $place)
	<div class="modal-body">
		<div class="form-group">
			<label for="documentName" class="col-sm-2 control-label"
				id="inputTitle">密码</label>
			<div class="col-sm-10">
				<input type="password" name="password" class="form-control"
					id="user_password" placeholder="密码" required>
			</div>
		</div>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label for="documentName" class="col-sm-2 control-label"
				id="inputTitle">确认密码</label>
			<div class="col-sm-10">
				<input type="password" name="password2" class="form-control"
					id="user_password2" placeholder="确认密码" required>
			</div>
			<div id="password2Tips"></div>
		</div>
	</div>
	@endif
	<div class="modal-body">
		<div class="form-group">
			<label for="documentName" class="col-sm-2 control-label"
				id="inputTitle">邮箱</label>
			<div class="col-sm-10">
				<input  type="email" name="email" class="form-control"
					id="user_email" placeholder="邮箱" value="{{ $user->email or '' }}" {{ $disabled or ''}}>
			</div>
		</div>
	</div>

	<div class="modal-body">
		<div class="form-group">
			<label for="documentName" class="col-sm-2 control-label"
				id="inputTitle">员工类型</label>
			<div class="col-sm-10">
				<select class="btn col-lg-12 col-md-12 col-sm-12 col-xs-12"
					 name="jobType" {{ $disabled or ''}}>
					<option value="1" {!! (isset($user) && $user->job_type==1)?'selected':'' !!}>管理员</option>
					<option value="2" {!! (isset($user) && $user->job_type==2)?'selected':'' !!}>普通员工</option>
					<option value="3" {!! (isset($user) && $user->job_type==3)?'selected':'' !!}>维修人员</option>
				</select>
			</div>
		</div>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label for="documentName" class="col-sm-2 control-label"
				id="inputTitle">员工编号</label>
			<div class="col-sm-10">
				<input type="text" name="number" class="form-control"
					placeholder="员工工号" value="{{ $user->number or '' }}" {{ $disabled or ''}}>
			</div>
		</div>
	</div>
@if('showNodeInfo' != $place)
	<div class="modal-footer">
		<span id="employeeMessage" style="color: #919191; font-size: 13px;"></span>
		<button type="submit" class="btn btn-primary" id="btnUserAction"
			data-loading-text="提交中...">确定</button>
		<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
	</div>
@endif
</form>