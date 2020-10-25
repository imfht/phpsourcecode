<script type="text/javascript">
<!--
$(function(){
	clr = setInterval(skip,1000);
})
function skip()
{
	var $time = $("#time").text();
	if($time>0){
		$("#time").text($time-1);
	}else{
		clearInterval(clr);
		location.href = 'index';
	}
}
//-->
</script>
<div class="main_right_mr">
    <h2></h2>
	<div>
		<span id="time" style="display: none;">3</span>
		<img src="<?php echo Yii::app()->request->baseUrl;?>/images/tiaozhuan03.gif" class="tiao"/>
	</div>
</div>