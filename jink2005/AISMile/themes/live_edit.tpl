{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<script type="text/javascript">
	{if isset($smarty.get.ad) && isset($smarty.get.live_edit)}
		var ad = "{$smarty.get.ad}";
	{/if}
	var lastMove = '';
	var saveOK = '{l s='Module position saved'}';
	var confirmClose = '{l s='Are you sure? If you close this window, its position won\'t be saved'}';
	var close = '{l s='Close'}';
	var cancel = '{l s='Cancel'}';
	var confirm = '{l s='Confirm'}';
	var add = '{l s='Add this module'}';
	var unableToUnregisterHook = '{l s='Unable to unregister hook'}';
	var unableToSaveModulePosition = '{l s='Unable to save module position'}';
	var loadFail = '{l s='Failed to load module list'}';
</script>

<div style=" background-color:000; background-color: rgba(0,0,0, 0.7); border-bottom: 1px solid #000; width:100%;height:30px; padding:5px 10px; position:fixed;top:0;left:0;z-index:9999;">
<form id="liveEdit-action-form" action="./{$ad}/index.php" method="post">
	<input type="hidden" name="ajax" value="1" />
	<input type="hidden" name="id_shop" value="{$id_shop}" />
	<input type="hidden" name="token" value="{$smarty.get.liveToken|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="tab" value="AdminModulesPositions" />
	<input type="hidden" name="action" value="saveHook" />
	{foreach from=$hook_list key=hook_id item=hook_name}
		<input class="hook_list" type="hidden" name="hook_list[{$hook_id}]" 
			value="{$hook_name}" />
	{/foreach}
<div >
	<input type="submit" value="{l s='Save'}" name="saveHook" id="saveLiveEdit" class="exclusive" style="color:#fff;float:right; text-shadow: 0 -1px 0 #157402; margin-right:20px;">
	<input type="submit" value="{l s='Close Live edit'}" id="closeLiveEdit" class="button" style="background: #333 none; color:#fff; border:1px solid #000; float:right; margin-right:10px;">

</div>
</form>
	<div style="float:right;margin-right:20px;" id="live_edit_feed_back"></div>
</div>
<a href="#" style="display:none;" id="fancy"></a>
<div id="live_edit_feedback" style="width:400px"> 
	<p id="live_edit_feedback_str">
	</p> 
	<!-- <a href="javascript:;" onclick="$.fancybox.close();">{l s='Close'}</a> --> 
</div>	
