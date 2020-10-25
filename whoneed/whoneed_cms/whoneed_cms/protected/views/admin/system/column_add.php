
<div class="pageContent">
	<form action="/admin/system/column_add_save" method="post" onsubmit="return validateCallback(this, navTabAjaxDone)">
		<div class="pageFormContent">
			<dl>
				<dt>栏目名称：</dt>
				<dd><input class="required" name="column_name" type="text" size="20" /></dd>
			</dl>
			<dl class="nowrap">
				<dt>栏目URL：</dt>
				<dd><input name="column_url" type="text" size="30" /></dd>
			</dl>
			<dl class="nowrap">
				<dt>内容模型：</dt>
				<dd>
					<select name='model_id'>
						<option value='0'>请选择模型</option>
					<?php
						$objModelList = CF::getSystemModelList();
						if($objModelList){
							foreach($objModelList as $model){
								echo "<option value='{$model->id}'>{$model->model_name}</option>";
							}
						}
					?>
					</select>
				</dd>
			</dl>
			<dl class="nowrap">
				<dt>排序：</dt>
				<dd><input name="c_order" type="text" size="4" value='0'/></dd>
			</dl>
			<dl>
				<dt>&nbsp;</dt>
				<dd><button type="submit">保存</button>&nbsp;&nbsp;<button type="button" class="close">取消</button></dd>
			</dl>
		</div>

		<input type='hidden' name='fid' value='<?php echo $fid; ?>' />
	</form>
</div>
