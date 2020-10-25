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
<?php require_once('../../Connections/localhost.php'); ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}



$colname_product = "-1";
if (isset($_GET['id'])) {
  $colname_product = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_product = sprintf("SELECT * FROM product WHERE id = %s", $colname_product);
$product = mysql_query($query_product, $localhost) or die(mysql_error());
$row_product = mysql_fetch_assoc($product);
$totalRows_product = mysql_num_rows($product);

mysql_select_db($database_localhost, $localhost);
$query_brands = "SELECT id, name FROM brands";
$brands = mysql_query($query_brands, $localhost) or die(mysql_error());
$row_brands = mysql_fetch_assoc($brands);
$totalRows_brands = mysql_num_rows($brands);

mysql_select_db($database_localhost, $localhost);
$query_product_types = "SELECT * FROM product_type WHERE pid = 0 and is_delete=0";
$product_types = mysql_query($query_product_types, $localhost) or die(mysql_error());
$row_product_types = mysql_fetch_assoc($product_types);
$totalRows_product_types = mysql_num_rows($product_types);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
 
	$on_sheft_time='';
	if($_POST['is_on_sheft']=='1' && $row_product['on_sheft_time']==null){
		 $on_sheft_time=date('Y-m-d H:i:s');
	}
	
	if($_POST['is_on_sheft']=='1' && $row_product['on_sheft_time']!=null){
		 $on_sheft_time=$row_product['on_sheft_time'];
	}

//	如果需要上架的话
 $updateSQL = sprintf("UPDATE product SET is_shipping_free=%s,meta_keywords=%s,meta_desc=%s,description=%s,product_type_id=%s, unit=%s,weight=%s,is_virtual=%s,on_sheft_time=%s,name=%s, ad_text=%s, price=%s, market_price=%s, is_on_sheft=%s, is_hot=%s, is_season=%s, is_recommanded=%s, store_num=%s, intro=%s, brand_id=%s WHERE id=%s",
						GetSQLValueString($_POST['is_shipping_free'], "int"),
						GetSQLValueString($_POST['meta_keywords'], "text"),
						GetSQLValueString($_POST['meta_desc'], "text"),
						GetSQLValueString($_POST['description'], "text"),
					   GetSQLValueString($_POST['product_type_id'], "text"),
 					   GetSQLValueString($_POST['unit'], "text"),
                       GetSQLValueString($_POST['weight'], "double"),
                       GetSQLValueString($_POST['is_virtual'], "int"),
					   GetSQLValueString($on_sheft_time, "date"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['ad_text'], "text"),
                       GetSQLValueString($_POST['price'], "double"),
                       GetSQLValueString($_POST['market_price'], "double"),
                       GetSQLValueString($_POST['is_on_sheft'], "int"),
                       GetSQLValueString($_POST['is_hot'], "text"),
                       GetSQLValueString($_POST['is_season'], "text"),
                       GetSQLValueString($_POST['is_recommanded'], "text"),
                       GetSQLValueString($_POST['store_num'], "int"),
                       GetSQLValueString($_POST['intro'], "text"),
					   GetSQLValueString($_POST['brand_id'], "int"),
                       GetSQLValueString($_POST['id'], "int"));
 
  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());

  $updateGoTo = "../product/index.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form method="post" name="form1"  id="form1"  action="<?php echo $editFormAction; ?>">
  <p class="phpshop123_title">更新产品信息</p>
  <table align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">名称:</td>
      <td><input name="name" type="text" id="name" value="<?php echo $row_product['name']; ?>" size="32" maxlength="50"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">广告语:</td>
      <td><input name="ad_text" type="text" id="ad_text"  value="<?php echo $row_product['ad_text']; ?>" size="32" maxlength="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">价格:</td>
      <td><input name="price" type="text" id="price" value="<?php echo $row_product['price']; ?>" size="32" maxlength="13"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">市场价:</td>
      <td><input name="market_price" type="text"  id="market_price" value="<?php echo $row_product['market_price']; ?>" size="32" maxlength="13">	</td>
    </tr>
	<tr valign="baseline">
      <td nowrap align="right">品牌:</td>
      <td><select name="brand_id" id="brand_id">
        <option value="0" <?php if (!(strcmp(0, $row_product['brand_id']))) {echo "selected=\"selected\"";} ?>>未设置</option>
        <?php
do {  
?>
        <option value="<?php echo $row_brands['id']?>"<?php if (!(strcmp($row_brands['id'], $row_product['brand_id']))) {echo "selected=\"selected\"";} ?>><?php echo $row_brands['name']?></option>
        <?php
} while ($row_brands = mysql_fetch_assoc($brands));
  $rows = mysql_num_rows($brands);
  if($rows > 0) {
      mysql_data_seek($brands, 0);
	  $row_brands = mysql_fetch_assoc($brands);
  }
?>
      </select></td>
    </tr>
	
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">商品类型：</td>
      <td><select name="product_type_id" id="product_type_id">
        <option value="0" <?php if (!(strcmp(0, $row_product['product_type_id']))) {echo "selected=\"selected\"";} ?>>未设置</option>
        <?php
do {  
?>
        <option value="<?php echo $row_product_types['id']?>"<?php if (!(strcmp($row_product_types['id'], $row_product['product_type_id']))) {echo "selected=\"selected\"";} ?>><?php echo $row_product_types['name']?></option>
        <?php
} while ($row_product_types = mysql_fetch_assoc($product_types));
  $rows = mysql_num_rows($product_types);
  if($rows > 0) {
      mysql_data_seek($product_types, 0);
	  $row_product_types = mysql_fetch_assoc($product_types);
  }
?>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">重量：</td>
      <td valign="baseline"><input name="weight" type="text"  id="weight" value="<?php echo $row_product['weight']; ?>" size="32" maxlength="13" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">单位：</td>
      <td valign="baseline"><input name="unit" type="text"  id="unit" value="<?php echo $row_product['unit']; ?>" size="32" maxlength="13" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">虚拟物品：</td>
      <td valign="baseline"><input type="radio" name="is_virtual" value="1" <?php if (!(strcmp($row_product['is_virtual'],"1"))) {echo "CHECKED";} ?> />
是
  <input type="radio" name="is_virtual" value="0" <?php if (!(strcmp($row_product['is_virtual'],"0"))) {echo "CHECKED";} ?> />
否</td>
    </tr> 
    <tr valign="baseline">
      <td nowrap align="right">上架:</td>
      <td valign="baseline"><input type="radio" name="is_on_sheft" value="1" <?php if (!(strcmp($row_product['is_on_sheft'],"1"))) {echo "CHECKED";} ?> />
是
  <input type="radio" name="is_on_sheft" value="0" <?php if (!(strcmp($row_product['is_on_sheft'],"0"))) {echo "CHECKED";} ?> />
  否</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">热门商品:</td>
      <td valign="baseline"><input type="radio" name="is_hot" value="1" <?php if (!(strcmp($row_product['is_hot'],"1"))) {echo "CHECKED";} ?> />
是
  <input type="radio" name="is_hot" value="0" <?php if (!(strcmp($row_product['is_hot'],"0"))) {echo "CHECKED";} ?> />
  否</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">当季商品:</td>
      <td valign="baseline"><input type="radio" name="is_season" value="1" <?php if (!(strcmp($row_product['is_season'],"1"))) {echo "CHECKED";} ?> />
是
  <input type="radio" name="is_season" value="0" <?php if (!(strcmp($row_product['is_season'],"0"))) {echo "CHECKED";} ?> />
  否</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">推荐商品:</td>
      <td valign="baseline"><input type="radio" name="is_recommanded" value="1" <?php if (!(strcmp($row_product['is_recommanded'],"1"))) {echo "CHECKED";} ?> />
是
  <input type="radio" name="is_recommanded" value="0" <?php if (!(strcmp($row_product['is_recommanded'],"0"))) {echo "CHECKED";} ?> />
  否</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">是否免运费</td>
      <td><input type="radio" name="is_shipping_free" value="1" <?php if (!(strcmp($row_product['is_shipping_free'],"1"))) {echo "CHECKED";} ?> />
是
  <input type="radio" name="is_shipping_free" value="0" <?php if (!(strcmp($row_product['is_shipping_free'],"0"))) {echo "CHECKED";} ?> />
否[选择是则这个产品不会计算进入运费]</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">库存:</td>
      <td><input name="store_num" type="text" id="store_num" value="<?php echo $row_product['store_num']; ?>" size="32" maxlength="11"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">标签：</td>
      <td><label>
        <input name="tags" type="text" id="tags" size="32" value="<?php echo $row_product['tags']; ?>" maxlength="50" />
      [2个标签之间请以空格隔开]</label></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">meta关键词</td>
      <td><label>
        <input name="meta_keywords" type="text" id="meta_keywords" value="<?php echo $row_product['meta_keywords']; ?>" />
      </label></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">meta描述</td>
      <td><input name="meta_desc" type="text" id="meta_desc" value="<?php echo $row_product['meta_desc']; ?>" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">备注</td>
      <td><input name="description" type="text" id="description" value="<?php echo $row_product['description']; ?>" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">介绍:</td>
      <td><script id="editor" type="text/plain" name="intro" style="width:1024px;height:500px;"><?php echo $row_product['intro']; ?></script>      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="更新记录"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1">
  <input type="hidden" name="id" value="<?php echo $row_product['id']; ?>">
</form>
<script type="text/javascript" charset="utf-8" src="/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/js/ueditor/ueditor.all.min.js"> </script>
<script type="text/javascript" charset="utf-8" src="/js/ueditor/lang/zh-cn/zh-cn.js"></script>
 <script language="JavaScript" type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/jquery.validate.min.js"></script>

<script>
$().ready(function(){
 	$("#form1").validate({
        rules: {	
            name: {
                required: true,
				remote:{
                    url: "ajax_update_product_name.php",
                    type: "post",
                    dataType: 'json',
                    data: {
                        'name': function(){return $("#name").val();},
						'id': function(){return <?php echo $colname_product;?>;}
                    }
				}
            },
            price: {
                required: true,
				number:true   
            } ,
            market_price: {
				 required: true,
				 number:true 					 
            },
			tags:{
				required: true
			},
            store_num: {
				 digits:true    				 
            } 
        },
        messages: {
			name: {
  				remote:"产品已存在"
            } 
        }
    });
});

//实例化编辑器
    //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
    var ue = UE.getEditor('editor');


    function isFocus(e){
        alert(UE.getEditor('editor').isFocus());
        UE.dom.domUtils.preventDefault(e)
    }
    function setblur(e){
        UE.getEditor('editor').blur();
        UE.dom.domUtils.preventDefault(e)
    }
    function insertHtml() {
        var value = prompt('插入html代码', '');
        UE.getEditor('editor').execCommand('insertHtml', value)
    }
    function createEditor() {
        enableBtn();
        UE.getEditor('editor');
    }
    function getAllHtml() {
        alert(UE.getEditor('editor').getAllHtml())
    }
    function getContent() {
        var arr = [];
        arr.push("使用editor.getContent()方法可以获得编辑器的内容");
        arr.push("内容为：");
        arr.push(UE.getEditor('editor').getContent());
        alert(arr.join("\n"));
    }
    function getPlainTxt() {
        var arr = [];
        arr.push("使用editor.getPlainTxt()方法可以获得编辑器的带格式的纯文本内容");
        arr.push("内容为：");
        arr.push(UE.getEditor('editor').getPlainTxt());
        alert(arr.join('\n'))
    }
    function setContent(isAppendTo) {
        var arr = [];
        arr.push("使用editor.setContent('欢迎使用ueditor')方法可以设置编辑器的内容");
        UE.getEditor('editor').setContent('欢迎使用ueditor', isAppendTo);
        alert(arr.join("\n"));
    }
    function setDisabled() {
        UE.getEditor('editor').setDisabled('fullscreen');
        disableBtn("enable");
    }

    function setEnabled() {
        UE.getEditor('editor').setEnabled();
        enableBtn();
    }

    function getText() {
        //当你点击按钮时编辑区域已经失去了焦点，如果直接用getText将不会得到内容，所以要在选回来，然后取得内容
        var range = UE.getEditor('editor').selection.getRange();
        range.select();
        var txt = UE.getEditor('editor').selection.getText();
        alert(txt)
    }

    function getContentTxt() {
        var arr = [];
        arr.push("使用editor.getContentTxt()方法可以获得编辑器的纯文本内容");
        arr.push("编辑器的纯文本内容为：");
        arr.push(UE.getEditor('editor').getContentTxt());
        alert(arr.join("\n"));
    }
    function hasContent() {
        var arr = [];
        arr.push("使用editor.hasContents()方法判断编辑器里是否有内容");
        arr.push("判断结果为：");
        arr.push(UE.getEditor('editor').hasContents());
        alert(arr.join("\n"));
    }
    function setFocus() {
        UE.getEditor('editor').focus();
    }
    function deleteEditor() {
        disableBtn();
        UE.getEditor('editor').destroy();
    }
    function disableBtn(str) {
        var div = document.getElementById('btns');
        var btns = UE.dom.domUtils.getElementsByTagName(div, "button");
        for (var i = 0, btn; btn = btns[i++];) {
            if (btn.id == str) {
                UE.dom.domUtils.removeAttributes(btn, ["disabled"]);
            } else {
                btn.setAttribute("disabled", "true");
            }
        }
    }
    function enableBtn() {
        var div = document.getElementById('btns');
        var btns = UE.dom.domUtils.getElementsByTagName(div, "button");
        for (var i = 0, btn; btn = btns[i++];) {
            UE.dom.domUtils.removeAttributes(btn, ["disabled"]);
        }
    }

    function getLocalData () {
        alert(UE.getEditor('editor').execCommand( "getlocaldata" ));
    }

    function clearLocalData () {
        UE.getEditor('editor').execCommand( "clearlocaldata" );
        alert("已清空草稿箱")
    }

</script>
</body>
</html>
<?php
mysql_free_result($product);

mysql_free_result($brands);

mysql_free_result($product_types);
?>

