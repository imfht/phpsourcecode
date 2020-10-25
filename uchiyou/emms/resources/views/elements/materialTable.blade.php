<?php
// 设置标题
$title = '添加物资信息';
$formId = 'materialFormId';
$disabled = '';
if ($place == 'showNodeInfo') {
	$title = '物资信息';
	$formId = '';
	$disabled = 'disabled="disabled"';
} elseif ($place == 'entireTree') {
	$title = '物资信息';
} elseif ($place == 'PurchaseMaterialApply'){
	
}

?>
<form class="form-horizontal" role="form" method="post"
		action="{{ $url or '' }}" id="{{ $formId or 'materialFormId' }}">
		<input type="hidden" name="_token" value="{{csrf_token()}}"> <input
			type="hidden" name="parentId" value="" id="parentId"> <input
			type="hidden" name="id" value="" id="nodeId"> <input type="hidden"
			name="saveOrUpdate" value="" id="saveOrUpdateMark"> <input
			type="hidden" name="materialId" value="{{ $material->id or '' }}">
		<div class="modal-header">
			<h4 class="modal-title" id="modal-title">{{ $title or ''}}</h4>
		</div>
		<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-sm-3 control-label"
					id="inputTitle">名称</label>
				<div class="col-sm-9">
					<input type="text" name="name" class="form-control"
						id="material-name" placeholder="物资名称"
						value="{{ $material->name or '' }}" {{ $disabled or ''}}>
				</div>
			</div>
		</div>
		@if($place == 'showNodeInfo')
		<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-sm-3 control-label"
					id="inputTitle">图片</label>
				<div class="col-sm-9">
					@if('' != $material->picture_url)
					<img id='showImage' src="/picture/download/{{$material->picture_url or ''}}"
					  data-action="zoom" class="stayShape" height="50" width="50" />      	
					@else
					暂无图片
					@endif
				</div>
			</div>
		</div>
		@endif
		<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-sm-3 control-label"
					id="inputTitle">资产类型</label>
				<div class="col-sm-9">
					<select class="btn col-lg-12 col-md-12 col-sm-12 col-xs-12"
						name="mainType" {{ $disabled or ''}}>
						<!-- 设置默认的资产类型，利用php 的短路原则 -->
						<option value="1" {{ isset($material) && $material->main_type ==
							'1' ? 'selected="selected"' : '' }}>固定资产</option>
						<option value="2" {{ isset($material) && $material->main_type ==
							'2' ? 'selected="selected"' : '' }}>耗材</option>
					</select>
				</div>
			</div>
		</div>
		<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-sm-3 control-label"
					id="label-material-type">类别</label>
				<div class="col-sm-9">
					<input type="text" name="type" class="form-control"
						id="material-type" placeholder="种类"
						value="{{ $material->type or '' }}" {{ $disabled or ''}}>
				</div>
			</div>
		</div>
		<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-sm-3 control-label"
					id="inputTitle">价格</label>
				<div class="col-sm-9">
					<input name="price" class="form-control" id="material-number"
						placeholder="物资价格" type="number" step=0.001
						value="{{ $material->price or '' }}" {{ $disabled or ''}}>
				</div>
			</div>
		</div>
		@if ($place != 'PurchaseMaterialApply')
		<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-sm-3 control-label"
					id="inputTitle">编号</label>
				<div class="col-sm-9">
					<input type="text" name="number" class="form-control"
						id="material-number" placeholder="物资编号"
						value="{{ $material->material_number or '' }}" {{ $disabled or ''}}>
				</div>
			</div>
		</div>
		@endif 
		@if ($place == 'PurchaseMaterialApply')
		<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-sm-3 control-label"
					id="inputTitle">数量</label>
				<div class="col-sm-9">
					<input name="quantity" class="form-control" id="material-quantity"
						placeholder="数量" type="number" step=1
						value="{{ $material->quantity or '' }}">
				</div>
			</div>
		</div>
		<!--<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-sm-3 control-label"
					id="inputTitle">审批人1</label>
				<div class="col-sm-7">
					<input type="text" name="approver" class="form-control"
						id="material-approver" placeholder="审批人姓名">

				</div>
				 <button class="btn btn-xs btn-info col-sm-2" id="addApprover">增加审批人</button>
			 </div>
		</div>-->
		@elseif ($place == 'entireTree')
		<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-sm-3 control-label"
					id="inputTitle">上传图片</label>
				<div class="col-sm-9">
					<input type="file" name="picture" class="form-control"
						id="picture">
					<a role="button" class="btn btn-xs-12 btn-info col-sm-12" id="uploadPicture">上传图片</a>
					
					<img id='imageShow' src="/picture/download/{{$material->picture_url or ''}}" height="100" width="200" />      
				
					<input type="hidden" class="form-control" value="{{ $material->picture_url or ''}}" name="pictureUrl" id="imageUrl">	
				</div>
			</div>
		</div>
		@endif 
		<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-sm-3 control-label"
					id="material-inputTitle">物资描述</label>
				<div class="col-sm-9">
					<textarea name="description" class="form-control"
						id="material-descrition" cols="30" rows="4"
					{{ $disabled or ''}}>{{ $material->description or '' }}</textarea>
				</div>
			</div>
		</div>
		@if(($place == 'PurchaseMaterialApply' || $place == 'entireTree'))
		<div class="modal-footer">
			<span id="error-message" style="color: #009191; font-size: 18px;"></span>
			<button type="submit" class="btn btn-success"
				id="btn-material-action" data-loading-text="提交中...">确定</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>

		</div>
		@elseif($place == 'showNodeInfo')
 		<div class="modal-body">
			<div class="form-group">
				<label for="documentName" class="col-sm-3 control-label"
					id="material-inputTitle">状态</label>
				<div class="col-sm-9">
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
				@endif</div>
			</div>
		</div>

		<div class="modal-footer">
			<span id="error-message" style="color: #919191; font-size: 13px;"></span>
<a role="button"
				href="/admin/material/{{ $material->id or '' }}/show"
				class="btn btn-success myrequest"> 详情 </a>
			
		</div>
		@endif
	</form>
	<!-- 物资信息表部分 -->
	
