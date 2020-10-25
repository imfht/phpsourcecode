<{include file="public/header.tpl"}>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>修改分类</h3>
	<form action="<{$smarty.const.__CONTROLLER__}>/update" method="post">
		<div class="form-group">
		<label for="InputCat">请选择父类：</label>
		<{$select}> 
		</div>
		<input type="hidden" name="id" value="<{$cats.id}>">
		<div class="form-group">
		<label for="InputName">修改类名</label>
			<input type="text" class="form-control" name='name' value='<{$cats.name}>'>
		</div>
		<button type="submit" class="btn btn-default">修改分类</button>
	</form>
</div>
</div>
<{include file="public/footer.tpl"}>