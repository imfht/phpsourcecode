<?php
/**
 * 123PHPSHOP
 * ============================================================================
 * 版权所有 2015 上海序程信息科技有限公司，并保留所有权利。
 * 网站地址: http://www.123PHPSHOP.com；
 * ----------------------------------------------------------------------------
 * 这是一个免费的软件。您可以在商业目的和非商业目的地前提下对程序除本声明之外的
 * 代码进行修改和使用；您可以对程序代码以任何形式任何目的的再发布，但一定请保留
 * 本声明和上海序程信息科技有限公司的联系方式！本软件中使用到的第三方代码版权属
 * 于原公司所有。上海序程信息科技有限公司拥有对本声明和123PHPSHOP软件使用的最终
 * 解释权！
 * ============================================================================
 *  作者:	123PHPSHOP团队
 *  手机:	13391334121
 *  邮箱:	service@123phpshop.com
 */
?>
<?php require_once('Connections/localhost.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>支付结果</title>
<style type="text/css">
<!--
.STYLE1 {
	font-size: 27px;
	font-weight: bold;
}
.STYLE2 {font-size: 14px}

a{
	text-decoration:none;
	color:#75a8d3;
 }
-->
</style>
</head>

<body style="margin:0px;">
<?php include_once('/widget/top_full_nav.php'); ?>
<?php include_once('/widget/logo_search_cart.php'); ?>
<div align="center">
<div style="-webkit-border-radius:10px;-moz-border-radius:10px;margin:auto 0;height:294px;width:593px;border:2px solid #75a8d3;">
  <table width="593" height="294" border="0" align="center" cellpadding="0">
    <tr>
      <td height="160"><div align="center"><span class="STYLE1">恭喜您，支付成功!</span></div></td>
    </tr>
    <tr>
      <td><div align="center" class="STYLE2">您现在可以<a href="user/index.php?path=order/detail.php?id=">查看订单状态</a>或是<a href="javascript:window.opener=null;window.open('','_self');window.close();">关闭本窗口</a></div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
</div>
</div>
</body>
</html>
