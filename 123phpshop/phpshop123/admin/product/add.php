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
<?php require_once('../../Connections/localhost.php'); ?><?php require_once('../../Connections/localhost.php'); 
mysql_select_db($database_localhost, $localhost);
$query_brands = "SELECT id, name FROM brands";
$brands = mysql_query($query_brands, $localhost) or die(mysql_error());
$row_brands = mysql_fetch_assoc($brands);
$totalRows_brands = mysql_num_rows($brands);

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/lib/catalogs.php');
  
  if($_POST['is_on_sheft']=='0'){
  $insertSQL = sprintf("INSERT INTO product (tags,unit,is_virtual,weight,cata_path,name, ad_text, catalog_id, price, market_price, is_on_sheft, is_hot, is_season, is_recommanded, store_num, intro,brand_id) VALUES (%s,%s,%s,%s,%s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
					   GetSQLValueString($_POST['tags'], "text"),
					   GetSQLValueString($_POST['unit'], "text"),
					   GetSQLValueString($_POST['is_virtual'], "int"),
					   GetSQLValueString($_POST['weight'], "double"),
					   GetSQLValueString("|".get_catalog_path(array($_POST['catalog_id']))."|", "text"),
					   GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['ad_text'], "text"),
                       GetSQLValueString($_POST['catalog_id'], "int"),
                       GetSQLValueString($_POST['price'], "double"),
                       GetSQLValueString($_POST['market_price'], "double"),
                       GetSQLValueString($_POST['is_on_sheft'], "int"),
                       GetSQLValueString($_POST['is_hot'], "text"),
                       GetSQLValueString($_POST['is_season'], "text"),
                       GetSQLValueString($_POST['is_recommanded'], "text"),
                       GetSQLValueString($_POST['store_num'], "int"),
                       GetSQLValueString($_POST['intro'], "text"),
					   GetSQLValueString($_POST['brand_id'], "text"));
}else{
 $insertSQL = sprintf("INSERT INTO product (tags,unit,is_virtual,weight,on_sheft_time,cata_path,name, ad_text, catalog_id, price, market_price, is_on_sheft, is_hot, is_season, is_recommanded, store_num, intro,brand_id) VALUES (%s,%s,%s,%s, %s, %s, %s, %s, %s,%s,%s,%s, %s, %s, %s, %s, %s, %s)",
 					   GetSQLValueString($_POST['tags'], "text"),
  					   GetSQLValueString($_POST['unit'], "text"),
					   GetSQLValueString($_POST['is_virtual'], "int"),
					   GetSQLValueString($_POST['weight'], "double"),
					   GetSQLValueString(date('Y-m-d H:i:s'), "date"),
                       GetSQLValueString("|".get_catalog_path(array($_POST['catalog_id']))."|", "text"),
					   GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['ad_text'], "text"),
                       GetSQLValueString($_POST['catalog_id'], "int"),
                       GetSQLValueString($_POST['price'], "double"),
                       GetSQLValueString($_POST['market_price'], "double"),
                       GetSQLValueString($_POST['is_on_sheft'], "int"),
                       GetSQLValueString($_POST['is_hot'], "text"),
                       GetSQLValueString($_POST['is_season'], "text"),
                       GetSQLValueString($_POST['is_recommanded'], "text"),
                       GetSQLValueString($_POST['store_num'], "int"),
                       GetSQLValueString($_POST['intro'], "text"),
					   GetSQLValueString($_POST['brand_id'], "text"));

}
  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());

  $insertGoTo = "detail.php?recordID=".mysql_insert_id();
   
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<?php
mysql_select_db($database_localhost, $localhost);
$query_product_types = "SELECT * FROM product_type WHERE pid = 0 and is_delete=0";
$product_types = mysql_query($query_product_types, $localhost) or die(mysql_error());
$row_product_types = mysql_fetch_assoc($product_types);
$totalRows_product_types = mysql_num_rows($product_types);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">添加产品 </p>
<form method="post" name="form1" id="form1" action="<?php echo $editFormAction; ?>">
  <table width="100%" align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <th nowrap align="right">名称:</th>
      <td><input name="name" type="text" class="required" id="name"  value="" size="32" maxlength="50">
      *</td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right">广告语:</th>
      <td><input  name="ad_text" type="text" id="ad_text"  value="" size="32" maxlength="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right">价格:</th>
      <td><input  name="price" type="text" class="required" id="price" value="" size="32" maxlength="13">
      *</td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right">市场价:</th>
      <td><input  name="market_price" type="text" class="required" id="market_price" value="" size="32" maxlength="13">
      *</td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right">品牌：</th>
      <td><label>
        <select name="brand_id" id="brand_id">
          <option value="0">未设置</option>
          <?php
do {  
?>
          <option value="<?php echo $row_brands['id']?>"><?php echo $row_brands['name']?></option>
          <?php
} while ($row_brands = mysql_fetch_assoc($brands));
  $rows = mysql_num_rows($brands);
  if($rows > 0) {
      mysql_data_seek($brands, 0);
	  $row_brands = mysql_fetch_assoc($brands);
  }
?>
        </select>
      </label></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right">商品类型：</th>
      <td><select name="product_type_id" id="product_type_id">
        <option value="0">未设置</option>
        <?php
do {  
?>
        <option value="<?php echo $row_product_types['id']?>"><?php echo $row_product_types['name']?></option>
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
      <th nowrap align="right">重量：</th>
      <td><input  name="weight" type="text" class="required" id="weight" value="" size="32" maxlength="13" />
      克</td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right">单位：</th>
      <td><input  name="unit" type="text" class="required" id="unit" value="" size="32" maxlength="5" />
      如盒，箱或支...</td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right">虚拟产品:</th>
      <td><table>
        <tr>
          <td><input type="radio" name="is_virtual" value="1">
            是
            <input  name="is_virtual" type="radio" value="0" checked="checked" />
            否</td>
        </tr>
      </table></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right">是否上架:</th>
      <td><table>
        <tr>
          <td><input type="radio"  checked name="is_on_sheft" value="1" >
            是
            <input type="radio"  name="is_on_sheft" value="0" />
否</td>
        </tr>
      </table></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right">热销产品:</th>
      <td valign="baseline"><table>
        <tr>
          <td><input type="radio"  name="is_hot" value="1" >
            是
              <input type="radio" checked name="is_hot" value="0" />
否</td>
        </tr>
      </table></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right">当季产品:</th>
      <td valign="baseline"><table>
        <tr>
          <td><input type="radio" name="is_season" value="1" >
            是
              <input type="radio"  checked name="is_season" value="0" />
否</td>
        </tr>
      </table></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right">推荐产品:</th>
      <td valign="baseline"><table>
        <tr>
          <td><input type="radio" name="is_recommanded" value="1" >
            是
              <input type="radio" checked  name="is_recommanded" value="0" />
否</td>
        </tr>
      </table></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right">库存:</th>
      <td><input name="store_num" type="text"  class="" id="store_num" value="100" size="32" maxlength="11">
      *</td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right" valign="top">标签:</th>
      <td><label>
        <input name="tags"  id="tags" type="text" id="tags" size="32" maxlength="50" />
      [2个标签之间请以空格隔开]</label></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="right" valign="top">介绍:</th>
      <td><script id="editor" type="text/plain" name="intro" style="width:1024px;height:500px;"></script>
      *      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="插入记录"></td>
    </tr>
  </table>
  <input type="hidden" name="catalog_id" value="<?php echo $_GET['catalog_id']; ?>">
  <input type="hidden" name="MM_insert" value="form1">
</form>
<p>&nbsp;</p>
<script type="text/javascript" charset="utf-8" src="/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/js/ueditor/ueditor.all.min.js"> </script>
<script type="text/javascript" charset="utf-8" src="/js/ueditor/lang/zh-cn/zh-cn.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>
<script>
$().ready(function(){
 	$("#form1").validate({
        rules: {
            name: {
                required: true,
				remote:{
                    url: "ajax_product_name.php",
                    type: "post",
                    dataType: 'json',
                    data: {
                        'name': function(){return $("#name").val();}
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
            store_num: {
				 digits:true    				 
            },
            tags: {
				 required: true   				 
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
mysql_free_result($brands);

mysql_free_result($product_types);
?>
