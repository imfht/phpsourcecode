<link href="{{ asset('css/bootstrap.min.css?v=3.3.6') }}"
	rel="stylesheet">
<link href="{{ asset('css/styles/centerSearch.css') }}"
	rel="stylesheet">
	
<div class="center">
<h1>信息检索</h1>
<form role="search" class="navbar-form-custom" method="get"
	action="/admin/search" id="centerSearchForm"
	style="display: inline-block">
	<input type="hidden" name="_token" value="{{csrf_token()}}">
	<table>
		<tr>
			<td><select class="form-control selectType" name="type"
				style="vertical-align: middle;" id="searchType">
					<option value="name">名称</option>
					<option value="type">类型</option>
					<option value="tree_trunk_name">部门</option>
			</select></td>
			<td><input type="text" placeholder="请输要查找的内容 …" class="form-control searchContent"
				style="vertical-align: middle;" name="content"></td>
		</tr>
	</table>
</form>
</div>
<!-- 全局js -->
	<script src="{{ asset('js/jquery.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap.min.js?v=3.3.6') }}"></script>
	<script src="{{ asset('js/jquery.form.js') }}"></script>
	<script src="{{ asset('js/search.js') }}"></script>