<div id="main_right">


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'order-form',
	'enableAjaxValidation'=>false,
)); ?>
<h2 style=" display:inline;">选择充值游戏：</h2>
	<?php echo $form->dropDownList($model, 'gid',  Games::model()->getGamesAllShow(),array('onchange'=>'dami()','style'=>'width:100px; display:inline;'));?>
<h2 style=" display:inline;">选择充值服务器：</h2>
<script>
			function dami(){
				var gid=$("#Order_gid").val();
				var url="<?php echo Yii::app()->baseUrl;?>/order/ajax";
				if(gid){
					$.post(url, { gid : gid},
						function(data){
							if(data){
								$("#Order_gid_server_id").html(data);
							}else{
								$("#Order_gid_server_id").html('<option value="">请选择其他游戏</potion>');
							}
							
						});
						
				}
			}
</script>
<select name="Order[gid_server_id]" id="Order_gid_server_id">
 <?php
	$getGamesValue=Games::model()->getGamesServerId("1");
	$i=1;
	if($getGamesValue){
		foreach($getGamesValue as $value){
			echo '<option value="'.$value.'">918双线'.$i.'区</option>';
			$i++;
		}
	}else{
		echo '<option value="">请选择其他游戏</potion>';
	}
	
 ?>
</select>

<h2>选择充值金额：</h2>
<div id="right_01">
<ul>
<li>
<input type="radio" class="radio" value="10" name="Order[price]" checked="checked" />10元
</li>
<li>
<input type="radio" class="radio" value="20" name="Order[price]" />20元
</li>
<li>
<input type="radio" class="radio" value="30" name="Order[price]" />30元
</li>
<li>
<input type="radio" class="radio" value="50" name="Order[price]" />50元
</li>
<li>
<input type="radio" class="radio" value="80" name="Order[price]" />80元
</li>
<li>
<input type="radio" class="radio" value="100" name="Order[price]" />100元
</li>
<li>
<input type="radio" class="radio" value="120" name="Order[price]" />120元
</li>
<li>
<input type="radio" class="radio" value="150" name="Order[price]" />150元
</li>
<li>
<input type="radio" class="radio" value="180" name="Order[price]" />180元
</li>
<li>
<input type="radio" class="radio" value="200" name="Order[price]" />200元
</li>
<li>
<input type="radio" class="radio" value="500" name="Order[price]" />500元
</li>
<li>
<input type="radio" class="radio" value="800" name="Order[price]" />800元
</li>
<li>
<input type="radio" class="radio" value="1000" name="Order[price]" />1000元
</li><br />
<li>
<input type="radio" class="radio" value="other" name="Order[price]" />其它
<input type="text" name="price" id="selfPrice" />*请填写10到1000000之间的整数
</li>
</ul>
</div>
<div id="right_02">
<p class="anniu"><a href="javascript:void(0)"  onclick="confirmPay()">立即支付</a></p>
<div class="gotopay"></div>
<?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
$(function(){
	$("body").append("<div id='windownbg'></div>");
	$("#windownbg").css({
		"background":"#000",
		"top":0,
		"left":0,
		"position":"absolute",
		"width":"100%",
		"height":$(document).height(),
		"z-index":"9999",
		"opacity":"0.6",
		"display":"none",
	});
})
function confirmPay()
{
	var gid=$("#Order_gid option:selected").text();
	var serverIdValue=$("#Order_gid_server_id").val();
	var price=$('input:radio[name="Order[price]"]:checked').val();
	var serverId=$("#Order_gid_server_id option:selected").text();
	if(price=="other"){
		price = $('#selfPrice').val();
		if(isNaN(price) || price==""){
			alert("请输入金额！");
			return;
		}
	}
	var re = /^[1-9][0-9]{1,4}$/;
	if(!re.test(price))
	{
		alert('请输入正确的金额');
		return;
	}
	$("#confirm_pay_type").text("<?php echo '支付宝';?>");
	$("#confirm_pay_game").text(gid+serverId);
	$("#confirm_pay_mid").text("<?php echo Yii::app()->user->name ;?>");
	$("#confirm_pay_number").text("<?php echo rand(1,9).date('is',time()).rand(1,9).rand(1,9)?>");
	$("#confirm_pay_price").text(price);
	popup(350,254);
};
function popup(width,height)
{
	$("#windownbg").show();
	$("#pay").show();
	var	cw = document.documentElement.clientWidth,ch = document.documentElement.clientHeight,est = document.documentElement.scrollTop;
	var _version = $.browser.version;
	if ( _version == 6.0 ) { 
		$("#pay").css({left:"50%",top:(parseInt((ch)/2)+est)+"px",marginTop: -((parseInt(height)+53)/2)+"px",marginLeft:-((parseInt(width)+32)/2)+"px",zIndex: "999999"}); 
	}else { 
		$("#pay").css({left:"50%",top:"50%",marginTop:-((parseInt(height)+53)/2)+"px",marginLeft:-((parseInt(width)+32)/2)+"px",zIndex: "999999"}); 
	};
	$("#resetOrder").click(function(){
		$("#windownbg").hide();
		$("#pay").hide();
	})
}
function pay(){
	var url="<?php echo Yii::app()->request->baseUrl;?>/alipay/";
	var pay_type=1;
	var pay_game=$("#Order_gid").val();
	var pay_server_value=$("#Order_gid_server_id").val();
	var price=$("#confirm_pay_price").text();
	var pay_server=$("#Order_gid_server_id option:selected").text();
	var pay_number=$("#confirm_pay_number").text();
	$.post(url, { pay_type : pay_type ,pay_game : pay_game,pay_number : pay_number,pay_server_value : pay_server_value,price : price,pay_server:pay_server},
	function(data){
		$(".gotopay").html(data);
	})
}
</script>
<div id="pay" style="display:none;">
 <table width="350" border="0">
  <tr>
    <th>您充值的方式</th>
    <td><span id="confirm_pay_type"></span></td>
  </tr>
  <tr>
    <th>您充值的游戏</th>
    <td><span id="confirm_pay_game"></span> </td>
  </tr>
  <tr>
    <th>您充值的帐号</th>
    <td><span id="confirm_pay_mid"></span></td>
  </tr>
  <tr>
    <th>本次充值的订单号</th>
    <td><span id="confirm_pay_number"></span></td>
  </tr>
  <tr>
    <th>您充值的金额</th>
    <td><span id="confirm_pay_price"></span></td>
  </tr>
  <tr>
    <td colspan="2" class="cl">
    	<a target="_bank" href="javascript:void(0)" onclick="pay()" id="submitOrder">确认提交 </a>
   		<a href="javascript:void(0)" id="resetOrder">重新填写 </a>
    </td>
    </tr>
</table>	
  </div>


