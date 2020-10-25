<?php
include_once("config.php");

function TinyMCEScriptPrint($AreaName = "")
{
	global $SYSTEM;
?>
	<!-- tinyMCE -->
	<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/tinymce/tiny_mce.js?<?php echo $SYSTEM['version']?>"></script>
	<script language="javascript" type="text/javascript">
		tinyMCE.init({
<?php
	if ($AreaName == "") {
		echo '			mode: "textareas",'."\n";
	} else {
		echo '			mode: "exact",'."\n";
		echo '			elements: "'.$AreaName.'",'."\n";
	}
?>
			theme : "advanced",
			plugins: "table",
			theme_advanced_path : false,
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_resize_horizontal : false,
			theme_advanced_resizing : true,
			theme_advanced_resizing_use_cookie : false,
			theme_advanced_path_location : "bottom",
			theme_advanced_buttons1 : "fontselect,fontsizeselect,bold,italic,underline,strikethrough,separator,sub,sup,separator,cut,copy,paste,separator,undo,redo",
			theme_advanced_buttons2 : "justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,outdent,indent,separator,forecolor,backcolor,separator,link,unlink,anchor,separator,image,charmap,removeformat",
			theme_advanced_buttons3 : "tablecontrols",
			content_css : "<?php echo $GLOBALS["SYS_URL_ROOT"]?>/style.css",
			language : "en"
		});
	</script>
	<!-- /tinyMCE -->
<?php
}

?>
