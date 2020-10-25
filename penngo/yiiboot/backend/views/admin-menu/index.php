
<?php
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use backend\models\AdminMenu;
use yii\helpers\Url;
$modelLabel = new \backend\models\AdminMenu();
?>

<?php $this->beginBlock('header');  ?>
<!-- <head></head>中代码块 -->
<?php $this->endBlock(); ?>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
      
        <div class="box-header">
          <h3 class="box-title">子菜单管理</h3>
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
          <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <!-- row start search-->
          	<div class="row">
          	<div class="col-sm-12">
                <?php ActiveForm::begin(['id' => 'admin-menu-search-form', 'method'=>'get', 'options' => ['class' => 'form-inline'], 'action'=>Url::toRoute('admin-menu/index')]); ?>
                  <input type="hidden" id="mid" name="mid" value="<?=$module_id?>" />
                  <div class="form-group" style="margin: 5px;">
                      <label><?=$modelLabel->getAttributeLabel('id')?>:</label>
                      <input type="text" class="form-control" id="query[id]" name="query[id]"  value="<?=isset($query["id"]) ? $query["id"] : "" ?>">
                  </div>

                  <div class="form-group" style="margin: 5px;">
                      <label><?=$modelLabel->getAttributeLabel('entry_url')?>:</label>
                      <input type="text" class="form-control" id="query[entry_url]" name="query[entry_url]"  value="<?=isset($query["entry_url"]) ? $query["entry_url"] : "" ?>">
                  </div>
              <div class="form-group">
              	<a onclick="searchAction()" class="btn btn-primary btn-sm" href="#"> <i class="glyphicon glyphicon-zoom-in icon-white"></i>搜索</a>
           	  </div>
               <?php ActiveForm::end(); ?> 
            </div>
          	</div>
          	<!-- row end search -->
          	
          	<!-- row start -->
          	<div class="row">
          	<div class="col-sm-12">
          	<table id="data_table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="data_table_info">
            <thead>
            <tr role="row">
            
            <?php 
		      echo '<th><input id="data_table_check" type="checkbox"></th>';
              echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('id').'</th>';
              echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('code').'</th>';
              echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('menu_name').'</th>';
//               echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('module_id').'</th>';
//               echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('display_label').'</th>';
              echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('display_order').'</th>';
//               echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('entry_right_name').'</th>';
              echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('entry_url').'</th>';
              echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('update_user').'</th>';
              echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('update_date').'</th>';
         
			?>
	
            <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >操作</th>
            </tr>
            </thead>
            <tbody>
            
            <?php
            $row = 0;
            foreach ($models as $model) {
                echo '<tr id="rowid_' . $model->id . '">';
                echo '  <td><label><input type="checkbox" value="' . $model->id . '"></label></td>';
                echo '  <td>' . $model->id . '</td>';
                echo '  <td>' . $model->code . '</td>';
                echo '  <td>' . $model->menu_name . '</td>';
//                 echo '  <td>' . $model->module_id . '</td>';
//                 echo '  <td>' . $model->display_label . '</td>';
                //echo '  <td>' . $model->des . '</td>';
                echo '  <td>' . $model->display_order . '</td>';
//                 echo '  <td>' . $model->entry_right_name . '</td>';
                echo '  <td>' . $model->entry_url . '</td>';
                //echo '  <td>' . $model->action . '</td>';
                //echo '  <td>' . $model->controller . '</td>';
                //echo '  <td>' . $model->has_lef . '</td>';
                //echo '  <td>' . $model->create_user . '</td>';
                //echo '  <td>' . $model->create_date . '</td>';
                echo '  <td>' . $model->update_user . '</td>';
                echo '  <td>' . $model->update_date . '</td>';
                echo '  <td class="center">';
                echo '      <a id="view_btn" class="btn btn-primary btn-sm" href="'.Url::toRoute(['admin-right/index','id'=>$model->id]).'"> <i class="glyphicon glyphicon-zoom-in icon-white"></i>路由管理</a>';
                echo '      <a id="view_btn" onclick="viewAction(' . $model->id . ')" class="btn btn-primary btn-sm" href="#"> <i class="glyphicon glyphicon-zoom-in icon-white"></i>查看</a>';
                echo '      <a id="edit_btn" onclick="editAction(' . $model->id . ')" class="btn btn-primary btn-sm" href="#"> <i class="glyphicon glyphicon-edit icon-white"></i>修改</a>';
                echo '      <a id="delete_btn" onclick="deleteAction(' . $model->id . ')" class="btn btn-danger btn-sm" href="#"> <i class="glyphicon glyphicon-trash icon-white"></i>删除</a>';
                echo '  </td>';
                echo '<tr/>';
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
            		从<?= $pages->getPage() * $pages->getPageSize() + 1 ?>            		到 <?= ($pageCount = ($pages->getPage() + 1) * $pages->getPageSize()) < $pages->totalCount ?  $pageCount : $pages->totalCount?>            		 共 <?= $pages->totalCount?> 条记录</div>
            	</div>
            </div>
          	<div class="col-sm-7">
              	<div class="dataTables_paginate paging_simple_numbers" id="data_table_paginate">
              	<?= LinkPager::widget([
              	    'pagination' => $pages,
              	    'nextPageLabel' => '下一页',
              	    'prevPageLabel' => '上一页',
              	    'firstPageLabel' => '首页',
              	    'lastPageLabel' => '尾页',
              	]); ?>	
              	
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
				<h3>子菜单管理</h3>
			</div>
			<div class="modal-body">
                <?php $form = ActiveForm::begin(["id" => "admin-menu-form", "class"=>"form-horizontal", "action"=>Url::toRoute('admin-menu/save')]); ?>                      
                 <input type="hidden" class="form-control" id="id" name="AdminMenu[id]" />
                 <input type="hidden" class="form-control" id="module_id" name="AdminMenu[module_id]" value="<?=$module_id?>">                    
          

          <div id="code_div" class="form-group">
              <label for="code" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("code")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="code" name="AdminMenu[code]" placeholder="必填" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="menu_name_div" class="form-group">
              <label for="menu_name" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("menu_name")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="menu_name" name="AdminMenu[menu_name]" placeholder="必填" />
              </div>
              <div class="clearfix"></div>
          </div>
<!-- 
          <div id="module_id_div" class="form-group">
              <label for="module_id" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("module_id")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="module_id" name="AdminMenu[module_id]" placeholder="必填" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="display_label_div" class="form-group">
              <label for="display_label" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("display_label")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="display_label" name="AdminMenu[display_label]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>
 -->
          <div id="des_div" class="form-group">
              <label for="des" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("des")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="des" name="AdminMenu[des]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="display_order_div" class="form-group">
              <label for="display_order" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("display_order")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="display_order" name="AdminMenu[display_order]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>
<!-- 
          <div id="entry_right_name_div" class="form-group">
              <label for="entry_right_name" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("entry_right_name")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="entry_right_name" name="AdminMenu[entry_right_name]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>
 -->
          <div id="entry_url_div" class="form-group">
              <label for="entry_url" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("entry_url")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="entry_url" name="AdminMenu[entry_url]" placeholder="必填" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="controller_div" class="form-group">
              <label for="controller" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("controller")?></label>
              <div class="col-sm-10">
              	<select class="form-control" name="AdminMenu[controller]" id="controller">
					<option>请选择</option>
    				<?php 	   
					  foreach($controllerData as $key=>$data){
					      echo "<option value='" . $key . "'>". $key."</option>";
					  }
					?>
        	    </select>
              </div>
              <div class="clearfix"></div>
          </div>
          
          <div id="action_div" class="form-group">
              <label for="action" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("action")?></label>
              <div class="col-sm-10">
              <select class="form-control" name="AdminMenu[action]" id="action">
    			<option>请选择</option>
              </select>
<!-- <input type="text" class="form-control" id="action" name="AdminMenu[action]" placeholder="必填" />  -->
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="has_lef_div" class="form-group">
              <label for="has_lef" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("has_lef")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="has_lef" name="AdminMenu[has_lef]" placeholder="必填" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="create_user_div" class="form-group">
              <label for="create_user" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("create_user")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="create_user" name="AdminMenu[create_user]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="create_date_div" class="form-group">
              <label for="create_date" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("create_date")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="create_date" name="AdminMenu[create_date]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="update_user_div" class="form-group">
              <label for="update_user" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("update_user")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="update_user" name="AdminMenu[update_user]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="update_date_div" class="form-group">
              <label for="update_date" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("update_date")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="update_date" name="AdminMenu[update_date]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>
                    

			<?php ActiveForm::end(); ?>          
                </div>
			<div class="modal-footer">
				<a href="#" class="btn btn-default" data-dismiss="modal">关闭</a> <a
					id="edit_dialog_ok" href="#" class="btn btn-primary">确定</a>
			</div>
		</div>
	</div>
</div>
<?php $this->beginBlock('footer');  ?>
<!-- <body></body>后代码块 -->
 <script>
 window.controllerData = <?=json_encode($controllerData); ?>;
 function searchAction(){
		$('#admin-menu-search-form').submit();
	}
 function viewAction(id){
		initModel(id, 'view', 'fun');
	}

 function initEditSystemModule(data, type){
	if(type == 'create'){
		$("#id").val('');
		$("#code").val('');
		$("#menu_name").val('');
// 		$("#module_id").val('');
// 		$("#display_label").val('');
		$("#des").val('');
		$("#display_order").val('');
// 		$("#entry_right_name").val('');
		$("#entry_url").val('');
		$("#action").val('');
		$("#controller").val('');
		$("#has_lef").val('');
		$("#create_user").val('');
		$("#create_date").val('');
		$("#update_user").val('');
		$("#update_date").val('');
		
	}
	else{
		$("#id").val(data.id);
    	$("#code").val(data.code);
    	$("#menu_name").val(data.menu_name);
    	$("#module_id").val(data.module_id);
//     	$("#display_label").val(data.display_label);
    	$("#des").val(data.des);
    	$("#display_order").val(data.display_order);
//     	$("#entry_right_name").val(data.entry_right_name);
    	$("#entry_url").val(data.entry_url);
    	$("#action").val(data.action);
    	$("#controller").val(data.controller);
    	$("#has_lef").val(data.has_lef);
    	$("#create_user").val(data.create_user);
    	$("#create_date").val(data.create_date);
    	$("#update_user").val(data.update_user);
    	$("#update_date").val(data.update_date);
    	}
	if(type == "view"){
      $("#id").attr({readonly:true,disabled:true});
      $("#code").attr({readonly:true,disabled:true});
      $("#menu_name").attr({readonly:true,disabled:true});
      $("#module_id").attr({readonly:true,disabled:true});
//       $("#display_label").attr({readonly:true,disabled:true});
      $("#des").attr({readonly:true,disabled:true});
      $("#display_order").attr({readonly:true,disabled:true});
//       $("#entry_right_name").attr({readonly:true,disabled:true});
      $("#entry_url").attr({readonly:true,disabled:true});
      $("#action").attr({readonly:true,disabled:true});
      $("#controller").attr({readonly:true,disabled:true});
      $("#has_lef").attr({readonly:true,disabled:true});
      $("#has_lef").parent().parent().show();
      $("#create_user").attr({readonly:true,disabled:true});
      $("#create_user").parent().parent().show();
      $("#create_date").attr({readonly:true,disabled:true});
      $("#create_date").parent().parent().show();
      $("#update_user").attr({readonly:true,disabled:true});
      $("#update_user").parent().parent().show();
      $("#update_date").attr({readonly:true,disabled:true});
      $("#update_date").parent().parent().show();
	$('#edit_dialog_ok').addClass('hidden');
	}
	else{
      $("#id").attr({readonly:false,disabled:false});
      $("#code").attr({readonly:false,disabled:false});
      $("#menu_name").attr({readonly:false,disabled:false});
      $("#module_id").attr({readonly:false,disabled:false});
//       $("#display_label").attr({readonly:false,disabled:false});
      $("#des").attr({readonly:false,disabled:false});
      $("#display_order").attr({readonly:false,disabled:false});
//       $("#entry_right_name").attr({readonly:false,disabled:false});
      $("#entry_url").attr({readonly:true,disabled:true});
//       $("#entry_url").attr({readonly:false,disabled:false});
      $("#action").attr({readonly:false,disabled:false});
      $("#controller").attr({readonly:false,disabled:false});
      $("#has_lef").attr({readonly:false,disabled:false});
      $("#has_lef").parent().parent().hide();
      $("#create_user").attr({readonly:false,disabled:false});
      $("#create_user").parent().parent().hide();
      $("#create_date").attr({readonly:false,disabled:false});
      $("#create_date").parent().parent().hide();
      $("#update_user").attr({readonly:false,disabled:false});
      $("#update_user").parent().parent().hide();
      $("#update_date").attr({readonly:false,disabled:false});
      $("#update_date").parent().parent().hide();
		$('#edit_dialog_ok').removeClass('hidden');
		}
		$('#edit_dialog').modal('show');
}

function initModel(id, type, fun){
	
	$.ajax({
		   type: "GET",
		   url: "<?=Url::toRoute('admin-menu/view')?>",
		   data: {"id":id},
		   cache: false,
		   dataType:"json",
		   error: function (xmlHttpRequest, textStatus, errorThrown) {
			    alert("出错了，" + textStatus);
			},
		   success: function(data){
			   initEditSystemModule(data, type);
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
				   url: "<?=Url::toRoute('admin-menu/delete')?>",
				   data: {"ids":ids},
				   cache: false,
				   dataType:"json",
				   error: function (xmlHttpRequest, textStatus, errorThrown) {
					    alert("出错了，" + textStatus);
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
	$('#admin-menu-form').submit();
});

$('#create_btn').click(function (e) {
    e.preventDefault();
    initEditSystemModule({}, 'create');
});

$('#delete_btn').click(function (e) {
    e.preventDefault();
    deleteAction('');
});

$('#admin-menu-form').bind('submit', function(e) {
	e.preventDefault();
	var id = $("#id").val();
	var action = id == "" ? "<?=Url::toRoute('admin-menu/create')?>" : "<?=Url::toRoute('admin-menu/update')?>";
    $(this).ajaxSubmit({
    	type: "post",
    	dataType:"json",
    	url: action,
    	data:{id:id},
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
$("#controller").change(function(){
    // 先清空第二个
	var controller = $(this).val();
     $("#action").empty();
     var option = $("<option>").html("请选择");
     $("#action").append(option);
     var actions = window.controllerData[controller];
     var nodes = actions.nodes;
     for(i = 0; i < nodes.length; i++){
         var action = nodes[i];
         var option = $("<option>").val(action.a).html(action.text);
         $("#action").append(option);
     }
});
 
</script>
<?php $this->endBlock(); ?>