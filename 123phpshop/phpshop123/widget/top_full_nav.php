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
<?php

$logoutAction = "/index.php?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

?>
<style>
#top_full_nav{
 	font-size:12px;
	background-color:#F1F1F1;
	height:30px;
	border-bottom:1px solid #eee;
	
 }
#top_full_nav a{
	text-decoration:none;
	color:#666;
}
.top_a{
	float:left;
	width:71px;
 }
</style>

<div id="top_full_nav" align="center" >
	<div style="width:1210px;margin: auto 0;line-height:30px;">
 	 <div style="float:left;"><a href="javascript:addFavorite(this)">添加到收藏</a></div> 
     <div style="float:right;">
	 	<div class="top_a"><?php echo isset($_SESSION['username'])?$_SESSION['username']:""; ?></div>
		<?php if(!isset($_SESSION['username'])){ ?>
			<div class="top_a"><a href="/login.php">请登录</a></div> 
			<div class="top_a"><a href="/register.php">免费注册</a></div>
		<?php } else{?>
		<div class="top_a"><a href="/user/index.php?path=order/index.php" target="_blank">我的订单</a></div>
		<div class="top_a"><a href="/user/index.php" target="_blank">用户中心</a></div>
		<div class="top_a"><a href="<?php echo $logoutAction ?>" target="_parent">退出</a></div>
		<?php }?>
	 </div>
     </div>
</div>

<script>
//加入收藏函数
        function addFavorite() {
          var a="<?php echo $_SERVER['HTTP_HOST'];?>",b="\u4eac\u4e1c<?php echo $_SERVER['HTTP_HOST'];?>-\u7f51\u8d2d\u4e0a\u4eac\u4e1c\uff0c\u7701\u94b1\u53c8\u653e\u5fc3";document.all?window.external.AddFavorite(a,b):window.sidebar&&window.sidebar.addPanel?window.sidebar.addPanel(b,a,""):alert("\u5bf9\u4e0d\u8d77\uff0c\u60a8\u7684\u6d4f\u89c8\u5668\u4e0d\u652f\u6301\u6b64\u64cd\u4f5c!\n\u8bf7\u60a8\u4f7f\u7528\u83dc\u5355\u680f\u6216Ctrl+D\u6536\u85cf\u672c\u7ad9\u3002"),createCookie("_fv","1",30,"/;domain=<?php echo $_SERVER['HTTP_HOST'];?>") 
        }
</script>
