@extends('layouts.adminFrame') @section('importCss')
<link
	href="{{ asset('css/plugins/jsTree/themes/default/style.min.css') }}"
	rel="stylesheet">
<link href="{{ asset('css/styles/treeStruct.css') }}" rel="stylesheet">
@endsection @section('content')
<!-- tree part -->
<div class="col-sm-3 col-lg-3">
@include('elements.treeHeaderPart',['place' => 'organization'])
</div>
<!--  tree trunk Model -->
<div class="modal fade" id="create_directory" tabindex="-1"
	role="dialog" aria-labelledby="添加目录信息" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			@include('elements.treeTrunkTable',['place' => 'organization'])</div>
	</div>
</div>
<!-- end tree trunk Model -->
<!-- employee Model -->
<div class="modal fade" id="create_user_info" tabindex="-1"
	role="dialog" aria-labelledby="添加员工信息" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">@include('elements.employee',['place' =>
			'organization'])</div>
	</div>
</div>
<!-- end employee Model -->
<!-- 显示节点信息区域 -->
<div class="col-sm-4 col-lg-4">
	<div id="nodeInfoShow"></div>
</div>
@endsection @section('importJs')

<script src="{{ asset('js/jquery.form.js') }}"></script>
<script src="{{ asset('js/plugins/layer/layer.min.js') }}"></script>
<script src="{{ asset('js/plugins/jsTree/jstree.js') }}"></script>

<script src="{{ asset('js/plugins/jsTree/organizationOperate.js') }}"></script>
<script src="{{ asset('js/plugins/jsTree/organizationTree.js') }}"></script>
<!-- <script src="{{ asset('js/plugins/jsTree/eventHandler-mytree.js') }}"></script> -->
@endsection
