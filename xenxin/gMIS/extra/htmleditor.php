<?php

# 
# auto-save, Wed Aug 13 15:55:22 CST 2014

$isoput = 0;
require("../comm/header.inc.php");

$myfield = trim($_REQUEST['field']);

?>
<!DOCTYPE html><html>
<head>
<!-- other stuff -->
<title> UEditor </title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

<script type="text/javascript" charset="utf-8">
    window.UEDITOR_HOME_URL = "<?php print $rtvdir;?>/extra/ueditor/";
</script>
<script type="text/javascript" charset="utf-8" src="<?php print $rtvdir;?>/extra/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php print $rtvdir;?>/extra/ueditor/ueditor.all.min.js"></script>

</head>
<body>

<script type="text/plain" id="myeditor" name="mycontent">

</script>

<input type="button" name="sendback" value="保存" onclick="javascript:parent.setCont('<?php print $myfield;?>', ue.getContent()); parent.switchArea('<?php print $myfield;?>_myeditordiv', 'off'); parent.switchArea('<?php print $myfield."_mytextdiv";?>', 'on');" />

<input type="button" name="cancelit" value="放弃" onclick="javascript:parent.switchArea('<?php print $myfield."_myeditordiv";?>', 'off'); parent.switchArea('<?php print $myfield."_mytextdiv";?>', 'on');" />

<script type="text/javascript">
console.log('getcont:['+parent.getCont('<?php print $myfield;?>')+']!');
var ue = UE.getEditor('myeditor', {});
//ue.setContent(parent.getCont('content'));
document.getElementById('myeditor').value = parent.getCont('<?php print $myfield;?>');
//document.body.onunload = function(){ parent.setCont('content', document.getElementById('uube_mta').innerText);}

// auto-save, Wed Aug 13 15:55:22 CST 2014
var autoSaveInterval = <?php print $_CONFIG['auto_save_interval']; ?> * 1000; // 60*1000;
window.setTimeout('setAutoSave('+autoSaveInterval+')', autoSaveInterval);
function setAutoSave(autoSaveInterval){
	parent.setCont('<?php print $myfield;?>',ue.getContent()); 
	window.setTimeout('setAutoSave('+autoSaveInterval+')', autoSaveInterval);	
}

</script>

</body>
</html>

<?php

require("../comm/footer.inc.php");

?>