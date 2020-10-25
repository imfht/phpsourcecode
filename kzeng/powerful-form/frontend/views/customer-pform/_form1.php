<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use nex\chosen\Chosen;

/* @var $this yii\web\View */
/* @var $model backend\models\CustomerPform */
/* @var $form yii\widgets\ActiveForm */

$pform_fields = backend\models\PformField::find()
				->where(['pform_uid' => $pform->uid])
				->orderBy(['id' => SORT_ASC])
				->all();

$customerform_count = \backend\models\CustomerPform::find()
        ->select(['customer_pform_uid'])
        ->where(["pform_uid" => $pform->uid])
        ->distinct()
        ->count();		
?>

<script>
    var ajaxUrl = "<?= Url::to(['/site/ajax-broker']); ?>";
</script>
 
<div class="customer-pform-form">
<h3><?= $pform->title?></h3>
<img src="<?= $pform->form_img_url?>" width="100%">

<br>
	<p class="pull-right" style="background-color:yellow">
	<span style="color: #ccc; font-size: 16px">* 已有</span>
	<span style="color: red; font-size: 22px"><?= $customerform_count + 100 ?></span>
	<span style="color: #ccc; font-size: 16px">人报名</span>
	</p>
<br>

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
		<?php } else if($pform_field->type == 2) { ?><!-- 手机号码 -->
			<div class="form-group">
				<input type="hidden" class="myformfield_id" value="<?= $pform_field->id ?>">
			    <label class="control-label" for="field_title"><?= $pform_field->title ?></label>
			    <input type="tel"  class="form-control myformfield"  maxlength="64" placeholder="<?= $pform_field->placeholder ?>">
			    <div class="help-block"></div>
			</div>		
		<?php } else if ($pform_field->type == 5) { ?> <!-- 多选 -->
			<div class="form-group">
				<input type="hidden" class="myformfield_id" value="<?= $pform_field->id ?>">
			    <label class="control-label" for="field_title"><?= $pform_field->title ?></label>

			    <?php
			    	$opts = explode(" ",$pform_field->value);
			    	$temp = [];
			    	foreach ($opts as $value) {
			    		$temp[$value] = $value;
			    	}
					if(empty($temp)) $temp = ['---' => '---'];
			    ?>
				<?= Chosen::widget([
				    'name' => 'ChosenTest',
				    'value' => 3,
				    'items' => $temp,
				    'multiple' => true,
				    // 'allowDeselect' => true,
				    // 'disableSearch' => true,
				    'placeholder' => '请选择',
				    // 'class' => 'myformfield', // 不行
				    'clientOptions' => [
				        'search_contains' => true,
				        // 'max_selected_options' => 2,
				    ],
				]);?>
			    <div class="help-block"></div>
			</div>	
		<?php } else if ($pform_field->type == 6) { ?> <!-- 备注（多行文本） -->
			<div class="form-group">
				<input type="hidden" class="myformfield_id" value="<?= $pform_field->id ?>">
			    <label class="control-label" for="field_title"><?= $pform_field->title ?></label>
			    <textarea class="form-control myformfield" placeholder="<?= $pform_field->placeholder ?>">
			    </textarea>
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

<br><br><br>
<small class='center-block text-center'>
<!--
<a href="http://<?= $_SERVER['HTTP_HOST'] ?>">&copy;超级表单 <?= date('Y') ?></a>
-->
<a href="#">ZK-TECH &copy; <?= date('Y') ?></a>
</small>

</div>

<script type="text/javascript">
	$('document').ready(function(){
	    var pform_uid = "<?= $pform->uid ?>";
		var myformfield="";
		var myformfield_id="";
		var flag = 0;
		var flag1 = 0;
		var flag2 = 0;
		
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
						location.href = ret['endlink'];
	            },
	            error: function () {
	            }
	        });
	    }

		$('#addCustomerFormData').click(function(){
			
			var flag = 0;
			$("input[type=text].myformfield").each(function(i){
			    var text = $(this).val();
			    if(text ==""){
			        flag = 1;
			        return false
			    }
			});

			if(flag == 1)
			{
				alert("请完善表单信息！");
				return false;
			}


	    	$(".myformfield").each(function(){
	    		//alert($(this).val());

	    		// alert($(this).attr('type'));

	    		// if( $(this).attr('type') == 'checkbox')
	    		// {
	    		// 	alert($(this).val());
	    		// 	if(flag == 0)
	    		// 		if($(this).prop("checked")) myformfield = $(this).val();
		    	// 	else
		    	// 		if($(this).prop("checked")) myformfield = myformfield+ "//" + $(this).val();
		    	// 	flag++;
	    		// }
	    		// else
	    		// {
		    		if(flag == 0)
		    			myformfield = ''+$(this).val();
		    		else
		    			myformfield = myformfield+ "<====>" + $(this).val();
		    		flag++;
	    		//}

	    	})

	    	$(".myformfield_id").each(function(){

	    		if(flag1 == 0)
	    			myformfield_id = $(this).val();
	    		else
	    			myformfield_id = myformfield_id+ "<====>" + $(this).val();
	    		flag1++;
	    	})
	    


	    	// $(".myformfield2:checked").each(function(){

	    	// 	if(flag2 == 0)

	    	// 		myformfield_id = $(this).val();
	    	// 	else
	    	// 		myformfield_id = myformfield_id+ "<====>" + $(this).val();

	    	// 	flag2++;
	    	// })


	    	// alert(myformfield);
	    	// alert(myformfield_id);

	    	addCustomerFormData();

		});

	});
</script>