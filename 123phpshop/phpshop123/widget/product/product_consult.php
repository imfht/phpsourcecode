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
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/localhost.php'); ?>
<?php
$colname_product = "-1";
if (isset($_GET['id'])) {
  $colname_product = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "new_consult") && $colname_product!='-1' && isset($_SESSION['user_id']) && isset($_POST['captcha']) && ($_POST['captcha']==$_SESSION['captcha']) ){


 //	  检查是否输入了验证码？如果么有输入,或是输入的验证码是否和SESSION中的验证码不一致，那么直接跳转到失败页面
 
   
	  $insertSQL = sprintf("INSERT INTO product_consult (user_id,content, product_id) VALUES (%s, %s, %s)",
						   GetSQLValueString($_SESSION['user_id'], "int"),
						   GetSQLValueString($_POST['content'], "text"),
						   GetSQLValueString($colname_product, "int"));
	
	  mysql_select_db($database_localhost, $localhost);
	  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());
	  
	  $update_sql=sprintf("update product set consulted_num=consulted_num+1 where id=%s",GetSQLValueString($colname_product, "int"));
	  $Result2 = mysql_query($update_sql, $localhost) or die(mysql_error());
 
}
 
$colname_consult = "-1";
if (isset($_GET['id'])) {
  $colname_consult = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_consult = sprintf("SELECT product_consult.*,user.username FROM product_consult inner join user on user.id=product_consult.user_id WHERE product_consult.product_id = %s and product_consult.is_delete = 0 ORDER BY product_consult.id DESC", $colname_consult);
$consult = mysql_query($query_consult, $localhost) or die(mysql_error());
$row_consult = mysql_fetch_assoc($consult);
$totalRows_consult = mysql_num_rows($consult);

 
?>
<style type="text/css">
<!--
.STYLE2 {color: #666}
-->
</style>
<br />
<style>
#consult_list{
	font-size:12px;
}

</style>
 <?php if ($totalRows_consult > 0) { // Show if recordset not empty ?>
<table width="990" height="31" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>
		<table style="background-color:white;border-top:2px solid red;border-bottom-width:0px" width="105" height="33" border="1" cellpadding="0" cellspacing="0" bordercolor="#DEDFDE">
		  <tr>
			<td><div align="center"><a style="text-decoration:none;color:#000000;" href="javascript://" name="consult" id="consult">咨询列表</a>[<?php echo $totalRows_consult;?>]</div></td>
		  </tr>
		</table>
	</td>
	
    <td><table  style="border-bottom:1px solid #DEDFDE " width="885" height="31" border="0">
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
<?php } // Show if recordset not empty ?>
            <?php if ($totalRows_consult > 0) { // Show if recordset not empty ?>
            <table id="consult_list" width="990" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#ddd" style="margin:0px auto;border-bottom:1px dotted grey;" >
            <?php do { ?>
                <tr >
                  <td  height="18" style="padding-top:5px;"><span class="STYLE2">网友：<?php echo $row_consult['username']; ?> <?php echo $row_consult['create_time']; ?></span></td>
                </tr>
				
				
				<?php 
					mysql_select_db($database_localhost, $localhost);
						$query_replay = "SELECT * FROM product_consult WHERE to_question = ".$row_consult['id']." and is_delete=0 order by id desc limit 1";
						$replay = mysql_query($query_replay, $localhost) or die(mysql_error());
						$row_replay = mysql_fetch_assoc($replay);
						$totalRows_replay = mysql_num_rows($replay);
					
				?>
                 <tr >
                  <td height="18" style="padding:5px 0px;<?php if($totalRows_replay==0){ ?>border-bottom:1px dotted grey;<?php }?>">咨询：<?php echo $row_consult['content']; ?></td>
                </tr>
 				<?php 
 					if($totalRows_replay>0){
				?>
				 <tr  >
                  <td height="18" style="padding-top:5px 0px;border-bottom:1px dotted grey;color:#FF6500;">回复：<?php echo $row_replay['content']; ?><div style="float:right;"><?php echo $row_replay['create_time']; ?></div></td>
                </tr>
				 <?php } ?>
            <?php } while ($row_consult = mysql_fetch_assoc($consult)); ?>
          </table>
		  
          <?php } // Show if recordset not empty ?>
<?php if(isset($_SESSION['user_id'])){?>
<form action="<?php echo $editFormAction; ?>" method="post" name="new_consult_form" id="new_consult_form">
<table align="center" width="990" style="margin:0px auto;" >
	<tr valign="baseline">
		<td>&nbsp;</td>
	</tr>
	<tr valign="baseline">
		<td><textarea name="content" cols="120" rows="10"></textarea>	</td>
	</tr>
	<tr valign="middle" >
	  <td style="padding-top:10px;"><label >
	    <input style="height:35px;font-size:20px;line-height:34px;" name="captcha" type="text" size="4" maxlength="4" />
	  </label><img height="37" style="cursor:pointer;float:left;margin-right:5px;" title="点击刷新" src="/captcha.php" align="absbottom" onclick="this.src='/captcha.php?'+Math.random();"><input style="height:35px;margin-left:5px;" name="submit" type="submit" value="马上咨询" /></td>
    </tr>
</table>
<input type="hidden" name="product_id2" value="<?php echo $row_product['id']; ?>" />
<input type="hidden" name="MM_insert" value="new_consult" />
</form>
<?php } ?>
 