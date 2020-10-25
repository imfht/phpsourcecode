<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl;?>/js/change/imgareaselect-default.css"/>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/js/change/jquery.imgareaselect.pack.js"></script>
<script type="text/javascript"><!--
/*
 * setImagePreview()
 * @param fileObj Strimg 表示上传域的ID
 * @param previewObj String 表示显示图片的ID
 * @param localImg String 表示显示图片外层的div的ID
 * @param width 表示显示图片的宽
 * @param height 表示显示图片的高
 */
function setImagePreview(fileObj, previewObj, localImg,width,height) {
    var docObj=document.getElementById(fileObj);
    var imgObjPreview=document.getElementById(previewObj);
    if(docObj.files && docObj.files[0]){
        //火狐下，直接设img属性
        imgObjPreview.style.display = 'block';
        imgObjPreview.style.width = width+'px';
        imgObjPreview.style.height = height+'px';
        //imgObjPreview.src = docObj.files[0].getAsDataURL();

        //火狐7以上版本不能用上面的getAsDataURL()方式获取，需要一下方式  
        imgObjPreview.src = window.URL.createObjectURL(docObj.files[0]);
    }else{
        //IE下，使用滤镜
        docObj.select();
        var imgSrc = document.selection.createRange().text;
        var localImagId = document.getElementById(localImg);
        //必须设置初始大小
        localImagId.style.width = width+'px';
        localImagId.style.height = height+'px';
        //图片异常的捕捉，防止用户修改后缀来伪造图片
        try{
            localImagId.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
            localImagId.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
        }catch(e){
            alert("您上传的图片格式不正确，请重新选择!");
            return false;
        }
        imgObjPreview.style.display = 'none';
        document.selection.empty();
    }
    return true;
}
$(function(){
//	$("#Member_headimg").hide();
	$("#Member_headimg").change(function() {
		setImagePreview('Member_headimg','preview','localImag',200,300);
		setImagePreview('Member_headimg','small_photo','small_img',75,75);
		setImagePreview('Member_headimg','big_photo','big_img',100,100);
	})
	
//	$("#send").click(function(){
//		$("#Member_headimg").click();
//	})
//	
	$("#localImag").imgAreaSelect({
		aspectRatio:'1:1',	//选中区域是正方形
		handles:true,
		fadeSpeed:200,		//出来效果
		onSelectChange:set //执行选择后执行的函数
	});

	function set(img,selection)
	{
		if (!selection.width || !selection.height)
	        return;
		var scaleX = 100/selection.width;
	    var scaleY = 100/selection.height;

	    

	    if (window.navigator.userAgent.indexOf("MSIE") >= 1) {
	    	$('#big_img').css({
		        width: Math.round(scaleX * 200),
		        height: Math.round(scaleY * 300),
		        marginLeft: -Math.round(scaleX * selection.x1),
		        marginTop: -Math.round(scaleY * selection.y1)
		    });

	    	$('#small_img').css({
		        width: Math.round(scaleX * 200*3/4),
		        height: Math.round(scaleY * 300*3/4),
		        marginLeft: -Math.round(scaleX * selection.x1*3/4),
		        marginTop: -Math.round(scaleY * selection.y1*3/4)
		    });
		}else{
			$('#big_photo').css({
		        width: Math.round(scaleX * 200),
		        height: Math.round(scaleY * 300),
		        marginLeft: -Math.round(scaleX * selection.x1),
		        marginTop: -Math.round(scaleY * selection.y1)
		    });

		    $('#small_photo').css({
		        width: Math.round(scaleX * 200*3/4),
		        height: Math.round(scaleY * 300*3/4),
		        marginLeft: -Math.round(scaleX * selection.x1*3/4),
		        marginTop: -Math.round(scaleY * selection.y1*3/4)
		    });
		}
	    		
		$("#w").val(selection.width);
		$("#h").val(selection.height);
		$("#x1").val(selection.x1);
		$("#x2").val(selection.x2);
		$("#y1").val(selection.y1);
		$("#y2").val(selection.y2);
		$("#yh").val(img.height);
	}

})
--></script>
  <div id="main_right">
  <div class="tuxiang">
    	<?php $form = $this->beginWidget('CActiveForm',array(
        'id'=>'member-form',
    	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
    	'enableAjaxValidation'=>true,
    	'enableClientValidation'=>true,
    	'clientOptions'=>array('validateOnSubmit'=>true),
    )); ?>
    <div class="row">
		<div class="w_300">
		<h2>头像设置</h2>
		<p><span id="headimg_file"><?php echo $form->fileField($model,'headimg'); ?></span> <input type="image" src="<?php echo Yii::app()->baseUrl;?>/images/send.jpg" />  </p>
		<?php echo $form->error($model,'headimg');?>
	</div>
	</div>
			<input type="hidden" id="w" name="w" />
    		<input type="hidden" id="h" name="h" />
    		<input type="hidden" id="x1" name="x1" />
    		<input type="hidden" id="x2" name="x2" />
    		<input type="hidden" id="y1" name="y1" />
    		<input type="hidden" id="y2" name="y2" />
    		<input type="hidden" id="yh" name="yh" />
    <?php $this->endWidget(); ?>
  <div id="img"><p class="color_l">单击拖动鼠标对图片进行修改</p>
  	<div class="hull">
	  	<div id="localImag" style="background:url(<?php echo Yii::app()->request->baseUrl?>/images/big_tx.jpg);"><img id="preview" width=-1 height=-1 style="diplay:none;border:none;" /></div>
	  	<div id="show_img">
	  		<p>大头像</p>
	  		<div style="background:url(<?php echo Yii::app()->request->baseUrl?>/images/sm_tx.jpg);height:100px;width:100px;border:5px solid #F5F4F3;overflow:hidden;"><div id="big_img" style="height:100px;width:100px;overflow:hidden;"><img id="big_photo" width=-1 height=-1 style="border:none;diplay:none" /></div></div>
  			<p>小头像</p>
  			<div style="background:url(<?php echo Yii::app()->request->baseUrl?>/images/sm_tx_2.jpg);height:75px;width:75px;border:5px solid #F5F4F3;overflow:hidden;"><div id="small_img" style="height:75px;width:75px;overflow:hidden;"><img id="small_photo" width=-1 height=-1 style="border:none;diplay:none" /></div></div>
	  		<br/><br/>
	  	</div>
  	</div>
  </div>
</div>
</div>