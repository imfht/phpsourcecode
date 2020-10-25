
<?php
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use backend\models\AdminRole;
use yii\helpers\Url;
$modelLabel = new \backend\models\AdminRole();
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
          <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <!-- row start search-->
          	<div class="row">
          	<div class="col-sm-12">
                <?php ActiveForm::begin(['id' => 'admin-role-search-form', 'method'=>'get', 'options' => ['class' => 'form-inline'], 'action'=>Url::toRoute('admin-role/index')]); ?>     
                
                  <div class="form-group" style="margin: 5px;">
                      <label><?=$modelLabel->getAttributeLabel('id')?>:</label>
                      <input type="text" class="form-control" id="query[id]" name="query[id]"  value="<?=isset($query["id"]) ? $query["id"] : "" ?>">
                  </div>

                  <div class="form-group" style="margin: 5px;">
                      <label><?=$modelLabel->getAttributeLabel('name')?>:</label>
                      <input type="text" class="form-control" id="query[name]" name="query[name]"  value="<?=isset($query["name"]) ? $query["name"] : "" ?>">
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
              echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('name').'</th>';
              echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('update_user').'</th>';
              echo '<th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('update_date').'</th>';
         
			?>
	
            <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >操作</th>
            </tr>
            </thead>
            <tbody>
            
            <?php
            foreach ($models as $model) {
                echo '<tr id="rowid_' . $model->id . '">';
                echo '  <td><label><input type="checkbox" value="' . $model->id . '"></label></td>';
                echo '  <td>' . $model->id . '</td>';
                echo '  <td>' . $model->code . '</td>';
                echo '  <td>' . $model->name . '</td>';
                //echo '  <td>' . $model->des . '</td>';
                //echo '  <td>' . $model->create_user . '</td>';
                //echo '  <td>' . $model->create_date . '</td>';
                echo '  <td>' . $model->update_user . '</td>';
                echo '  <td>' . $model->update_date . '</td>';
                echo '  <td class="center">';
                echo '      <a id="view_btn" class="btn btn-primary btn-sm" href="'.Url::toRoute(['admin-user-role/index', 'roleId'=>$model->id]).'"> <i class="glyphicon glyphicon-zoom-in icon-white"></i>分配用户</a>';
                echo '      <a id="view_btn" onclick="rightAction('.$model->id.')" class="btn btn-primary btn-sm" href="#"> <i class="glyphicon glyphicon-zoom-in icon-white"></i>分配权限</a>';
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
				<h3>Settings</h3>
			</div>
			<div class="modal-body">
                <?php $form = ActiveForm::begin(["id" => "admin-role-form", "class"=>"form-horizontal", "action"=>Url::toRoute("admin-role/save")]); ?>                      
                 
          <input type="hidden" class="form-control" id="id" name="AdminRole[id]" />

          <div id="code_div" class="form-group">
              <label for="code" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("code")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="code" name="AdminRole[code]" placeholder="必填" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="name_div" class="form-group">
              <label for="name" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("name")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="name" name="AdminRole[name]" placeholder="必填" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="des_div" class="form-group">
              <label for="des" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("des")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="des" name="AdminRole[des]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="create_user_div" class="form-group">
              <label for="create_user" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("create_user")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="create_user" name="AdminRole[create_user]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="create_date_div" class="form-group">
              <label for="create_date" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("create_date")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="create_date" name="AdminRole[create_date]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="update_user_div" class="form-group">
              <label for="update_user" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("update_user")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="update_user" name="AdminRole[update_user]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="update_date_div" class="form-group">
              <label for="update_date" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("update_date")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="update_date" name="AdminRole[update_date]" placeholder="" />
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

<!-- 分配权限 -->
<div class="modal fade" id="tree_dialog" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>权限分配</h3>
			</div>
			<div class="modal-body">
			     <input type="hidden" id="select_role_id" />
                <?php $form = ActiveForm::begin(["id" => "system-role-form", "class"=>"form-horizontal", "action"=>Url::toRoute("system-role/save")]); ?>                
               <div id="treeview"></div>
                <?php ActiveForm::end(); ?>            
            </div>
			<div class="modal-footer">
				<a href="#" class="btn btn-default" data-dismiss="modal">关闭</a> <a
					id="right_dialog_ok" href="#" class="btn btn-primary">确定</a>
			</div>
		</div>
	</div>
</div>
<!-- 分配权限结束 -->

<?php $this->beginBlock('footer');  ?>
<!-- <body></body>后代码块 -->
 <script>
 // 分配权限
 $(function(){
	// 树节点 http://www.htmleaf.com/jQuery/Menu-Navigation/201502141379.html
	$('#user_btn').click(function(){
		$('#tree_dialog').modal('show');
	});

	$('#right_btn').click(function(){
		
	});
});
function changeCheckState(node, checked){
	if(!!node.nodes == true){
		var nodes = node.nodes;
		for(var i = 0; i < nodes.length; i++){
			var node1 = nodes[i];
			if(checked == true){
				$('#treeview').treeview('checkNode', [ node1.nodeId, { silent: true } ]);
			}
			else{
				$('#treeview').treeview('uncheckNode', [ node1.nodeId, { silent: true } ]);
			}
			changeCheckState(node1, checked);
		}
	}
}

function rightAction(roleId){
	$('#select_role_id').val(roleId);
	$.ajax({
		   type: "GET",
		   url: "<?=Url::toRoute('admin-role/get-all-rights')?>",
		   data: {'roleId':roleId},
		   cache: false,
		   dataType:"json",
		   error: function (xmlHttpRequest, textStatus, errorThrown) {
			    alert("出错了，" + textStatus);
			},
		   success: function(data){
				$('#treeview').treeview({
					data:data,
					showIcon: false,
			        showCheckbox: true,
			        onNodeChecked: function(event, node) {
			          //console.log('======',node);
			          changeCheckState(node, true);
			        },
			        onNodeUnchecked: function (event, node) {
			        	changeCheckState(node, false);
			        }
					});
		   }
		});
	$('#tree_dialog').modal('show');
}

$('#right_dialog_ok').click(function(){
	var role_id = $('#select_role_id').val();
	var checkNodes = $('#treeview').treeview('getChecked');
	if(checkNodes.length > 0){
		var rids = [];
		for(i = 0; i < checkNodes.length; i++){
			var node = checkNodes[i];
			if(node.type == 'r'){
				rids.push(node.rid);
			}
		}
		$.ajax({
			   type: "GET",
			   url: "<?=Url::toRoute('admin-role/save-rights')?>",
			   data: {"rids":rids, 'roleId':role_id},
			   cache: false,
			   dataType:"json",
			   error: function (xmlHttpRequest, textStatus, errorThrown) {
				    alert("出错了，" + textStatus);
				},
			   success: function(data){
				   if(data.errno == 0){
					   admin_tool.alert('msg_info', '保存成功', 'success');
				   }
				   else{
					   admin_tool.alert('msg_info', '保存失败', 'error');
				   }
				   $('#tree_dialog').modal('hide');
//	 			   console.log(msg);
				   //initEditSystemModule(data, type);
			   }
			});
// 		console.log('====',rids);
	}
});
//分配权限

 function searchAction(){
		$('#admin-role-search-form').submit();
	}
 function viewAction(id){
		initModel(id, 'view', 'fun');
	}

 function initEditSystemModule(data, type){
	if(type == 'create'){
		$("#id").val('');
		$("#code").val('');
		$("#name").val('');
		$("#des").val('');
		$("#create_user").val('');
		$("#create_date").val('');
		$("#update_user").val('');
		$("#update_date").val('');
		
	}
	else{
		$("#id").val(data.id);
    	$("#code").val(data.code);
    	$("#name").val(data.name);
    	$("#des").val(data.des);
    	$("#create_user").val(data.create_user);
    	$("#create_date").val(data.create_date);
    	$("#update_user").val(data.update_user);
    	$("#update_date").val(data.update_date);
    	}
	if(type == "view"){
      $("#id").attr({readonly:true,disabled:true});
      $("#code").attr({readonly:true,disabled:true});
      $("#name").attr({readonly:true,disabled:true});
      $("#des").attr({readonly:true,disabled:true});
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
      $("#name").attr({readonly:false,disabled:false});
      $("#des").attr({readonly:false,disabled:false});
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
		   url: "<?=Url::toRoute('admin-role/view')?>",
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
				   url: "<?=Url::toRoute('admin-role/delete')?>",
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
	$('#admin-role-form').submit();
});

$('#create_btn').click(function (e) {
    e.preventDefault();
    initEditSystemModule({}, 'create');
});

$('#delete_btn').click(function (e) {
    e.preventDefault();
    deleteAction('');
});

$('#admin-role-form').bind('submit', function(e) {
	e.preventDefault();
	var id = $("#id").val();
	var action = id == "" ? "<?=Url::toRoute('admin-role/create')?>" : "<?=Url::toRoute('admin-role/update')?>";
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

 
</script>
<?php $this->endBlock(); ?>