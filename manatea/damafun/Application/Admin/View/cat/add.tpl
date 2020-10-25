<{include file="public/header.tpl"}>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>添加分类</h3>
<form action="<{$smarty.const.__CONTROLLER__}>/insert" method='post'>
	<div class="form-group">
	<label for="InputCat">上层分类：</label>
	<{$select}> 
	</div>
	<div class="form-group">
	<label for="InputName">分类名称：</label>
	<input type="text" class="form-control" name='name' value=''>
	</div>
	<button type="submit" class="btn btn-default">添加分类</button>
</form>
</div>
</div>

<{include file="public/footer.tpl"}>