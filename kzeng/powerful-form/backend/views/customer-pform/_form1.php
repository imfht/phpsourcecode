<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\CustomerPform */
/* @var $form yii\widgets\ActiveForm */

$pform_fields = backend\models\PformField::find()
				->where(['pform_uid' => $pform->uid])
				->orderBy(['id' => SORT_ASC])
				->all();
?>

<script>
    var ajaxUrl = "<?= Url::to(['/site/ajax-broker']); ?>";
</script>

<div class="customer-pform-form">
<h3><?= $pform->title?></h3>
<img src="<?= '/admin'.$pform->form_img_url?>" width="100%">
<br><br>

<?php if(empty($pform_fields)) {?>
	<h1>您还没有为表单增加任何字段哟~</h1>

<?php } else { ?>
	<form id="form1">
	<input type="hidden" id="form_uid" value="<?= $pform->uid ?>">
	<?php foreach ($pform_fields as $pform_field) { ?>

		<?php if($pform_field->type == 4) { ?><!-- 单选 -->
			<div class="form-group">
				<input type="hidden" class="myformfield_id" value="<?= $pform_field->id ?>">
			    <label class="control-label" for="field_title"><?= $pform_field->title ?></label>
			    
			    <select  class="form-control myformfield">
					<?php 
						$opts = explode(" ",$pform_field->value);
						if(empty($opts)) $opts = ['---'];

						for( $i=0; $i<count($opts); $i++) 
						{ 
					?>	
						<option> <?= $opts[$i] ?> </option>
					<?php
						} 
					?>
			    </select>
			    <div class="help-block"></div>
			</div>
		<?php } else { ?>
			<div class="form-group">
				<input type="hidden" class="myformfield_id" value="<?= $pform_field->id ?>">
			    <label class="control-label" for="field_title"><?= $pform_field->title ?></label>
			    <input type="text"  class="form-control myformfield"  maxlength="64" placeholder="<?= $pform_field->placeholder ?>">
			    <div class="help-block"></div>
			</div>
		<?php } ?>

	<?php } ?>

	<div class="form-group">
		<button type="button" class="btn btn-success btn-block btn-lg" id="addCustomerFormData">确定</button>
	</div>
	</form>
<?php } ?>

</div>

<script type="text/javascript">
	$('document').ready(function(){
	    var pform_uid = "<?= $pform->uid ?>";
		var myformfield="";
		var myformfield_id="";
		var flag = 0;
		var flag1 = 0;
		
	    function addCustomerFormData() {
	        var args = {
	            'classname': '\\backend\\models\\Pform',
	            'funcname': 'addCustomerFormData',
	            'params': {
	                'form_uid': pform_uid,
	                'myformfield_id': myformfield_id,
	                'myformfield': myformfield,
	            }
	        };
	        $.ajax({
	            url: ajaxUrl,
	            type: 'GET',
	            cache: false,
	            dataType: 'json',
	            data: 'args=' + JSON.stringify(args),
	            success: function (ret) {
	            	if(ret['code'] ==0)
	            		alert('您已经成功提交表单，谢谢。');

	                location.reload();
	            },
	            error: function () {
	            }
	        });
	    }

		$('#addCustomerFormData').click(function(){
			
	    	$(".myformfield").each(function(){
	    		//alert($(this).val());
	    		if(flag == 0)
	    			myformfield = $(this).val();
	    		else
	    			myformfield = myformfield+ "<====>" + $(this).val();
	    		flag++;
	    	})

	    	$(".myformfield_id").each(function(){
	    		if(flag1 == 0)
	    			myformfield_id = $(this).val();
	    		else
	    			myformfield_id = myformfield_id+ "<====>" + $(this).val();
	    		flag1++;
	    	})
	    
	    	// alert(myformfield);
	    	// alert(myformfield_id);

	    	addCustomerFormData();

		});

	});
</script>