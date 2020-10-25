<?php
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$modelClass = $generator->modelClass;
$mn = explode('\\', $modelClass);
$modelName = array_pop($mn);
$model = new $modelClass();
if(method_exists($model, 'getTableColumnInfo') == false){
    exit('error, ' . get_class($model) . ' no getTableColumnInfo method. can not use common (..\..\vendor\yiisoft\yii2-gii\generators\crud\common) to generated.');
}
$tableColumnInfo = $model->getTableColumnInfo();
$controllerClass = $generator->controllerClass;
$controllerName = substr($controllerClass, 0, strlen($controllerClass) - 10);

?>

<?="<?php\n"?>
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use common\utils\CommonFun;
use yii\helpers\Url;

use <?=$modelClass?>;

$modelLabel = new \<?=$modelClass?>();
?>

<?="<?php \$this->beginBlock('header');  ?>\n"?>
<!-- <head></head>中代码块 -->
<?="<?php \$this->endBlock(); ?>"?>


<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
      
        <div class="box-header">
          <h3 class="box-title">数据列表</h3>
          <div class="box-tools">
            <div class="input-group input-group-sm" style="width: 150px;">
                <button id="create_btn" type="button" class="btn btn-xs btn-primary">添&nbsp;&emsp;加</button>
        			|
        		<button id="delete_btn" type="button" class="btn btn-xs btn-danger">批量删除</button>
            </div>
          </div>
        </div>
        <!-- /.box-header -->
        
        <div class="box-body">
          <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap table-responsive">
            <!-- row start search-->
          	<div class="row">
          	<div class="col-sm-12">
                <?="<?php ActiveForm::begin(['id' => '".Inflector::camel2id(StringHelper::basename($controllerName))."-search-form', 'method'=>'get', 'options' => ['class' => 'form-inline'], 'action'=>Url::toRoute('".Inflector::camel2id(StringHelper::basename($controllerName))."/index')]); ?>"?>     
                <?php foreach($tableColumnInfo as $key=>$column){
                        if($column['isSearch'] === true){
                            echo "\n";
                            echo "                  <div class=\"form-group\" style=\"margin: 5px;\">\n";
                            echo "                      <label><?=\$modelLabel->getAttributeLabel('$key')?>:</label>\n";
                            echo "                      <input type=\"text\" class=\"form-control\" id=\"query[$key]\" name=\"query[$key]\"  value=\"<?=isset(\$query[\"$key\"]) ? \$query[\"$key\"] : \"\" ?>\">\n";
                            echo "                  </div>\n";
                        }
                    }
                
                    ?>
              <div class="form-group">
              	<a onclick="searchAction()" class="btn btn-primary btn-sm" href="#"> <i class="glyphicon glyphicon-zoom-in icon-white"></i>搜索</a>
           	  </div>
               <?="<?php ActiveForm::end(); ?>"?> 
            </div>
          	</div>
          	<!-- row end search -->
          	
          	<!-- row start -->
          	<div class="row">
          	<div class="col-sm-12">
          	<table id="data_table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="data_table_info">
            <thead class="text-nowrap">
            <tr role="row">
            
            <?="<?php \n"?>
              $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';
		      echo '<th><input id="data_table_check" type="checkbox"></th>';
<?php 
foreach($tableColumnInfo as $key=>$column){
if($column['isDisplay'] == true){
echo "              echo '<th onclick=\"orderby(\'$key\', \'desc\')\" '.CommonFun::sortClass(\$orderby, '$key').' tabindex=\"0\" aria-controls=\"data_table\" rowspan=\"1\" colspan=\"1\" aria-sort=\"ascending\" >'.\$modelLabel->getAttributeLabel('$key').'</th>';\n";
}

}
?>         
			?>
	
            <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >操作</th>
            </tr>
            </thead>
            <tbody>
            
            <?= "<?php\n" ?>
            foreach ($models as $model) {
                echo '<tr id="rowid_' . $model->id . '">';
                echo '  <td><label><input type="checkbox" value="' . $model->id . '"></label></td>';
<?php 
foreach($tableColumnInfo as $key=>$column){
    if($column['isDisplay'] === true){
        echo "                echo '  <td>' . \$model->$key . '</td>';\n";
    }
    else{
        echo "                //echo '  <td>' . \$model->$key . '</td>';\n";
    }
}
?>
                echo '  <td class="center">';
                echo '      <a id="view_btn" onclick="viewAction(' . $model->id . ')" class="btn btn-primary btn-sm" href="#"> <i class="glyphicon glyphicon-zoom-in icon-white"></i>查看</a>';
                echo '      <a id="edit_btn" onclick="editAction(' . $model->id . ')" class="btn btn-primary btn-sm" href="#"> <i class="glyphicon glyphicon-edit icon-white"></i>修改</a>';
                echo '      <a id="delete_btn" onclick="deleteAction(' . $model->id . ')" class="btn btn-danger btn-sm" href="#"> <i class="glyphicon glyphicon-trash icon-white"></i>删除</a>';
                echo '  </td>';
                echo '</tr>';
            }
            
            ?>
            
           
           
            </tbody>
            <!-- <tfoot></tfoot> -->
          </table>
          </div>
          </div>
          <!-- row end -->
          
          <!-- row start -->
          <div class="row">
          	<div class="col-sm-5">
            	<div class="dataTables_info" id="data_table_info" role="status" aria-live="polite">
            		<div class="infos">
            		从<?= "<?= \$pages->getPage() * \$pages->getPageSize() + 1 ?>"?>
            		到 <?= "<?= (\$pageCount = (\$pages->getPage() + 1) * \$pages->getPageSize()) < \$pages->totalCount ?  \$pageCount : \$pages->totalCount?>" ?>
            		 共 <?= "<?= \$pages->totalCount?>" ?> 条记录</div>
            	</div>
            </div>
          	<div class="col-sm-7">
              	<div class="dataTables_paginate paging_simple_numbers" id="data_table_paginate">
              	<?="<?= LinkPager::widget([
              	    'pagination' => \$pages,
              	    'nextPageLabel' => '»',
              	    'prevPageLabel' => '«',
              	    'firstPageLabel' => '首页',
              	    'lastPageLabel' => '尾页',
              	]); ?>	\n"?>
              	
              	</div>
          	</div>
		  </div>
		  <!-- row end -->
        </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>
<!-- /.content -->

<div class="modal fade" id="edit_dialog" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>Settings</h3>
			</div>
			<div class="modal-body">
                <?='<?php $form = ActiveForm::begin(["id" => "'.Inflector::camel2id(StringHelper::basename($controllerName)).'-form", "class"=>"form-horizontal", "action"=>Url::toRoute("'.Inflector::camel2id(StringHelper::basename($controllerName)).'/save")]); ?>'?>                      
                 <?php foreach($tableColumnInfo as $key=>$column){
                    echo "\n";
                    $isNeed = $column['allowNull'] == false ? '必填' : '';
                    //$widget = '';
                    $widget = '          <div id="'.$key.'_div" class="form-group">'."\n";
                    $widget.= '              <label for="'.$key.'" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("'.$key.'")?></label>'."\n";
                    $widget.= '              <div class="col-sm-10">'."\n";
                    $widget.= '==widget==';
                    $widget.= '              </div>'."\n";
                    $widget.= '              <div class="clearfix"></div>'."\n";
                    $widget.= '          </div>'."\n";
                    
                    switch($column['inputType']){
                        case 'hidden':
                            if($column['isPrimaryKey'] == true){
                                $inputWidget = '          <input type="hidden" class="form-control" id="'.$key.'" name="'.$key.'" />'."\n";
                            }
                            else{
                                $inputWidget = '          <input type="hidden" class="form-control" id="'.$key.'" name="'.$modelName.'['.$key.']" />'."\n";
                            }
                            echo $inputWidget;
                            break;
                        case 'text':
                            $inputWidget = '                  <input type="text" class="form-control" id="'.$key.'" name="'.$modelName.'['.$key.']" placeholder="'.$isNeed . '" />'."\n";
                            echo str_replace('==widget==', $inputWidget, $widget);
                            break;
                        case 'select':
                            //UdcCode::$YES_NO
                            $inputWidget = '<select class="form-control" name="'.$modelName.'['.$key.']" id="'.$key.'">';
                            $udc = $column['udc'];
                            $code = str_ireplace('UdcCode::$', '', $udc);
                            $vars = get_class_vars('common\utils\UdcCode');
                            if(empty($vars[$code]) == false){
                                foreach($vars[$code] as $k=>$v){
                                    $inputWidget .= "<option value=\"{$k}\">{$v}</option>";
                                }
                            }
                    		$inputWidget .= '</select>';
          		  			echo str_replace('==widget==', $inputWidget, $widget);
                            break;
                        case 'radio':
                            $udc = $column['udc'];
                            $code = str_ireplace('UdcCode::$', '', $udc);
                            $vars = get_class_vars('common\utils\UdcCode');
                            $inputWidget = '';
                            if(empty($vars[$code]) == false){
                                foreach($vars[$code] as $k=>$v){
                                    $inputWidget .= '<label class="radio-inline">' .
                                        '<input type="radio" name="'.$modelName.'['.$key.']" id="'.$key.'" value="'.$k.'">' . $v .
          	                                       '</label>'."\n";
                                }
                            }
                            echo str_replace('==widget==', $inputWidget, $widget);
                            break;
                        case 'checkbox':
                            $udc = $column['udc'];
                            $code = str_ireplace('UdcCode::$', '', $udc);
                            $vars = get_class_vars('common\utils\UdcCode');
                            $inputWidget = '';
                            if(empty($vars[$code]) == false){
                                foreach($vars[$code] as $k=>$v){
                                    $inputWidget .= '<label class="checkbox-inline">' .
                                        '<input type="checkbox" name="'.$modelName.'['.$key.'][]" id="inlineCheckbox1" value="'.$k.'">'. $v .
                                        '</label>' . "\n";
                                }
                            }
                            echo str_replace('==widget==', $inputWidget, $widget);
                            break;
                            
                        case 'file':
                            $inputWidget = '<input type="hidden" class="form-control" id="'.$key.'" name="'.$modelName.'['.$key.']" />' .
                                           '<input id="'.$key.'_file" type="file" name="'.$key.'_file" class="file" data-overwrite-initial="true" data-min-file-count="1">';
                            echo str_replace('==widget==', $inputWidget, $widget);
                            break;
                        case 'password':
                            $inputWidget = '<input name="password" id="password" type="password" class="form-control"';
                            echo str_replace('==widget==', $inputWidget, $widget);
                            break;
                    }
                 }
                 ?>
                    

			<?= "<?php ActiveForm::end(); ?>" ?>          
                </div>
			<div class="modal-footer">
				<a href="#" class="btn btn-default" data-dismiss="modal">关闭</a> <a
					id="edit_dialog_ok" href="#" class="btn btn-primary">确定</a>
			</div>
		</div>
	</div>
</div>
<?="<?php \$this->beginBlock('footer');  ?>\n"?>
<!-- <body></body>后代码块 -->
 <script>
function orderby(field, op){
	 var url = window.location.search;
	 var optemp = field + " desc";
	 if(url.indexOf('orderby') != -1){
		 url = url.replace(/orderby=([^&?]*)/ig,  function($0, $1){ 
			 var optemp = field + " desc";
			 optemp = decodeURI($1) != optemp ? optemp : field + " asc";
			 return "orderby=" + optemp;
		   }); 
	 }
	 else{
		 if(url.indexOf('?') != -1){
			 url = url + "&orderby=" + encodeURI(optemp);
		 }
		 else{
			 url = url + "?orderby=" + encodeURI(optemp);
		 }
	 }
	 window.location.href=url; 
 }
 function searchAction(){
		$('#<?=Inflector::camel2id(StringHelper::basename($controllerName))?>-search-form').submit();
	}
 function viewAction(id){
		initModel(id, 'view', 'fun');
	}

 function initEditSystemModule(data, type){
	if(type == 'create'){
    	<?php 
    	foreach($tableColumnInfo as $key=>$column){
        	switch ($column['inputType']){
        	    case 'select':
        	        echo '        $("#'.$key.'").prop("selectedIndex", 0)'."\n";
        	        break;
        	    case 'radio': 
        	        echo '        $("input[name=\''.$modelName . '['.$key.']\']").removeAttr("checked");'."\n";
        		    break;
        	    case 'checkbox':
        	        echo '        $("input[name=\''.$modelName . '['.$key.'][]\']").removeAttr("checked");'."\n";
        	        break;
        		default:
        			echo '        $("#'.$key.'").val("");'."\n";
        	}
    	}
    	?>
	
	}
	else{
    	<?php foreach($tableColumnInfo as $key=>$column){
            switch ($column['inputType']){
                case 'radio':
                    echo '        $("input[name=\''.$modelName . '['.$key.']\'][value=\'" + data.'.$key.' + "\']").attr("checked","true");'."\n";
                    break;
                case 'checkbox': 
                    echo '        $("input[name=\''.$modelName . '['.$key.'][]\']").removeAttr("checked");'."\n";
                    echo "        if(!!data.{$key}){ \n";
                    echo "            data.{$key} = $.parseJSON(data.{$key});\n";
                    echo "            $.each(data.{$key}, function(i, v){\n";
                    echo '                $("input[name=\''.$modelName . '['.$key.'][]\'][value=\'"+ v + "\']").prop("checked",true);'."\n";
                    echo "         });\n";
                    echo "        }\n";
                    break;
                default:
                    echo '        $("#'.$key.'").val(data.'.$key.')'."\n";
            }
        }
        ?>
	}
	if(type == "view"){
<?php 

foreach($tableColumnInfo as $key=>$column){
    echo '      $("#'.$key.'").attr({readonly:true,disabled:true});'."\n";
    if($column['isEdit'] === false){
    echo '      $("#'.$key.'").parent().parent().show();'."\n";
    
    }
}

?>
	$('#edit_dialog_ok').addClass('hidden');
	}
	else{
<?php 

foreach($tableColumnInfo as $key=>$column){
    echo '      $("#'.$key.'").attr({readonly:false,disabled:false});'."\n";
    if($column['isEdit'] === false){
    echo '      $("#'.$key.'").parent().parent().hide();'."\n";

    }
}

?>
		$('#edit_dialog_ok').removeClass('hidden');
		}
		$('#edit_dialog').modal('show');
}

function initModel(id, type, fun){
	
	$.ajax({
		   type: "GET",
		   url: "<?="<?=Url::toRoute('".Inflector::camel2id(StringHelper::basename($controllerName))."/view')?>"?>",
		   data: {"id":id},
		   cache: false,
		   dataType:"json",
		   error: function (xmlHttpRequest, textStatus, errorThrown) {
			    alert("出错了，" + textStatus);
			},
		   success: function(data){
			   initEditSystemModule(data, type);

			   ////////////////////////////////////////
   <?php 
	   foreach($tableColumnInfo as $key=>$column){
	       
	       switch($column['inputType']){
	           case 'file':
	               //////////////// $modelName
	               ?>
	               $("#<?=$key?>_file").fileinput('destroy').fileinput({
	                   language: 'zh', //设置语言
	                   uploadUrl: '<?="<?=Url::toRoute('test-user/upload-images')?>"?>',
	                   allowedFileExtensions: ['jpg', 'png', 'gif', 'docx'], // 允许上传的文件类型
	                   initialPreviewAsData: true,
	                   showUpload : false,
	                   dropZoneEnabled:false,
	                   previewSettings: {
	                       image: {width: "100px", height: "100px"},
	                   },
	                   uploadExtraData: function(previewId, index) {   //额外参数的关键点
	                       var obj = {_csrf:$("[name='_csrf']").val()};
	                       return obj;
	                   },
	                   deleteExtraData:function(previewId, index){
	                       var obj = {_csrf:$("[name='_csrf']").val()};
	                       return obj;
	                   },
	                   initialPreview: data.initialPreview,
	                   initialPreviewConfig: data.initialPreviewConfig,
	               })
	               .on('fileselect', function(event, numFiles, label) {
	            	   $(this).fileinput("upload");
	                   // 只考虑上传一个文件
	                   if($('#<?=$key?>_div .kv-preview-thumb').size() > 1){
	                       $('#<?=$key?>_div .kv-preview-thumb').first().remove();
	                   }
	               })
	               .on("fileuploaded", function(event, data,previewId,index){
	                   var result = data.response;
	                   $("#<?=$key?>").val(result.image);
	               })
	               .on('filedeleted', function(event, key, jqXHR, data) {
	                   // 只考虑上传一个文件
	                   if($('#<?=$key?>_div .kv-preview-thumb').size() == 0){
	                       $("#<?=$key?>").val('');
	                   }
	               })
	               .on('filesuccessremove', function(event, previewId) {
	                   $("#<?=$key?>").val('');
	               });
				<?php 
	               break;
	       }
	   }
	?>
		   }
		});
}
	
function editAction(id){
	initModel(id, 'edit');
}

function deleteAction(id){
	var ids = [];
	if(!!id == true){
		ids[0] = id;
	}
	else{
		var checkboxs = $('#data_table :checked');
	    if(checkboxs.size() > 0){
	        var c = 0;
	        for(i = 0; i < checkboxs.size(); i++){
	            var id = checkboxs.eq(i).val();
	            if(id != ""){
	            	ids[c++] = id;
	            }
	        }
	    }
	}
	if(ids.length > 0){
		admin_tool.confirm('请确认是否删除', function(){
		    $.ajax({
				   type: "GET",
				   url: "<?="<?=Url::toRoute('".Inflector::camel2id(StringHelper::basename($controllerName))."/delete')?>"?>",
				   data: {"ids":ids},
				   cache: false,
				   dataType:"json",
				   error: function (xmlHttpRequest, textStatus, errorThrown) {
					    admin_tool.alert('msg_info', '出错了，' + textStatus, 'warning');
					},
				   success: function(data){
					   for(i = 0; i < ids.length; i++){
						   $('#rowid_' + ids[i]).remove();
					   }
					   admin_tool.alert('msg_info', '删除成功', 'success');
					   window.location.reload();
				   }
				});
		});
	}
	else{
		admin_tool.alert('msg_info', '请先选择要删除的数据', 'warning');
	}
    
}

function getSelectedIdValues(formId)
{
	var value="";
	$( formId + " :checked").each(function(i)
	{
		if(!this.checked)
		{
			return true;
		}
		value += this.value;
		if(i != $("input[name='id']").size()-1)
		{
			value += ",";
		}
	 });
	return value;
}

$('#edit_dialog_ok').click(function (e) {
    e.preventDefault();
	$('#<?=Inflector::camel2id(StringHelper::basename($controllerName))?>-form').submit();
});

$('#create_btn').click(function (e) {
    e.preventDefault();
    initEditSystemModule({}, 'create');
});

$('#delete_btn').click(function (e) {
    e.preventDefault();
    deleteAction('');
});

$('#<?=Inflector::camel2id(StringHelper::basename($controllerName))?>-form').bind('submit', function(e) {
	e.preventDefault();
	var id = $("#id").val();
	var action = id == "" ? "<?="<?=Url::toRoute('".Inflector::camel2id(StringHelper::basename($controllerName))."/create')?>"?>" : "<?="<?=Url::toRoute('".Inflector::camel2id(StringHelper::basename($controllerName))."/update')?>"?>";
    $(this).ajaxSubmit({
    	type: "post",
    	dataType:"json",
    	url: action,
    	success: function(value) 
    	{
        	if(value.errno == 0){
        		$('#edit_dialog').modal('hide');
        		admin_tool.alert('msg_info', '添加成功', 'success');
        		window.location.reload();
        	}
        	else{
            	var json = value.data;
        		for(var key in json){
        			$('#' + key).attr({'data-placement':'bottom', 'data-content':json[key], 'data-toggle':'popover'}).addClass('popover-show').popover('show');
        			
        		}
        	}

    	}
    });
});

</script>
<?="<?php \$this->endBlock(); ?>"?>
