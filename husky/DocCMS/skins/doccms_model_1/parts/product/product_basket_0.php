<?php
    // 为方便并保证您以后的快速升级 请使用SHL提供的如下全局数组
	
	// 数组定义/config/doc-global.php
	
	// 如有需要， 请去掉注释，输出数据。
	/*
	echo '<pre>';
		print_r($tag);
	echo '</pre>';
	*/
?>
<?php global $basket; ?>
<script src="<?php echo $tag['path.skin']; ?>res/js/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("input[name=buynum]").change(function(){
		var id=parseInt($(this).attr('id'));
		var value=parseInt($(this).attr('value'))
			$.ajax({
				type:"POST",
				url:"<?php echo sys_href($request["p"],'updatebasketfornum')?>",
				data:"r="+id+"&num="+value,
				timeout:"4000",
				dataType:"json",                                 
				success: function(html){
					$("#change_"+id).html(html.curprice);
					$("#totalnum").html(html.num);
					$("#totalprice").html(html.price);
					alert("操作成功");
				}
			});
	});
	$("a.del").click(function(){
		var del=$(this).parent().parent();
		$.ajax({
			type:"POST",
			url:"<?php echo sys_href($request["p"],'updatebasketfordel')?>",
			data:"r="+parseInt($(this).attr('id')),
			timeout:"4000",
			dataType:"json",                                 
			success: function(html){
				switch (html.flag){
					case "0": $("#submitbasket").remove();$("#totalnum").html('0'); $("#totalprice").html('0');del.next().append('<td colspan="6">购物车为空，请继续购物！</td>');break;
					case "1": $("#totalnum").html(html.num); $("#totalprice").html(html.price); break;
					default:;
				}
				del.remove();
				alert("操作成功");
			}
		});
	}); 
});
</script>
<?php 
if(empty($basket['productinfo'])){
	$str.='<tr><td colspan="6">购物车为空，请继续购物！</td></tr>';
	$flag=false;
}else{
	foreach($basket['productinfo'] as $k=>$data){
		$str.='<tr align="center">
				<td height="70" class="orderborder"><img src="'.ispic($data['smallPic']).'" width="60" height="40"/></td>
				<td class="orderborder">'.$data['title'].'</td>
				<td class="orderborder">'.$data['preferPrice'].'</td>
				<td class="orderborder"><input type="text" name="buynum" id="'.$data['id'].'buynum" value="'.$data['num'].'" size="2"/></td>
				<td class="orderborder" ><span id="change_'.$data['id'].'">'.($data['preferPrice']*$data['num']).'</span></td>
				<td class="orderborder"><a href="#" id="'.$data['id'].'" class="del">删除</a></td>
			</tr>';	
		$num+=$data['num'];
		$price+=$data['preferPrice']*$data['num'];
	}
	$str.='<tr></tr>';
	$flag=true;
 } 
 ?>
<style type="text/css">
#mycart { width:98%; float:left; overflow:hidden; margin-bottom:15px; }
#mycarttitle { width:100%; height:25px; float:left; margin:5px 0 0 15px; background:url(<?php echo $tag['path.skin']; ?>res/images/basket/cart_icon.jpg) left top no-repeat; padding:5px 0 15px 35px; font-size:14px; color:#666; font-weight:bold; border-bottom:1px dashed #ccc;}
#step1 { width:375px; height:35px; float:left; margin:15px 0 15px 105px; display:inline; background:url(<?php echo $tag['path.skin']; ?>res/images/basket/step1_bg.jpg) no-repeat; padding:0; }
#step2 { width:375px; height:35px; float:left; margin:15px 0 15px 105px; display:inline; background:url(<?php echo $tag['path.skin']; ?>res/images/basket/step2_bg.jpg) no-repeat; padding:0; }
#step3 { width:375px; height:35px; float:left; margin:15px 0 15px 105px; display:inline; background:url(<?php echo $tag['path.skin']; ?>res/images/basket/step3_bg.jpg) no-repeat; padding:0; }
#step1 li, #step2 li, #step3 li { width:125px; float:left; padding-top:6px; text-align:center; color:#666; }
#step1 #selected, #step2 #selected, #step3 #selected { font-weight:bold; }
.ordercontents { width:100%; float:left; overflow:hidden; }
.ordercontents table { width:100%; margin:20px auto; }
.titleborder { border-bottom:2px #A7CBFF solid; }
.orderborder { border-bottom:1px #D1EBFF solid; border-right:1px #fff solid; background:#E2F2FF; }
.proamount { width:40px; height:20px; }
#prototal { float:right; margin:25px 15px 10px 0; display:inline; }
#prototal span { float:left; margin-top:2px; }
#prototal .totalprice { color:#f30; font-size:18px; font-weight:bold; margin-top:-5px; }
#nextbutton { width:50%; height:35px; float:left; margin:50px 35px 0 210px; display:inline; }
#nextbutton img { border:none; }
.orderdetails { width:100%; float:left; overflow:hidden; }
.orderdetails h2 { font-size:16px; font-weight:bold; color:#333; margin:0 0 15px 20px; float:left; }
.orderdetails table { width:100%; border-top:1px solid #A7CBFF; border-left:1px solid #A7CBFF; margin:20px auto; }
.orderdetails .ordertitle { background:#e3e3e3; font-weight:bold; height:35px; }
.orderdetails td { border-right:1px solid #A7CBFF; border-bottom:1px solid #A7CBFF; }
.orderdetails p { padding-left:20px; float:left; width:95%; margin:0 0 10px 0; }
.orderdetails span { width:70px; float:left; text-align:right; padding-top:5px; }
.ordertext { width:118px; height:20px; float:left; margin-left:10px; display:inline; border:1px solid #999; }
.orderadd { width:228px; height:20px; float:left; margin-left:10px; display:inline; border:1px solid #999; }
.orderdetails h5 { font-size:12px; margin-top:60px; font-weight:normal; }
.savebt { -moz-border-bottom-colors: none; -moz-border-image: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background-color: #7FBF4D; background-image: -moz-linear-gradient(center top, #7FBF4D, #63A62F); border-color: #63A62F #63A62F #5B992B; border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; box-shadow: 0 1px 0 0 #96CA6D inset; color: #FFFFFF; font: 12px; padding:0.4em 1em; text-align: center; text-shadow: 0 -1px 0 #4C9021; cursor:pointer; float:right; font-size:14px;}
.savebt:hover { background-color: #76B347; background-image: -moz-linear-gradient(center top, #76B347, #5E9E2E); box-shadow: 0 1px 0 0 #8DBF67 inset; cursor: pointer; color:#fff; text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.3); }
.clear { clear:both; }
</style>
<div id="mycart"> <span id="mycarttitle">我的购物车</span>
  <ol id="step1">
    <li id="selected">加入购物车</li>
    <li>确认订单</li>
    <li>提交订单</li>
  </ol>
  <div class="ordercontents">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
      <tr align="center">
        <td width="15%" height="30" class="titleborder">商品编号</td>
        <td width="35%" class="titleborder">商品名称</td>
        <td width="10%" class="titleborder">单价</td>
        <td width="12%" class="titleborder">数量</td>
        <td width="13%" class="titleborder">小计（RMB）</td>
        <td class="titleborder">操作</td>
      </tr>
      <?php echo $str; ?>
    </table>
  </div>
  <?php if($flag){ ?>
  <div id="prototal"> <span>商品总数：</span><span class="totalnum" id="totalnum"><?php echo $num; ?></span><span>件，</span> <span>总价：</span><span class="totalprice" id="totalprice"><?php echo $price; ?></span><span>RMB</span> </div>
  <?php } ?>
  <div id="nextbutton"> 
    <a href="<?php echo sys_href($request['p'])?>"><img src="<?php echo $tag['path.skin']; ?>res/images/basket/goonbuy.jpg" width="130" height="35" align="left" /></a>
    <?php if($flag){ ?>
    <a id="submitbasket" href="<?php echo sys_href($request['p'],'product_basketqr')?>" ><img src="<?php echo $tag['path.skin']; ?>res/images/basket/settlement.jpg" width="130" height="35" align="right" /></a>
    <?php } ?>
  </div>
</div>