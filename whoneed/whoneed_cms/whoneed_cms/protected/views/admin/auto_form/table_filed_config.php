<div class="pageContent">
	<form action="/admin/auto_form/table_filed_config_save" method="post" onsubmit="return validateCallback(this, navTabAjaxDone)">
		<div class="pageFormContent" layoutH="20">
			<dl>
				<dt>字段名：</dt>
				<dd><?php echo $fname; ?></dd>
			</dl>
			<dl class="nowrap">
				<dt>显示名称：</dt>
				<dd>
					<input type='text' name='logic_name' value='<?php echo $objData->logic_name; ?>' />
				</dd>
			</dl>
			<dl class="nowrap">
				<dt>是否表单显示：</dt>
				<dd>
					<input type="radio" name="is_submit" value="1" <?php if($objData->is_submit) echo 'checked'; ?> />是<input type="radio" name="is_submit" value="0" <?php if(!$objData->is_submit) echo 'checked'; ?> />否
				</dd>
			</dl>
			<dl class="nowrap">
				<dt>是否列表显示：</dt>
				<dd>
					<input type="radio" name="is_list" value="1" <?php if($objData->is_list) echo 'checked'; ?> />是<input type="radio" name="is_list" value="0" <?php if(!$objData->is_list) echo 'checked'; ?> />否
				</dd>
			</dl>
			<dl class="nowrap">
				<dt>是否在从表后面显示：</dt>
				<dd>
					<input type="radio" name="is_after_slave" value="1" <?php if($objData->is_after_slave) echo 'checked'; ?> />是<input type="radio" name="is_after_slave" value="0" <?php if(!$objData->is_after_slave) echo 'checked'; ?> />否
				</dd>
			</dl>
			<dl class="nowrap">
				<dt>字段显示顺序：</dt>
				<dd>
					<input type='text' size=3 name='field_order' value='<?php $c_order = intval($_GET['c_oid']); if($objData){ $c_order = $objData->field_order; } echo $c_order; ?>' />
				</dd>
			</dl>
			<div class="unit">
				<label>字段的解释说明：</label>
				<input type='text' name='field_explain' size=30 value='<?php echo $objData->field_explain; ?>' />
				<span class='inputInfo'>在添加/编辑的时候，显示在字段旁边</span>
			</div>
			<div class="divider"></div>
			<dl class="nowrap">
				<dt>字段类型：</dt>
				<dd>
					<select name='field_type'>
						<option vlaue='0'>请选择字段类型</option>
						<?php
							$strFieldType = CF::funGetData('field_type');
							if($strFieldType){								
								foreach($strFieldType as $k=>$v){
						?>
									<option value ="<?php echo $k; ?>" <?php if($objData->field_type == $k) echo 'selected'; ?>><?php echo $v; ?></option>
						<?php
								}
							}
						?>
					</select>
				</dd>
			</dl>
			<dl class="nowrap">
				<dt>字段类型的数据源：</dt>
				<dd>
					<textarea cols="80" rows="8" name="field_type_value"><?php echo $objData->field_type_value; ?></textarea>
				</dd>
			</dl>
			<div class="divider"></div>
			<dl class="nowrap">
				<dt>字段特殊流程处理：</dt>
				<dd>
					<textarea cols="80" rows="5" name="flow_deal"><?php echo $objData->flow_deal; ?></textarea>
				</dd>
			</dl>
			<div class="divider"></div>
			<dl class="nowrap">
				<dt>是否查询显示：</dt>
				<dd>
					<input type="radio" name="is_query" value="1" <?php if($objData->is_query) echo 'checked'; ?> />是<input type="radio" name="is_query" value="0" <?php if(!$objData->is_query) echo 'checked'; ?> />否
				</dd>
			</dl>
			<dl class="nowrap">
				<dt>查询的属性配制：</dt>
				<dd>
					<textarea cols="60" rows="5" name="query_deal"><?php echo $objData->query_deal; ?></textarea>
				</dd>
            </dl>
            <div class="divider"></div>
			<dl class="nowrap">
				<dt>数据汇总(当前页面)：</dt>
				<dd>
					<input type="radio" name="is_current_data_count" value="1" <?php if($arrExtraData['is_current_data_count']) echo 'checked'; ?> />是<input type="radio" name="is_current_data_count" value="0" <?php if(!$arrExtraData['is_current_data_count']) echo 'checked'; ?> />否
				</dd>
			</dl>
            <dl class="nowrap">
				<dt>数据汇总func(当前页面)：</dt>
                <dd>
                    <input type='text' name="current_func" size=30 value='<?php echo $arrExtraData['current_func']; ?>'/>
&nbsp;&nbsp;注意：果填写此函数，上面的汇总开关无效
				</dd>
			</dl>

			<dl class="nowrap">
				<dt>&nbsp;</dt>
				<dd><button type="submit">保存</button>&nbsp;&nbsp;<button type="button" class="close">取消</button></dd>
			</dl>
		</div>
		<input type='hidden' name='tid' value='<?php echo $tid; ?>' />
		<input type='hidden' name='fname' value='<?php echo $fname; ?>' />
	</form>
</div>
