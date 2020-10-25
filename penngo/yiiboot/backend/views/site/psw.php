<?php
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
?>


<!-- Main content -->
<section class="content">
	<!-- Small boxes (Stat box) -->
	<div class="row">
			<div class="col-md-1">
                </div>
                <div class="col-md-6">
                  <!-- Horizontal Form -->
                  <div class="box">
                    <div class="box-header with-border">
                      <h3 class="box-title">修改密码</h3>
                    </div>
                    <!-- /.box-header -->
                    <?php ActiveForm::begin(["id" => "update-psw-form", 'options' => ['class' => 'form-horizontal']]); ?>                      
                      <div class="box-body">
                      
                        <div class="form-group">
                          <label for="inputEmail3" class="col-sm-3 control-label">当前角色</label>
        
                          <div class="col-sm-9">
                            <input type="text" readonly="readonly" disabled="disabled" class="form-control" id="user_role" value="<?=$user_role?>" />
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">旧密码</label>
                          <div class="col-sm-9">
                            <input type="password" class="form-control" id="old_password" name="old_password"  placeholder="旧密码" />
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">新密码</label>
                          <div class="col-sm-9">
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="新密码">
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">确认密码</label>
                          <div class="col-sm-9">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="确认密码">
                          </div>
                        </div>
                      </div>
                      <!-- /.box-body -->
                      <div class="box-footer">
                       <label id="msg_info" class="control-label text-green hide"><i class="fa fa-check"></i>修改密码成功</label>
                        <button id="update_psw_btn" type="button" class="btn btn-info pull-right">修改密码</button>
                      </div>
                      <!-- /.box-footer -->
                    <?php ActiveForm::end(); ?>       
                  </div>
                  <!-- /.box -->
  
                  <!-- /.box -->
                </div>
              
              <div class="col-md-5">
                </div>
	
	
		
	</div>
	<!-- /.row -->
	<!-- Main row -->
	<div class="row">
		
	</div>
	<!-- /.row (main row) -->

</section>
<!-- /.content -->
<?php $this->beginBlock('footer');  ?>
<script>
$('#update_psw_btn').click(function (e) {
    e.preventDefault();
	$('#update-psw-form').submit();
});

$('#update-psw-form').bind('submit', function(e) {
	e.preventDefault();
	$("#msg_info").addClass('hide');
    $(this).ajaxSubmit({
    	type: "post",
    	dataType:"json",
    	url: "<?=Url::toRoute('site/psw-save')?>",
    	success: function(value) 
    	{
        	if(value.errno == 0){
        		$("#msg_info").removeClass('hide');
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