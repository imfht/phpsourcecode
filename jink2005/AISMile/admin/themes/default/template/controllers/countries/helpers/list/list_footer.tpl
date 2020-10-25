{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

			</table>
			{if $bulk_actions}
				<p>
					{if $bulk_actions|count > 1}
						<select id="select_submitBulk" name="select_submitBulk">
							{foreach $bulk_actions as $key => $params}
								<option value="{$key}">{$params.text}</option>
							{/foreach}
						</select>
						<input type="submit" class="button" name="submitBulk" id="submitBulk" value="{l s='Apply'}" />
					{else}
						{foreach $bulk_actions as $key => $params}
							{if $key == 'affectzone'}
								<select id="zone_to_affect" name="zone_to_affect">
									{foreach $zones as $z}
										<option value="{$z['id_zone']}">{$z['name']}</option>
									{/foreach}
								</select>
							{/if}
							<input type="submit" class="button" name="submitBulk{$key}{$table}" value="{$params.text}" {if isset($params.confirm)}onclick="return confirm('{$params.confirm}');"{/if} />
						{/foreach}
					{/if}
				</p>
			{/if}
		</td>
	</tr>
</table>
<input type="hidden" name="token" value="{$token}" />
</form>

<script type="text/javascript">
	var confirmation = new Array();
	{foreach $bulk_actions as $key => $params}
		{if isset($params.confirm)}
			confirmation['{$key}{$table}'] = "{$params.confirm}";
		{/if}
	{/foreach}

	$(document).ready(function(){
		{if $bulk_actions|count > 1}
			$('#submitBulk').click(function(){
				if (confirmation[$(this).val()])
					return confirm(confirmation[$(this).val()]);
				else
					return true;
			});
			$('#select_submitBulk').change(function(){
				if ($(this).val() == 'affectzone')
					loadZones();
				else if (loaded)
					$('#zone_to_affect').fadeOut('slow');
			});
		{/if}
	});
	var loaded = false;
	function loadZones()
	{
		if (!loaded)
		{
			$.ajax({
				type: 'POST',
				url: 'ajax.php',
				data: 'getZones=true&token={$token}',
				async : true,
				cache: false,
				dataType: 'json',
				success: function(data) {
					var html = $(data);
					html.hide();
					$('#select_submitBulk').after(html);
					html.fadeIn('slow');
				}
			});
			loaded = true;
		}
		else
		{
			$('#zone_to_affect').fadeIn('slow');
		}
	}
</script>