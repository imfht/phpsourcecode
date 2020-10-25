
<div class="pageContent">
	<form action="/admin/system/column_edit_save" method="post" onsubmit="return validateCallback(this, navTabAjaxDone)">
		<div class="pageFormContent">
			<dl>
				<dt>栏目名称：</dt>
				<dd><input class="required" name="column_name" type="text" size="20" value='<?php echo $objData->column_name; ?>' /></dd>
			</dl>
			<dl class="nowrap">
				<dt>栏目URL：</dt>
				<dd><input name="column_url" type="text" size="30" value='<?php echo $objData->column_url; ?>' /></dd>
			</dl>
			<dl class="nowrap">
				<dt>内容模型：</dt>
				<dd>
					<select name='model_id'>
						<option value='0'>请选择模型</option>
					<?php
						$objModelList = CF::getSystemModelList();
						if($objModelList){
							$intModelId = $objData->model_id;
							foreach($objModelList as $model){
								$selected = '';
								if($model->id == $intModelId) $selected = 'selected';
								echo "<option value='{$model->id}' {$selected}>{$model->model_name}</option>";
							}
						}
					?>
					</select>
				</dd>
			</dl>
			<dl class="nowrap">
				<dt>排序：</dt>
				<dd><input name="c_order" type="text" size="4" value='<?php echo $objData->c_order; ?>' /></dd>
			</dl>
			<dl>
				<dt>&nbsp;</dt>
				<dd><button type="submit">保存</button>&nbsp;&nbsp;<button type="button" class="close">取消</button></dd>
			</dl>
		</div>

		<input type='hidden' name='fid' value='<?php echo $objData->fid; ?>' />
		<input type='hidden' name='id' value='<?php echo $objData->id; ?>' />
	</form>
</div>
