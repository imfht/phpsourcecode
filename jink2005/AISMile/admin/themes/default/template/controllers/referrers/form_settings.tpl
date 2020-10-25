{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

</div>

<div>
	<fieldset>
		<legend>
			<img src="../img/admin/tab-preferences.gif" /> {l s='Settings'}
		</legend>
		<form action="{$current}&token={$token}" method="post" id="settings_form" name="settings_form">
			<label>{l s='Save direct traffic'}</label>
			<div class="margin-left">
				<label class="t" for="tracking_dt_on"><img src="../img/admin/enabled.gif" alt="{l s='Yes'}" title="{l s='Yes'}" /></label>
				<input type="radio" name="tracking_dt" id="tracking_dt_on" value="1" {if $tracking_dt}checked="checked"{/if} />
				<label class="t" for="tracking_dt_on"> {l s='Yes'}</label>
				<label class="t" for="tracking_dt_off"><img src="../img/admin/disabled.gif" alt="{l s='No'}" title="{l s='No'}" style="margin-left: 10px;" /></label>
				<input type="radio" name="tracking_dt" id="tracking_dt_off" value="0" {if !$tracking_dt}checked="checked"{/if}/>
				<label class="t" for="tracking_dt_off"> {l s='No'}</label>
			</div>
			<p>{l s='Direct traffic can be quite resource-intensive. You should consider enabling it only if you have a strong database server and a strong need for it.'}</p>
			<input type="submit" class="button" value="{l s='   Save   '}" name="submitSettings" id="submitSettings" />
		</form>
		<div class="separation"></div>
		<form action="{$current}&token={$token}" method="post" id="refresh_index_form" name="refresh_index_form">
			<h3>{l s='Indexation'}</h3>
			<p>{l s='There is a huge quantity of data, so each connection corresponding to a referrer is indexed. You can refresh this index by clicking on the button above. Be aware that it may take a long time and it is only needed if you modified or added a referrer and if you want your changes to be retroactive.'}</p>
			<input type="submit" class="button" value="{l s='Refresh index'}" name="submitRefreshIndex" id="submitRefreshIndex" />
		</form>
				<div class="separation"></div>
		<form action="{$current}&token={$token}" method="post" id="refresh_cache_form" name="refresh_cache_form">
			<h3>{l s='Cache'}</h3>
			<p>{l s='For you to sort and filter your data, it is cached. You can refresh the cache by clicking on the button above.'}</p>
			<input type="submit" class="button" value="{l s='Refresh cache'}" name="submitRefreshCache" id="submitRefreshCache" />
		</form>
	</fieldset>
</div>

<div class="clear">&nbsp;</div>

	