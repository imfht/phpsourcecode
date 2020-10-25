{**
 * MILEBIZ Ã×ÀÖÉÌ³Ç
 * ============================================================================
 * °æÈ¨ËùÓĞ 2011-20__ Ã×ÀÖÍø¡£
 * ÍøÕ¾µØÖ·: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{capture assign='page_title'}{l s='æˆ‘ä»¬çš„å•†åº—'}{/capture}
{include file='./page-title.tpl'}

<script type="text/javascript">
		// <![CDATA[
		var map;
		var markers = [];
		var infoWindow;
		var locationSelect;

		var defaultLat = '{$defaultLat}';
		var defaultLong = '{$defaultLong}';
		
		var translation_1 = '{l s='No stores found, try selecting a wider radius' js=1}';
		var translation_2 = '{l s='store found - see details:' js=1}';
		var translation_3 = '{l s='stores found - see all results:' js=1}';
		var translation_4 = '{l s='Phone:' js=1}';
		var translation_5 = '{l s='Get Directions' js=1}';
		var translation_6 = '{l s='Not found' js=1}';
		
		var hasStoreIcon = '{$hasStoreIcon}';
		var distance_unit = '{$distance_unit}';
		var img_store_dir = '{$img_store_dir}';
		var img_ps_dir = '{$img_ps_dir}';
		var searchUrl = '{$searchUrl}';
		//]]>
</script>

<!-- Stores -->
<div data-role="content" id="content" class="stores">

	<div id="stores_search_block">
		<label for="location">
			{l s='Enter a location (e.g. zip/postal code, address, city or country) in order to find the nearest stores.'}
		</label>
	    <input type="text" name="location" id="location" value="" />
	</div>
	
	<div id="stores_search_block">
		<label for="radius">{l s='Radius:'} ({$distance_unit})</label>
		<input type="range" name="radius_slider" id="radius" value="15" min="0" max="100" data-highlight="true"/>
	</div>
	
	<div id="stores_search_block">
		<button type="submit" data-theme="a" name="submit" value="submit-value" class="ui-btn-hidden" aria-disabled="false">
			{l s='Search'}
		</button>
	</div>
	
	<div class="stores_block">
		<h3 class="bg">{l s='æˆ‘ä»¬çš„å•†åº—'}</h3>
		<ul data-role="listview" data-theme="c" id="stores_list">
		</ul>
	</div>
	{include file="./sitemap.tpl"}
</div> 
<!-- END Stores -->