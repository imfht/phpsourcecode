<?php
function index()
{
	global $mapshow,$db,$request;

	$mapshow = $db->get_row("SELECT * FROM ".TB_PREFIX."mapshow WHERE channelId=$request[p]");

}
function edit()
{
	global $request,$db,$mapshow;
	if($_POST)
	{
		$id = $db->get_var("SELECT id FROM ".TB_PREFIX."mapshow WHERE channelId=$request[p]");		
		$mapshow = new mapshow();
		empty($id)?$mapshow->addnew():'';
		$mapshow->id=$id;
		$mapshow->get_request($request);
		$mapshow->channelId	=$request['p'];
		$mapshow->save();	
		redirect("./index.php?p=$request[p]");
	}
	else
	$mapshow = $db->get_row("SELECT * FROM ".TB_PREFIX."mapshow WHERE channelId=$request[p]");	
}
?>