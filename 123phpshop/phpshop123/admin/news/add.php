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

ckeditor_upload();

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

$colname_news_catalog = "-1";
if (isset($_GET['catalog_id'])) {
  $colname_news_catalog = (get_magic_quotes_gpc()) ? $_GET['catalog_id'] : addslashes($_GET['catalog_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_news_catalog = sprintf("SELECT * FROM `news_catalog` WHERE id = %s", $colname_news_catalog);
$news_catalog = mysql_query($query_news_catalog, $localhost) or die(mysql_error());
$row_news_catalog = mysql_fetch_assoc($news_catalog);
$totalRows_news_catalog = mysql_num_rows($news_catalog);


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$publish_time=date('Y-m-d H:i:s');
	if($_POST['is_published']==null){
		$publish_time=null;
	}
  $insertSQL = sprintf("INSERT INTO news (title, content, catalog_id, `from`, from_text, is_published, publish_time) VALUES (%s, %s, %s,%s, %s, %s, %s)",
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['content'], "text"),
                       GetSQLValueString($_POST['catalog_id'], "int"), 
					   GetSQLValueString($_POST['from'], "text"),
                       GetSQLValueString($_POST['from_text'], "text"),
                       GetSQLValueString($_POST['is_published']==null?0:1, "int"),
					   GetSQLValueString($publish_time, "text"));
					   
   mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());

  $insertGoTo = "index.php?catalog_id=" . $row_news_catalog['id'] . "";
   
  header(sprintf("Location: %s", $insertGoTo));
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title"><?php echo $row_news_catalog['name']; ?>-&gt;添加文章</p>
<p>&nbsp;</p>

<form method="post" name="form1"  id="form1" action="<?php echo $editFormAction; ?>">
  <table align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">标题:</td>
      <td><input name="title" type="text" class="required" value="" size="32" maxlength="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">来源网站:</td>
      <td><input name="from_text" type="text" class="required" id="from_text" value="本站" size="32" maxlength="10" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">来源网址:</td>
      <td><input value="http://<?php echo $_SERVER['SERVER_NAME'];?>" name="from" type="text" class="required" id="from" size="32" maxlength="50" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">马上发布:</td>
      <td><label>
        <input name="is_published" type="checkbox" id="is_published" value="1" checked="checked" />
      </label></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">内容:</td>
      <td><script id="editor" type="text/plain" name="content" style="width:1024px;height:500px;"></script></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="插入记录"></td>
    </tr>
  </table>
  <input type="hidden" name="catalog_id" value="<?php echo $colname_news_catalog; ?>">
  <input type="hidden" name="MM_insert" value="form1">
</form>
<script type="text/javascript" charset="utf-8" src="/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/js/ueditor/ueditor.all.min.js"> </script>
<script type="text/javascript" charset="utf-8" src="/js/ueditor/lang/zh-cn/zh-cn.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script>
$().ready(function(){
 	$("#form1").validate();
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
mysql_free_result($news_catalog);
?>
