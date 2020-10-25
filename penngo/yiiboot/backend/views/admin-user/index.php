
<?php
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use backend\models\AdminUser;
use yii\helpers\Url;
use backend\services\AdminUserService;
$modelLabel = new \backend\models\AdminUser();
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
          <h3 class="box-title">用户管理</h3>
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
                <?php ActiveForm::begin(['id' => 'admin-user-search-form', 'method'=>'get', 'options' => ['class' => 'form-inline'], 'action'=>Url::toRoute('admin-user/index')]); ?>     
                
                  <div class="form-group" style="margin: 5px;">
                      <label><?=$modelLabel->getAttributeLabel('id')?>:</label>
                      <input type="text" class="form-control" id="query[id]" name="query[id]"  value="<?=isset($query["id"]) ? $query["id"] : "" ?>">
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
              echo '<th class="sorting" tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('id').'</th>';
              echo '<th class="sorting" tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('uname').'</th>';
              echo '<th class="sorting" tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('last_ip').'</th>';
              echo '<th class="sorting" tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('is_online').'</th>';
              echo '<th class="sorting" tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('status').'</th>';
              echo '<th class="sorting" tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('update_user').'</th>';
              echo '<th class="sorting" tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('update_date').'</th>';
         
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
                echo '  <td>' . $model->uname . '</td>';
                echo '  <td>' . $model->last_ip . '</td>';
                echo '  <td>' . ($model->is_online == 'n' ? '否' : '是')  . '</td>';
                echo '  <td>' . AdminUserService::$STATUS_CODE[$model->status] . '</td>';
                echo '  <td>' . $model->update_user . '</td>';
                echo '  <td>' . $model->update_date . '</td>';
                echo '  <td class="center">';
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
				<h3>Settings</h3>
			</div>
			<div class="modal-body">
                <?php $form = ActiveForm::begin(["id" => "admin-user-form", "class"=>"form-horizontal", "action"=>Url::toRoute("admin-user/save")]); ?>                      
                 <input type="hidden" class="form-control" id="id" name="AdminUser[id]" />
                 <!-- 
          <div id="id_div" class="form-group">
              <label for="id" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("id")?></label>
              <div class="col-sm-10">
          
              </div>
              <div class="clearfix"></div>
          </div>
             -->
          <div id="uname_div" class="form-group">
              <label for="uname" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("uname")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="uname" name="AdminUser[uname]" placeholder="必填" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="password_div" class="form-group">
              <label for="password" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("password")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="password" name="AdminUser[password]" placeholder="必填" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="auth_key_div" class="form-group">
              <label for="auth_key" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("auth_key")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="auth_key" name="AdminUser[auth_key]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="last_ip_div" class="form-group">
              <label for="last_ip" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("last_ip")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="last_ip" name="AdminUser[last_ip]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="is_online_div" class="form-group">
              <label for="is_online" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("is_online")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="is_online" name="AdminUser[is_online]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="domain_account_div" class="form-group">
              <label for="domain_account" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("domain_account")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="domain_account" name="AdminUser[domain_account]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="status_div" class="form-group">
              <label for="status" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("status")?></label>
              <div class="col-sm-10">
          		  <select class="form-control" name="AdminUser[status]" id="status">
          		  	<?php 
          		  	foreach(AdminUserService::$STATUS_CODE as $key=>$value){
          		  	    echo "<option value=\"{$key}\">{$value}</option>";
          		  	}
          		  	?>
                    
          		  </select>
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="create_user_div" class="form-group">
              <label for="create_user" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("create_user")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="create_user" name="AdminUser[create_user]" placeholder="必填" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="create_date_div" class="form-group">
              <label for="create_date" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("create_date")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="create_date" name="AdminUser[create_date]" placeholder="必填" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="update_user_div" class="form-group">
              <label for="update_user" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("update_user")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="update_user" name="AdminUser[update_user]" placeholder="必填" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="update_date_div" class="form-group">
              <label for="update_date" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("update_date")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="update_date" name="AdminUser[update_date]" placeholder="必填" />
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
 function searchAction(){
		$('#admin-user-search-form').submit();
	}
 function viewAction(id){
		initModel(id, 'view', 'fun');
	}

 function initEditSystemModule(data, type){
	if(type == 'create'){
		$("#id").val('');
		$("#uname").val('');
		$("#password").val('');
		$("#auth_key").val('');
		$("#last_ip").val('');
		$("#is_online").val('');
		$("#domain_account").val('');
		$("#status").val('10');
		$("#create_user").val('');
		$("#create_date").val('');
		$("#update_user").val('');
		$("#update_date").val('');
		
	}
	else{
		$("#id").val(data.id);
    	$("#uname").val(data.uname);
    	$("#password").val(data.password);
    	$("#auth_key").val(data.auth_key);
    	$("#last_ip").val(data.last_ip);
    	$("#is_online").val(data.is_online);
    	$("#domain_account").val(data.domain_account);
    	$("#status").val(data.status);
    	$("#create_user").val(data.create_user);
    	$("#create_date").val(data.create_date);
    	$("#update_user").val(data.update_user);
    	$("#update_date").val(data.update_date);
    	}
	if(type == "view"){
      $("#id").attr({readonly:true,disabled:true});
      $("#uname").attr({readonly:true,disabled:true});
      $("#password").attr({readonly:true,disabled:true});
      $("#password").parent().parent().hide();
      $("#auth_key").attr({readonly:true,disabled:true});
      $("#auth_key").parent().parent().hide();
      $("#last_ip").attr({readonly:true,disabled:true});
      $("#is_online").attr({readonly:true,disabled:true});
      $("#domain_account").attr({readonly:true,disabled:true});
      $("#status").attr({readonly:true,disabled:true});
      $("#create_user").attr({readonly:true,disabled:true});
      $("#create_date").attr({readonly:true,disabled:true});
      $("#update_user").attr({readonly:true,disabled:true});
      $("#update_date").attr({readonly:true,disabled:true});
	  $('#edit_dialog_ok').addClass('hidden');
	}
	else{
      $("#id").attr({readonly:false,disabled:false});
      $("#uname").attr({readonly:true,disabled:true});
      if(type == "create"){
    	  $("#uname").attr({readonly:false,disabled:false});
    	  $("#password").attr({readonly:false,disabled:false});
          $("#password").parent().parent().show();
      }
      else{
    	  $("#uname").attr({readonly:true,disabled:true});
    	  $("#password").attr({readonly:true,disabled:true});
          $("#password").parent().parent().hide();
      }
      
      $("#auth_key").attr({readonly:true,disabled:true});
      $("#auth_key").parent().parent().hide();
      $("#last_ip").attr({readonly:true,disabled:true});
      $("#last_ip").parent().parent().hide();
      $("#is_online").attr({readonly:true,disabled:true});
      $("#is_online").parent().parent().hide();
      $("#domain_account").attr({readonly:false,disabled:false});
      $("#domain_account").parent().parent().hide();
      $("#status").attr({readonly:false,disabled:false});
      $("#create_user").attr({readonly:true,disabled:true});
      $("#create_user").parent().parent().hide();
      $("#create_date").attr({readonly:true,disabled:true});
      $("#create_date").parent().parent().hide();
      $("#update_user").attr({readonly:true,disabled:true});
      $("#update_user").parent().parent().hide();
      $("#update_date").attr({readonly:true,disabled:true});
      $("#update_date").parent().parent().hide();
	  $('#edit_dialog_ok').removeClass('hidden');
	}
	$('#edit_dialog').modal('show');
}

function initModel(id, type, fun){
	
	$.ajax({
		   type: "GET",
		   url: "<?=Url::toRoute('admin-user/view')?>",
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
				   url: "<?=Url::toRoute('admin-user/delete')?>",
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
	$('#admin-user-form').submit();
});

$('#create_btn').click(function (e) {
    e.preventDefault();
    initEditSystemModule({}, 'create');
});

$('#delete_btn').click(function (e) {
    e.preventDefault();
    deleteAction('');
});

$('#admin-user-form').bind('submit', function(e) {
	e.preventDefault();
	var id = $("#id").val();
	var action = id == "" ? "<?=Url::toRoute('admin-user/create')?>" : "<?=Url::toRoute('admin-user/update')?>";
    $(this).ajaxSubmit({
    	type: "post",
    	dataType:"json",
    	url: action,
    	data:{id:id},
    	success: function(value) 
    	{
        	console.log("value====", value);
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