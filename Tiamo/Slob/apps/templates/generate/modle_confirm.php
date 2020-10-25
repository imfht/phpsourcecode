<h2 class="contentTitle"><?php echo $page_title; ?></h2>
<div class="pageContent">
	<form method="post" action="<?=URL("generate/modelGenerate")?>" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
		<div class="pageFormContent nowrap" layoutH="97">
			<dl>
				<dt>
					Model名称：
				</dt>
				<dd>
					<input type="text" name="modelName" maxlength="30" class="required" value="<?php echo $model["modelName"]; ?>" />
					<input type="hidden" name="name" maxlength="30"  value="<?php echo $model["name"]; ?>" />
					<span class="info">建议不要修改 <?php if ($model["isModel"]) { ?>当前Model已经存在<?php } ?></span>
				</dd>
			</dl>
		</div>
		<div class="formBar">
			<ul>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
				<li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
			</ul>
		</div>
	</form>
</div>
