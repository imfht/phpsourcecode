<style>
.buzou span { width:20%; display:block; float:left; line-height:25px; text-align:center; font-weight:bold; }
.yu_list { width:95%; background:#F7FBFF; margin:0 auto; padding:30px 20px; }
<!--
.orderbox { background: #FFFFFF; border: 2px solid #BBDFFF; margin-bottom: 15px; overflow: hidden; padding: 15px 0 15px 30px; width: 96%; }
.orderbox h2 { width:860px; border-bottom:1px solid #def; font-size:20px; float:left; padding:0 0 5px 10px; margin-bottom:15px; }
.ordes { float:left; margin-bottom:30px; }
.ordes td { padding:10px 10px 0 10px; font-size:14px; line-height:30px; }
.ortitle { font-weight:bold; color:#333; background:#def; }
.orprice { color:#c33; font-size:16px; font-weight:bold; font-family:"微软雅黑"; }
.payway { float:left; }
.payway td { padding:10px 10px 0 10px; line-height:45px; }
.payway img { margin-right:45px; }
input.formbutton { padding:4px 1em; *padding:5px 1.5em 0;
border:2px solid; border-color:#82D0D4 #4D989B #54A3A7 #92D6D9; background:#63C5C8; color:#fff; letter-spacing:.1em; cursor:pointer; *width:auto;
_width:0; *overflow:visible;
}
.ordes {color:#399; }
.ordes a { color:#399; }
.ordes .paybut { padding-top:50px; }
.mesWindow { background:#fff; width:345px; border:1px solid #999; }
.mesWindowTop { height:35px; padding:6px 10px 0 17px; margin-bottom:30px; }
.mesWindowTop span { float:left; }
.mesWindowTop .close { background:url(<?php echo $tag['path.skin']?>/res/images/x.jpg); border:none; cursor:pointer; width:46px; height:16px; float:right; }
.mesWindowContent { }
#abcd { width:315px; height:305px; padding-left:35px; }
#abcd p { width:280px; float:left; padding:0; margin:0; font-size:14px; }
#abcd .orremind { line-height:20px; margin-bottom:15px; }
#abcd h4 { background:url(<?php echo $tag['path.skin']?>/res/images/remind.jpg) left top no-repeat; padding:0 0 35px 100px; font-size:14px; color:#000; font-family:"微软雅黑"; width:160px; line-height:28px; float:left; margin:0; }
.orbut { margin-right:15px; }
.orfh a { display:block; margin-top:30px; color:#399; text-decoration:none; }
-->
</style>
<script type="text/javascript" src="<?php echo $tag['path.skin']?>/res/js/popup.js"></script>
<script>
function testMessageBox(ev)
{
var objPos = mousePosition(ev);
var cont = '<div id="abcd"><p><h4>请您再新打开的页面上完成付款。</h4></p><p class="orremind">付款完成前请不要关闭此窗口。<br>完成付款后请根据您的情况点击下面的按钮：</p><p><input class="formbutton orbut" value="已完成付款" type="button" onclick="window.location.href=\'/?m=user\'"/><input class="formbutton" value="付款遇到问题" type="button" onclick="closeWindow();" /></p><p class="orfh"><a href="javascript:;" onclick="closeWindow();" ><<返回选择其他支付方式</a></p></div>';
messContent=cont;
showMessageBox('',messContent,objPos,350);
}
</script>
<script>
function pay()
{
	var type ='';
	var pay=document.getElementsByName("payway");
	for(i=0;i<pay.length;i++) 
	{ 
	   if(pay[i].checked) 
	   { 
		   var type = pay[i].value;
	   } 
	}
	if(type=='')
	{
		alert('请选择您的支付方式');
	}
	else if(type=='alipay')
	{
		document.form1.action= '<?php echo sys_href('alipay','pay')?>';
		document.form1.submit();
		testMessageBox(event);
	}
	else
	{
		document.form1.action= '<?php echo sys_href('tenpay','pay')?>';
		document.form1.submit();
		testMessageBox(event);
	}
}
</script>
<?php global $pfileName,$request;?>
<?php
if(empty(sys_get_session('pay_orderId')))
{
	sys_set_session('pay_orderId',$request['orderId']);
	sys_set_session('pay_subject',$request['subject']);
	sys_set_session('pay_body',$request['body']);
	sys_set_session('pay_price',$request['price']);
}
$subject = @explode('<@>',sys_get_session('pay_subject'));
$body    = @explode('<@>',sys_get_session('pay_body'));
$price   = @explode('<@>',sys_get_session('pay_price'));
?>
<div class="yu_list">
  <div class="orderbox">
    <h2>您的订单</h2>
    <form name="form1" action="" method="post" target="_blank">
    <table width="97%" border="0" cellspacing="0" cellpadding="0" class="ordes">
      <tr height="40">
        <td class="ortitle" width="85%">商品</td>
        <td class="ortitle">价格</td>
      </tr>
      <?php 
	  for($i=0;$i<count($subject);$i++)
	  {
	  ?>
      <tr height="30">
        <td ><?php echo $subject[$i]?> </td>
        <td class="orprice">￥<?php echo $price[$i]?></td>
      </tr>
      <?php }?>
      <tr height="30">
        <td style="text-align:right">总价：</td>
        <td class="orprice">￥<?php echo array_sum($price)?></td>
      </tr>
    </table>
      <table width="97%" border="0" cellspacing="0" cellpadding="0" class="ordes">
        <tr height="40">
          <td class="ortitle">请选择支付方式</td>
        </tr>
        <tr>
          <td><table width="97%"class="payway">
              <tr>
                <td><label>
                    <input type="radio" name="payway" value="alipay" id="payway_0" />
                    <img src="<?php echo $tag['path.skin']?>/res/images/alipay.jpg" alt="支付宝" />推荐淘宝用户使用</label></td>
              </tr>
              <tr>
                <td><label>
                    <input type="radio" name="payway" value="tenpay" id="payway_1" />
                    <img src="<?php echo $tag['path.skin']?>/res/images/tenpay.jpg" alt="财付通" />无需注册，支持国内各大银行支付</label></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td class="paybut"><input id="order-pay-button" class="formbutton" value="确认订单，付款" type="button" onclick="pay()">
            <a style="margin-left: 1em" href="javascript:history.go(-1)">返回修改订单</a></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div class="footer"></div>
