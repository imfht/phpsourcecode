<{include file="public/header.tpl"}>
 <frameset rows="50,*" frameborder=0 id="scrool">
	<frame src="<{$smarty.const.__CONTROLLER__}>/top" name="top" />

	<frameset cols="180,*">
		<frame src="<{$smarty.const.__CONTROLLER__}>/left" name="left"/>
			
		<frame src="<{$smarty.const.__MODULE__}>/Video/Index" name="main"/>
		
	</frameset>
	
</frameset> 
<{include file="public/footer.tpl"}>

