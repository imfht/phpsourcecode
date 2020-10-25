<?php
define('REGED',false);
function ewebeditor($style,$name,$content='',$id='')
{
	$id = empty($id)?$name:$id;
	switch($style)
	{
		case 'kindeditor':
		    return  '<textarea id="'.$id.'" name="'.$name.'" cols="100" rows="8" style="width:95%;height:400px;">'.$content.'</textarea>
			<script type="text/javascript">
					var editor;
					KindEditor.ready(function(K) {
							editor = K.create("#'.$id.'",{allowFileManager : true , filterMode:false});
					});</script>';
			break;
		case 'ueditor':
			return   '<script type="text/plain" id="'.$id.'" name="'.$name.'" style="width:95%;">
'.$content.'</script>
			<script type="text/javascript">
			  var ue = new UE.ui.Editor();
			  ue.render("'.$id.'");
			  ue.addListener("selectionchange",function(){
			 });</script>';
			break;
		default:
			break;
	}
}