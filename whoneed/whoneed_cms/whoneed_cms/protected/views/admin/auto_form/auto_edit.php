<div class="pageContent">
	<form action="/admin/auto_form/auto_edit_save" method="post" class="pageForm required-validate" enctype="multipart/form-data" onsubmit="return iframeCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="10">			
			<?php
				// 主表信息
				if($objField){
					// 主表中的字段，放在从表后面显示
					$arrAfterSlave = array();					
					foreach($objField as $k=>$v){
						
						// 取得值
						$strField = $v->physics_name;
						$strValue = $objMData->$strField;

						// 字段配制
						$arrParm = array(
							'name'		=> $v->physics_name,
							'value'		=> $strValue,
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
						// 取得值
						$strFieldName	= $v->physics_name;
						$strValue		= $objMData->slave->$strFieldName;
						
						// 字段配制
						$arrParm = array(
							'name'		=> $v->physics_name,
							'value'		=> $strValue,
							'table_id'	=> $v->table_id
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
					if($arrAutoFormOpe['update'] && $arrAutoFormOpe['update'] == 'no'){
					}else{
						echo "<dd><button type='submit'>编辑</button>&nbsp;&nbsp;<button type='button' class='close'>取消</button></dd>";
					}
				?>				
			</dl>
		</div>

		<input type='hidden' name='tid' value='<?php echo $tid; ?>' />
		<input type='hidden' name='id' value='<?php echo $id; ?>' />

		<?php
			// 自定义分类过滤条件
			$arrAutoFormOpe = array();
			$arrAutoFormOpe = CF::getAutoFormOpe($tid, 'type');
			if($arrAutoFormOpe){				
				foreach($arrAutoFormOpe as $k=>$v){
					$strType = '';
					$strType = trim(Yii::app()->request->getParam($k));
					if($v == 'int'){
						$strType = intval($strType);
					}
					
					if(!empty($strType))	echo "<input type='hidden' name='{$k}' value='{$strType}' />";
				}
			}
		?>
	</form>
</div>

<script>
function DrawImage(ImgD,iwidth,iheight){   
    //参数(图片,允许的宽度,允许的高度)   
    var image=new Image();   
    image.src=ImgD.src;   
    if(image.width>0 && image.height>0){   
      if(image.width/image.height>= iwidth/iheight){   
          if(image.width>iwidth){     
              ImgD.width=iwidth;   
              ImgD.height=(image.height*iwidth)/image.width;   
          }else{   
              ImgD.width=image.width;     
              ImgD.height=image.height;   
          }   
      }else{   
          if(image.height>iheight){     
              ImgD.height=iheight;   
              ImgD.width=(image.width*iheight)/image.height;           
          }else{   
              ImgD.width=image.width;     
              ImgD.height=image.height;   
          }   
      }   
    }   
} 
</script>