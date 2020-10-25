<?php 
// 设置标题
	$disabled = '';
	
	if($place == 'showNodeInfo'){
		$disabled = 'disabled="disabled"';
		$title = '物资信息';
		$formId = '';
	}
	
?>
			<form class="form-horizontal" role="form" method="post"
				action="treeTrunk" id="directoryFormId">
				<input type="hidden" name="_token" value="{{csrf_token()}}"> <input
					type="hidden" name="id" value=""> <input type="hidden"
					name="parentId" value="" id="parentId"> <input type="hidden"
					name="saveOrUpdate" value="">
				<div class="modal-header">
					<h4 class="modal-title" id="modal-title">目录信息</h4>
				</div>
				
						<div class="modal-body">
					<div class="form-group">
						<label for="documentName" class="col-sm-2 control-label"
							id="label-directory-type">类别</label>
						<div class="col-sm-10">
							<select class="btn col-lg-12 col-md-12 col-sm-12 col-xs-12"
								name="type" {{ $disabled or ''}}>
								<option value="1" {{ isset($directory) && $directory->type ==
							'1' ? 'selected="selected"' : '' }} >组织部门</option>
								<option value="2" {{ isset($directory) && $directory->type ==
							'2' ? 'selected="selected"' : '' }} >物资分支</option>
							</select>
					</div>
				</div>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="documentName" class="col-sm-2 control-label"
							id="inputTitle">名称</label>
						<div class="col-sm-10">
							<input type="text" name="name" class="form-control"
								id="directory-name" placeholder="目录名称"
								value="{{ $directory->name or '' }}" {{ $disabled or ''}}>
						</div>
					</div>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<label for="documentName" class="col-sm-2 control-label"
							id="inputTitle">编号</label>
						<div class="col-sm-10">
							<input type="text" name="number" class="form-control"
								id="directory-number" placeholder="目录编号"
								value="{{ $directory->number or '' }}" {{ $disabled or ''}}>
						</div>
					</div>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="documentName" class="col-sm-2 control-label"
							id="directory-inputTitle">描述</label>
						<div class="col-sm-10">
							<textarea name="description" class="form-control"
								id="directory-descrition" cols="30" rows="4"
							 {{ $disabled or ''}}>{{ $directory->description or '' }}</textarea> 
						</div>
					</div>
				</div>
@if($place != 'showNodeInfo')
				<div class="modal-footer">
					<span id="trunkErrorMessage" style="color: #919191; font-size: 13px;"></span>
					<button type="submit" class="btn btn-primary"
						id="btn-directory-action" data-loading-text="提交中...">确定</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				</div>
@endif
			</form>