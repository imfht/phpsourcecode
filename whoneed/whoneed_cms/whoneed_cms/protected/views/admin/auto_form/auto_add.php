<div class="pageContent">
	<form action="/admin/auto_form/auto_add_save" method="post" class="pageForm required-validate" enctype="multipart/form-data" onsubmit="return iframeCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="10">			
			<?php
				// 主表信息
				if($objField){
					// 主表中的字段，放在从表后面显示
					$arrAfterSlave = array();					
					foreach($objField as $k=>$v){
						
						// 字段配制
						$arrParm = array(
							'name'		=> $v->physics_name,
							'value'		=> '',
							'table_id'	=> $tid
						);
						
						// 判断字段是否需要在从表后面显示
						if($objSlave && $v->is_after_slave){
							$arrT = array();
							$arrT['type']		= $v->field_type;
							$arrT['parm']		= $arrParm;
							$arrT['value']		= $v->field_type_value;
							$arrT['logic_name']	= $v->logic_name;
							$arrAfterSlave[] = $arrT;	
						}else{
							echo '<div class="unit">';
							echo "<label>{$v->logic_name}：</label>";
							echo CF::getFormHtml($v->field_type, $arrParm, $v->field_type_value);
							echo "<span class='inputInfo'>&nbsp;{$v->field_explain}</span>";
							echo '</div>';
						}
					}
				}

				// 从表信息
				if($objSlave){
					foreach($objSlave as $k=>$v){

						$arrParm = array(
							'name'	=> $v->physics_name,
							'value' => '',
							'table_id' => $v->table_id
						);
						
						echo '<div class="unit">';
						echo "<label>{$v->logic_name}：</label>";
						echo CF::getFormHtml($v->field_type, $arrParm, $v->field_type_value);
						echo "<span class='inputInfo'>&nbsp;{$v->field_explain}</span>";
						echo '</div>';
					}

					// 主表需要在从表后面显示的字段
					if($arrAfterSlave){
						foreach($arrAfterSlave as $v){
							echo '<div class="unit">';
							echo "<label>{$v['logic_name']}：</label>";
							echo CF::getFormHtml($v['type'], $v['parm'], $v['value']);
							echo "<span class='inputInfo'>&nbsp;{$v->field_explain}</span>";
							echo '</div>';
						}
					}
				}
			?>

			<dl class="nowrap">
				<dt>&nbsp;</dt>
				<?php
					// 取得自定义的列表操作
					$arrAutoFormOpe = array();
					$arrAutoFormOpe = CF::getAutoFormOpe($tid, 'curd');
					if($arrAutoFormOpe['add'] && $arrAutoFormOpe['add'] == 'no'){
					}else{
						echo "<dd><button type='submit'>保存</button>&nbsp;&nbsp;<button type='button' class='close'>取消</button></dd>";
					}
				?>				
			</dl>
		</div>

		<input type='hidden' name='tid' value='<?php echo $tid; ?>' />
		<input type='hidden' name='type' value='<?php echo $type; ?>' />

		<?php
			// 自定义分类过滤条件
			$arrAutoFormOpe = array();
			$arrAutoFormOpe = CF::getAutoFormOpe($tid, 'type');
			if($arrAutoFormOpe){				
				foreach($arrAutoFormOpe as $k=>$v){
					$strType = '';
					$strType = Yii::app()->request->getParam($k);
					
					echo "<input type='hidden' name='{$k}' value='{$strType}' />";
				}
			}
		?>
	</form>
</div>
