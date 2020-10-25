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
<style type="text/css">
.orderinfo{ width:100%; float:left; overflow:hidden;}
.orderinfo b{ }
.orderinfo li{ margin-left:100px; list-style:none; line-height:30px;}
.orderinfo li a{ text-decoration:none;}
.orderbox { border: 2px solid #BBDFFF; float:left; margin-bottom:15px; overflow: hidden;  padding: 15px 0 15px 30px; width:906px;}
.orderbox h2{ width:860px; border-bottom:1px solid #def; font-size:20px; float:left; padding:0 0 5px 10px; margin-bottom:15px;}
.ordes{ width:870px; float:left; margin-bottom:30px;}
.ordes td{ padding:10px 10px 0 10px; font-size:14px; line-height:30px;}
.ortitle{ font-weight:bold; color:#333; background:#def;}
.orprice{ color:#c33; font-size:16px; font-weight:bold; font-family:"微软雅黑";}
.payway{ width:800px; float:left;}
.payway td{ padding:10px 10px 0 10px; line-height:45px;}
.payway img{ margin-right:45px;}
input.formbutton{padding:4px 1em;*padding:5px 1.5em 0;border:2px solid;border-color:#82D0D4 #4D989B #54A3A7 #92D6D9;background:#63C5C8;color:#fff;letter-spacing:.1em;cursor:pointer;*width:auto;_width:0;*overflow:visible;}
.ordes a{ color:#399;}
.ordes .paybut{ padding-top:50px;}

.mesWindow{background:#fff; width:345px; border:1px solid #999;}
.mesWindowTop{ height:35px; padding:6px 10px 0 17px; margin-bottom:30px;}
.mesWindowTop span{ float:left;}
.mesWindowTop .close{background:url(<?php echo $tag['path.skin']; ?>res/images/x.jpg);border:none;cursor:pointer;width:46px;height:16px;float:right;}
.mesWindowContent{}
#abcd{ width:315px; height:305px; padding-left:35px;}
#abcd p{ width:280px; float:left; padding:0; margin:0; font-size:14px;}
#abcd .orremind{ line-height:20px; margin-bottom:15px;}
#abcd h4{ background:url(<?php echo $tag['path.skin']; ?>res/images/remind.jpg) left top no-repeat; padding:0 0 35px 100px; font-size:14px; color:#000; font-family:"微软雅黑"; width:160px; line-height:28px; float:left; margin:0;}
.orbut{ margin-right:15px;}
.orfh a{ display:block; margin-top:30px; color:#399; text-decoration:none;}
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
.savebt { -moz-border-bottom-colors: none; -moz-border-image: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background-color: #7FBF4D; background-image: -moz-linear-gradient(center top, #7FBF4D, #63A62F); border-color: #63A62F #63A62F #5B992B; border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; box-shadow: 0 1px 0 0 #96CA6D inset; color: #FFFFFF; font: 12px; padding:0.1em 1em; text-align: center; text-shadow: 0 -1px 0 #4C9021; cursor:pointer; float:left; font-size:14px;}
.savebt:hover { background-color: #76B347; background-image: -moz-linear-gradient(center top, #76B347, #5E9E2E); box-shadow: 0 1px 0 0 #8DBF67 inset; cursor: pointer; color:#fff; text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.3); }
.clear { clear:both; }
</style>
<div id="crs">
  <div id="mycart"> <span id="mycarttitle">我的购物车</span>
    <ol id="step3">
      <li>加入购物车</li>
      <li>确认订单</li>
      <li id="selected">提交订单</li>
    </ol>
    <div class="orderdetails"> <img src="<?php echo $tag['path.skin']; ?>res/images/basket/cart3_bg.png" width="81" height="95" style="margin:30px 15px 0 150px" align="left" />
      <h5>您的订单已提交，请耐心等待。</h5>
    </div>
    <?php 
	if(PAY_ISPAY){//是否开启支付接口
	$basket=parseCookie($_COOKIE['doc_basket']);
	?>
    <div class="orderinfo"> <li><b>收货人信息</b></li>
      <li>收&nbsp;货&nbsp;人：<?php echo $basket['customer']?></li>
      <li>地&nbsp;&nbsp;&nbsp;&nbsp;址：<?php echo $basket['address']?></li>
      <li>手机号码：<?php echo $basket['m_tel']?></li>
      <li><a href="<?php echo sys_href('index','pay')?>" class="savebt" >确认付款 </a></li>
    </div>
    <?php }?>
    <div id="nextbutton">
      <a href="<?php echo sys_href($request['p'])?>"><img src="<?php echo get_skin_root(); ?>res/images/basket/goonbuy.jpg" width="130" height="35" align="left" /></a>   
    </div>
  </div>
</div>
