<!-- 故障描述部分 -->
<form class="form-horizontal" role="form" method="post"
	action="/admin/material/{!! $materialId !!}/repaire/apply" id="faultFormId">
	<input type="hidden" name="_token" value="{{csrf_token()}}">
	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">故障报修</h4>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label for="documentName" class="col-sm-2 control-label"
				id="inputTitle">故障描述</label>
			<div class="col-sm-10">
				<textarea rows="5" cols="30" name="description" class="form-control"
					placeholder="故障描述，越详细越好"></textarea>
			</div>
		</div>
	</div>

	<div class="modal-footer">
		<span id="error-message" style="color: #919191; font-size: 13px;"></span>
		<button type="submit" class="btn btn-primary" id="btn_user_action"
			data-loading-text="提交中...">报修</button>
	</div>
</form>
<!--  故障描述部分 -->