<div class="pageContent">
	<form method="post" action="/admin/auto_form/auto_list/tid/<?php echo $tid; ?>" class="pageForm" onsubmit="return navTabSearch(this);">
		<div class="pageFormContent" layoutH="58">
		<?php
			// 主表信息
			if($objField){				
				foreach($objField as $k=>$v){
					
					// 字段配制
					$arrParm = array(
						'name'		=> $v->physics_name,
						'value'		=> $_GET[$v->physics_name],
						'table_id'	=> $tid
					);
					
					echo '<div class="unit">';
					echo "<label>{$v->logic_name}：</label>";
					

					// 如果查询的是一个区间，比如时间区间，需要两个输入框
					$arrQueryDeal = array();
					if($v->query_deal){
						$str = "\$arrQueryDeal = ".$v->query_deal.";";
						eval($str);
					}

					// 带有区间的查询
					if($arrQueryDeal && $arrQueryDeal['query_type'] == 'between'){
                        // 针对宽度的设置
                        $arrFieldType   = array();
                        $strLeftStyle   = 'style="width:120px;"';
                        $strRightStyle  = 'style="width:120px; margin-left:5px;"';
                        if($v->field_type_value){
                            $str = "\$arrFieldType = ".$v->field_type_value.";";
                            eval($str);

                            if($arrFieldType){
                                // left
                                if(isset($arrFieldType['left_style'])){
                                    $strLeftStyle = $arrFieldType['left_style'];
                                }

                                // right
                                if(isset($arrFieldType['right_style'])){
                                    $strRightStyle = $arrFieldType['right_style'];
                                }                            
                            }
                        }

						// 开始
						$arrParm['name'] = $v->physics_name.'_begin';
                        $arrParm['value']= $_GET[$arrParm['name']];
                        echo "<p {$strLeftStyle}>";
						echo CF::getFormHtml($v->field_type, $arrParm, $v->field_type_value, false, true);
                        echo '</p>';

						// 中间线
						$strFName	= CF::getFName($tid);
						echo '<input type="text" style="background:#fff;height:17px;line-height:17px;border:0px;width:10px;" disabled=true value="——" />';
						echo "<input type='hidden' name='{$strFName}[{$v->physics_name}]' value='1' />";

						// 结束
						$arrParm['name'] = $v->physics_name.'_end';
                        $arrParm['value']= $_GET[$arrParm['name']];
                        echo "<p {$strRightStyle}>";
                        echo CF::getFormHtml($v->field_type, $arrParm, $v->field_type_value, false, true);
                        echo '</p>';
					}else{
						echo CF::getFormHtml($v->field_type, $arrParm, $v->field_type_value, false, true);
					}

					echo "<span class='inputInfo'>&nbsp;{$v->field_explain}</span>";
					echo '</div>';
				}
			}
		?>
		</div>
		<div class="formBar">
			<ul>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">开始检索</button></div></div></li>
				<li><div class="button"><div class="buttonContent"><button type="reset">清空重输</button></div></div></li>
			</ul>
		</div>

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
