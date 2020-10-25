<form role="search" class="navbar-form-custom" method="get"
	action="{{$url}}" id="centerSearchForm"
	style="display: inline-block">
	<input type="hidden" name="_token" value="{{csrf_token()}}">
	<table>
		<tr>
			<td><select class="form-control selectType" name="type"
				style="vertical-align: middle;" id="searchType">
					<option value="materialName">物资名称</option>
					<option value="userName">用户名字</option>
			</select></td>
			<td><input type="text" placeholder="请输要查找的记录内容 …"
				class="form-control searchContent" style="vertical-align: middle;"
				name="content"></td>
		</tr>
	</table>
</form>