{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<table>
	<tr>
		<td style="text-align: left; font-size: 6pt; color: #444">
			{$shop_address|escape:'htmlall':'UTF-8'}<br />

			{if !empty($shop_phone) OR !empty($shop_fax)}
				{l s='For more assistance, contact Support:' pdf='true'}<br />
				{if !empty($shop_phone)}
					Tel: {$shop_phone|escape:'htmlall':'UTF-8'}
				{/if}

				{if !empty($shop_fax)}
					Fax: {$shop_fax|escape:'htmlall':'UTF-8'}
				{/if}
				<br />
			{/if}
            
            {if isset($shop_details)}
                {$shop_details|escape:'htmlall':'UTF-8'}<br />
            {/if}

            {if isset($free_text)}
            	{foreach $free_text as $text}
    				{$text|escape:'htmlall':'UTF-8'}<br />
    			{/foreach}
            {/if}
		</td>
	</tr>
</table>

