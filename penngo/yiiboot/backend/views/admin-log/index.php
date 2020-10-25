
<?php
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use common\utils\CommonFun;
use backend\models\AdminLog;
use yii\helpers\Url;
$modelLabel = new \backend\models\AdminLog();
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
            <!-- 
                <button id="create_btn" type="button" class="btn btn-xs btn-primary">添&nbsp;&emsp;加</button>
        			|
        		<button id="delete_btn" type="button" class="btn btn-xs btn-danger">批量删除</button>
        		 -->
            </div>
          </div>
        </div>
        <!-- /.box-header -->
        
        <div class="box-body">
          <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <!-- row start search-->
          	<div class="row">
          	<div class="col-sm-12">
                <?php ActiveForm::begin(['id' => 'admin-log-search-form', 'method'=>'get', 'options' => ['class' => 'form-inline'], 'action'=>Url::toRoute('admin-log/index')]); ?>     
                
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
              $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';
		      echo '<th><input id="data_table_check" type="checkbox"></th>';
              echo '<th onclick="orderby(\'id\', \'desc\')" '.CommonFun::sortClass($orderby, 'id').' tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('id').'</th>';
//              echo '<th onclick="orderby(\'controller_id\', \'desc\')" '.CommonFun::sortClass($orderby, 'controller_id').' tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('controller_id').'</th>';
//              echo '<th onclick="orderby(\'action_id\', \'desc\')" '.CommonFun::sortClass($orderby, 'action_id').' tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('action_id').'</th>';
              echo '<th onclick="orderby(\'url\', \'desc\')" '.CommonFun::sortClass($orderby, 'url').' tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('url').'</th>';
              echo '<th onclick="orderby(\'module_name\', \'desc\')" '.CommonFun::sortClass($orderby, 'module_name').' tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'
                  .$modelLabel->getAttributeLabel('module_name')
                  . '→' . $modelLabel->getAttributeLabel('func_name')
                  . '→' . $modelLabel->getAttributeLabel('right_name')
                  .'</th>';
              echo '<th onclick="orderby(\'client_ip\', \'desc\')" '.CommonFun::sortClass($orderby, 'client_ip').' tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('client_ip').'</th>';
              echo '<th onclick="orderby(\'create_user\', \'desc\')" '.CommonFun::sortClass($orderby, 'create_user').' tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('create_user').'</th>';
              echo '<th onclick="orderby(\'create_date\', \'desc\')" '.CommonFun::sortClass($orderby, 'create_date').' tabindex="0" aria-controls="data_table" rowspan="1" colspan="1" aria-sort="ascending" >'.$modelLabel->getAttributeLabel('create_date').'</th>';
         
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
                echo '  <td>' . $model->url . '</td>';
                echo '  <td>' . $model->module_name . '→'.$model->func_name. '→'.$model->right_name.'</td>';
                echo '  <td>' . $model->client_ip . '</td>';
                echo '  <td>' . $model->create_user . '</td>';
                echo '  <td>' . $model->create_date . '</td>';
                echo '  <td class="center">';
                echo '      <a id="view_btn" onclick="viewAction(' . $model->id . ')" class="btn btn-primary btn-sm" href="#"> <i class="glyphicon glyphicon-zoom-in icon-white"></i>查看</a>';
//                 echo '      <a id="edit_btn" onclick="editAction(' . $model->id . ')" class="btn btn-primary btn-sm" href="#"> <i class="glyphicon glyphicon-edit icon-white"></i>修改</a>';
//                 echo '      <a id="delete_btn" onclick="deleteAction(' . $model->id . ')" class="btn btn-danger btn-sm" href="#"> <i class="glyphicon glyphicon-trash icon-white"></i>删除</a>';
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
              	    'nextPageLabel' => '»',
              	    'prevPageLabel' => '«',
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
                <?php $form = ActiveForm::begin(["id" => "admin-log-form", "class"=>"form-horizontal", "action"=>"index.php?r=admin-log/save"]); ?>                      
                 
          <input type="hidden" class="form-control" id="id" name="AdminLog[id]" />

          <div id="controller_id_div" class="form-group">
              <label for="controller_id" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("controller_id")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="controller_id" name="AdminLog[controller_id]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="action_id_div" class="form-group">
              <label for="action_id" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("action_id")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="action_id" name="AdminLog[action_id]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="url_div" class="form-group">
              <label for="url" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("url")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="url" name="AdminLog[url]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="module_name_div" class="form-group">
              <label for="module_name" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("module_name")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="module_name" name="AdminLog[module_name]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="func_name_div" class="form-group">
              <label for="func_name" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("func_name")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="func_name" name="AdminLog[func_name]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="right_name_div" class="form-group">
              <label for="right_name" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("right_name")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="right_name" name="AdminLog[right_name]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="client_ip_div" class="form-group">
              <label for="client_ip" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("client_ip")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="client_ip" name="AdminLog[client_ip]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="create_user_div" class="form-group">
              <label for="create_user" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("create_user")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="create_user" name="AdminLog[create_user]" placeholder="" />
              </div>
              <div class="clearfix"></div>
          </div>

          <div id="create_date_div" class="form-group">
              <label for="create_date" class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("create_date")?></label>
              <div class="col-sm-10">
                  <input type="text" class="form-control" id="create_date" name="AdminLog[create_date]" placeholder="" />
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
		$('#admin-log-search-form').submit();
	}
 function viewAction(id){
		initModel(id, 'view', 'fun');
	}

 function initEditSystemModule(data, type){
	if(type == 'create'){
		$("#id").val('');
		$("#controller_id").val('');
		$("#action_id").val('');
		$("#url").val('');
		$("#module_name").val('');
		$("#func_name").val('');
		$("#right_name").val('');
		$("#client_ip").val('');
		$("#create_user").val('');
		$("#create_date").val('');
		
	}
	else{
		$("#id").val(data.id);
    	$("#controller_id").val(data.controller_id);
    	$("#action_id").val(data.action_id);
    	$("#url").val(data.url);
    	$("#module_name").val(data.module_name);
    	$("#func_name").val(data.func_name);
    	$("#right_name").val(data.right_name);
    	$("#client_ip").val(data.client_ip);
    	$("#create_user").val(data.create_user);
    	$("#create_date").val(data.create_date);
    	}
	if(type == "view"){
      $("#id").attr({readonly:true,disabled:true});
      $("#controller_id").attr({readonly:true,disabled:true});
      $("#action_id").attr({readonly:true,disabled:true});
      $("#url").attr({readonly:true,disabled:true});
      $("#module_name").attr({readonly:true,disabled:true});
      $("#func_name").attr({readonly:true,disabled:true});
      $("#right_name").attr({readonly:true,disabled:true});
      $("#client_ip").attr({readonly:true,disabled:true});
      $("#create_user").attr({readonly:true,disabled:true});
      $("#create_date").attr({readonly:true,disabled:true});
	$('#edit_dialog_ok').addClass('hidden');
	}
	else{
      $("#id").attr({readonly:false,disabled:false});
      $("#controller_id").attr({readonly:false,disabled:false});
      $("#action_id").attr({readonly:false,disabled:false});
      $("#url").attr({readonly:false,disabled:false});
      $("#module_name").attr({readonly:false,disabled:false});
      $("#func_name").attr({readonly:false,disabled:false});
      $("#right_name").attr({readonly:false,disabled:false});
      $("#client_ip").attr({readonly:false,disabled:false});
      $("#create_user").attr({readonly:false,disabled:false});
      $("#create_date").attr({readonly:false,disabled:false});
		$('#edit_dialog_ok').removeClass('hidden');
		}
		$('#edit_dialog').modal('show');
}

function initModel(id, type, fun){
	
	$.ajax({
		   type: "GET",
		   url: "<?=Url::toRoute('admin-log/view')?>",
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
		var checkboxs = $('#data_table tbody :checked');
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
				   url: "<?=Url::toRoute('admin-log/delete')?>",
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
	$('#admin-log-form').submit();
});

$('#create_btn').click(function (e) {
    e.preventDefault();
    initEditSystemModule({}, 'create');
});

$('#delete_btn').click(function (e) {
    e.preventDefault();
    deleteAction('');
});

$('#admin-log-form').bind('submit', function(e) {
	e.preventDefault();
	var id = $("#id").val();
	var action = id == "" ? "<?=Url::toRoute('admin-log/create')?>" : "<?=Url::toRoute('admin-log/update')?>";
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