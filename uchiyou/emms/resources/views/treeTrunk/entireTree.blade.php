@extends('layouts.adminFrame') @section('importCss')
<link
	href="{{ asset('css/plugins/jsTree/themes/default/style.min.css') }}"
	rel="stylesheet">
<link href="{{ asset('css/styles/treeStruct.css') }}" rel="stylesheet">
@endsection @section('content')
<div class="col-sm-3 col-lg-3">
	@include('elements.treeHeaderPart',['place' => 'entireTree'])</div>
<!--  tree trunk Model -->
<div class="modal fade" id="create_directory" tabindex="-1"
	role="dialog" aria-labelledby="{{session('company')!=null and session('company')->name or ''}}"
	 aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			@include('elements.treeTrunkTable',['place' => 'entireTree'])
		</div>
	</div>
</div>
<!-- end tree trunk Model -->
<!--leaf Modal -->
<div class="modal fade" id="create_material_info" tabindex="-1"
	role="dialog" aria-labelledby="添加物资信息" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		@include('elements.materialTable',['place'
			=> 'entireTree','url'=>'material'])
			</div>
	</div>
</div>
<!--end leaf Modal -->
<!-- 显示节点信息区域 -->
<div class="col-sm-4 col-lg-4">
	<div id="nodeInfoShow"></div>
</div>

@endsection 
@section('importJs')

<script src="{{ asset('js/jquery.form.js') }}"></script>
<script src="{{ asset('js/ajaxfileupload.js') }}"></script>
<script src="{{ asset('js/plugins/layer/layer.min.js') }}"></script>
<script src="{{ asset('js/plugins/jsTree/jstree.js') }}"></script>

 @if(Auth::user()->job_type == 1)
	<script src="{{ asset('js/plugins/jsTree/mytreeOperate.js') }}"></script>
	<script src="{{ asset('js/plugins/jsTree/materialTree.js') }}"></script>
 @else
 	<script src="{{ asset('js/plugins/jsTree/showTreeNodeInfo.js') }}"></script>
 @endif
<script>
$(document).ready(function(){
	
	// 拦截归还的 get请求
	$(".appointment").on('click',function(){
	    var rentUrl = $(this).attr('href');// 获取当前标签 href 属性的值
	    var alink = $(this);
	    return false;
	    $.ajax({  
	        url: rentUrl,  // 请求的url
	        data: {},  //请求携带的数据
	        dataType: 'text',  // 请求携带的数据类型
	        type: 'get',   // 请求的方法
	        success: function(data,status){
	            layer.msg('信息提交成功 ！');
	            alink.parent().parent().hide();
	            },  
	        error: function(){
		        },  // 请求出错时的回调方法
	        complete: function(){}  
	    });
	   // $(this).parent().parent().hide();
	    return false;// 超链接标签本身不在加载该链接内容
	});
});
</script>
<!-- <script src="{{ asset('js/plugins/jsTree/eventHandler-mytree.js') }}"></script> -->
@endsection
