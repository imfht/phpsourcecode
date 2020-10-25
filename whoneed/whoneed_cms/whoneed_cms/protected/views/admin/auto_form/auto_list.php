<form id="pagerForm" method="post" action="<?php echo Yii::app()->request->requestUri?>">
	<input type="hidden" name="pageNum" value="1" />
	<input type="hidden" name="numPerPage" value="${model.numPerPage}" />
	<input type="hidden" name="orderField" value="${param.orderField}" />
	<input type="hidden" name="orderDirection" value="${param.orderDirection}" />
	<?php
		// 有搜索提交的post数据
		if($arrAjaxPagePost){
			foreach($arrAjaxPagePost as $k=>$v){
				$key	= "{$strFName}[{$k}]";
				echo "<input type='hidden' name='{$key}' value='{$v}' />";			
			}
		}
	?>
</form>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
		<?php
			// 取得自定义的列表操作
			$arrAutoFormOpe = array();
			$arrAutoFormOpe = CF::getAutoFormOpe($tid, 'curd');
//print_r($arrAutoFormOpe);exit;
			// 添加
			if($arrAutoFormOpe['add'] && $arrAutoFormOpe['add'] == 'no'){
				// 关闭添加
			}else{
				?>
				<li><a class="add" href="/admin/auto_form/auto_add/tid/<?php echo $tid; ?>/type/<?php echo $type; ?><?php echo $strTypeParma; ?>" target="dialog" mask="true" width="800" height="500"><span>添加</span></a></li>
				<?php
			}

			// 编辑
			if($arrAutoFormOpe['update'] && $arrAutoFormOpe['update'] == 'no'){
				// 关闭修改
			}else{
				?>
				<li><a class="edit" href="/admin/auto_form/auto_edit/tid/<?php echo $tid; ?>/id/{did}" target="dialog" mask="true" width="800" height="500" warn="请选择一条数据再进行操作！"><span>修改</span></a></li>
				<?php
			}

			// 批量删除
			if($arrAutoFormOpe['delete'] && $arrAutoFormOpe['delete'] == 'no'){
				// 关闭删除
			}else{
				?>
				<li><a class="delete" id='batchDel' onclick="return batchDel();" href="#" target="ajaxTodo" title="确定要删除吗?"><span>批量删除</span></a></li>
				<?php
			}

			// 高级检索
			if($arrAutoFormOpe['high_search'] && $arrAutoFormOpe['high_search'] == 'no'){
				// 关闭高级检索
			}else{
				?>
				<li><a class="add" href="/admin/auto_form/auto_high_search/tid/<?php echo $tid; ?>/type/<?php echo $type; ?><?php echo $strTypeParma; ?>" target="dialog" mask="true" title="查询框"><span>高级检索</span></a></li>
				<?php
			}

            // 添加其他的自定义操作
            if($arrAutoFormOpe['list']){
                foreach($arrAutoFormOpe['list'] as $v)
                {
                    echo "<li>{$v}</li>";
                }
			}
		?>
		</ul>
	</div>
	<table class="table" width='100%' layoutH="75">
		<thead>
			<tr align='center'>
				<th width="30"><input type="checkbox" group="ids" class="checkboxCtrl"></th>
				<?php
					if($objField){
						foreach($objField as $field){
							echo "<th field_name='{$field->physics_name}'>{$field->logic_name}</th>";
						}
					}
				?>
				<?php
					$strView = $arrAutoFormOpe['view'];
					if($strView && $strView == 'no_ope'){					
					}else{
						echo '<th>操作</th>';
					}
				?>				
			</tr>
		</thead>
		<tbody>
			<?php
				if($objField && $objLData){

					// 取得自定义的列表操作
					$arrAutoFormOpe = array();
					$arrAutoFormOpe = CF::getAutoFormOpe($tid, 'list');
					
					// 子类数据
					$arrAFOpe = array();
					$arrAFOpe = CF::getAutoFormOpe($tid, 'show_style');

					foreach($objLData as $data){
						// 正常输出列表
						CF::echoAutoFormList($tid, $objField, $data, $arrAutoFormOpe, $strView);

						// 输出子类列表
						if($arrAFOpe && $arrAFOpe['sub_type_data']){
							CF::echoSubAutoFormList($tid, $objField, $data, $arrAutoFormOpe, $strView);
						}
                    }

                    //=========== 汇总数据(current)
                    $arrDataCount = array();    // 汇总数据数组
                    $arrDataDeal  = array();    // 需要特殊处理的字段
                    $arrDCOpe     = array();
                    $arrDCOpe     = CF::getAutoFormOpe($tid, 'data_count');
                    if(isset($arrDCOpe['current']) && $arrDCOpe['current']){
                        foreach($objLData as $data){
                            foreach($objField as $v){
                                if($v->extra_data){
                                    $arrT = array();
                                    $arrT = CF::funGetExtraData($v->extra_data);
                                    
                                    // 需要汇总
                                    if(isset($arrT['is_current_data_count']) && $arrT['is_current_data_count']){
                                        $k = $v->physics_name;
                                        $arrDataCount[$k] += $data->$k;
                                    }

                                    // 需要处理
                                    if(isset($arrT['current_func']) && $arrT['current_func']){
                                        if(!isset($arrDataDeal[$k]))
                                            $arrDataDeal[$k] = $arrT['current_func'];
                                    }
                                }
                            } 
                        }
                    }

                    if($arrDataCount)
                    {
                        echo '<tr align="center">';
                        echo '<td style="color:red;">总计</td>';
                        $rowData = $arrDataCount;
                        
                        foreach($objField as $v){
                            $strT = '&nbsp;';

                            // 汇总数据
                            if(isset($arrDataCount[$v->physics_name]))
                                $strT = $arrDataCount[$v->physics_name];

                            // 特殊处理
                            if(isset($arrDataDeal[$v->physics_name])){
                                $func = $arrDataDeal[$v->physics_name];
					            $strDeal = "\$strT = {$func};";
					            eval($strDeal);
                            }

							echo "<td>{$strT}</td>";
						}

                        echo '</tr>';
                    }
                    //========== 汇总数据(current) end
				}
			?>
		</tbody>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>共<?php echo $pages->itemCount?>条</span>
		</div>		
		<div class="pagination" targetType="navTab" totalCount="<?php echo $pages->itemCount?>" numPerPage="<?php echo $pages->pageSize?>" pageNumShown="10" currentPage="<?php echo $pages->currentPage + 1?>"></div>
	</div>
</div>

<script>
function getIds()
{
    var strIds = '';
    var obj = document.getElementsByName("ids");
	for(var i = 0; i < obj.length; i++) 
	{ 
		if(obj[i].checked){ 
			strIds += strIds == '' ? obj[i].value : ',' + obj[i].value ;
		} 
    }

    return strIds;
}

function doForeach(id, strUrl)
{
    var strIds = getIds();
    strUrl += strIds;
	$('#'+id, navTab.getCurrentPanel()).attr("href", strUrl);

	return true;
}

function batchDel(){
	var strDUrl = '/admin/auto_form/auto_batch_delete/tid/<?php echo $tid; ?>/ids/';
	var strIds = '';
	
	/*
	$("input[name='ids'][checked]", navTab.getCurrentPanel()).each(function(){
		strIds += strIds == '' ? $(this).val() : ',' + $(this).val() ;
		alert('');
	});
	*/
	var obj = document.getElementsByName("ids");
	for(var i = 0; i < obj.length; i++) 
	{ 
		if(obj[i].checked){ 
			strIds += strIds == '' ? obj[i].value : ',' + obj[i].value ;
		} 
	}

	strDUrl += strIds;
	$('#batchDel', navTab.getCurrentPanel()).attr("href", strDUrl);

	return true;
}
</script>
