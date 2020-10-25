<?php 
function index()
{
	global $calllist,$selectcalllist,$db,$request;
	
	$sql = "SELECT callId FROM `".TB_PREFIX."calllist` WHERE channelId=".$request['p'];
	$selectlist = $db->get_var($sql);
	if(!empty($selectlist))
	{
		$sql = "SELECT * FROM `".TB_PREFIX."menu` WHERE type='list' AND id not in(".$selectlist.")";
		$calllist = $db->get_results($sql);	
		$sql = "SELECT * FROM `".TB_PREFIX."menu` WHERE type='list' AND id in(".$selectlist.")";
		$selectcalllist = $db->get_results($sql);
	}
	else
	{
		$sql = "SELECT * FROM `".TB_PREFIX."menu` WHERE type='list'";
		$calllist = $db->get_results($sql);	
	}
}
function calllist()
{
	global $db,$request;		
	$calllist = new calllist();
	$calllist->callId = substr($request['calllist'],0,strlen($request['calllist'])-1);
	$calllist->channelId = $request['p'];
	
	if($db->get_var("SELECT count(*) FROM ".TB_PREFIX."calllist WHERE channelId=$request[p]")==0)
	{
		$calllist->addnew();
		$calllist->save();
	}
	else
	{
		$calllist->id = $db->get_var("SELECT id FROM ".TB_PREFIX."calllist WHERE channelId=$request[p]");
		$calllist->save();
	}
	redirect_to($request['p'],'index');
}
?>