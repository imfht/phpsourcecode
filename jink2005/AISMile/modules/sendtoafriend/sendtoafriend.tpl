{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{capture name=path}{l s='Send to a friend' mod='sendtoafriend'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<div id="sendfriendpage">
	<h1>{l s='Send to a friend' mod='sendtoafriend'}</h1>

	<p class="bold"><strong>{l s='Send this page to a friend who might be interested in the item below.' mod='sendtoafriend'}.</strong></p>
	{include file="$tpl_dir/errors.tpl"}

	{if isset($smarty.get.submited)}
		<p class="success">{l s='Your email has been sent successfully' mod='sendtoafriend'}</p>
	{else}
			<form method="post" action="{$request_uri}" class="std">
				<fieldset>
					<div class="product clearfix">
						<a href="{$productLink}" class="img_link"><img src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'small_default')}" alt="" title="{$cover.legend}" /></a>
						<a href="{$productLink}" class="product_desc"><br />{$product->name}</a>
					</div>

					<div id="send_friend_form_content">
						<p class="text">
							<label for="friend-name">{l s='Friend\'s name:' mod='sendtoafriend'}</label>
							<input type="text" id="friend-name" name="name" value="{if isset($smarty.post.name)}{$smarty.post.name|escape:'htmlall':'UTF-8'|stripslashes}{/if}" />
						</p>
						<p class="text">
							<label for="friend-address">{l s='Friend\'s email:' mod='sendtoafriend'}</label>
							<input type="text" id="friend-address" name="email" value="{if isset($smarty.post.name)}{$smarty.post.email|escape:'htmlall'|stripslashes}{/if}" />
						</p>

						<p class="submit">
							<input type="submit" name="submitAddtoafriend" value="{l s='send' mod='sendtoafriend'}" class="button" />
						</p>
					</div>
				</fieldset>
			</form>
	{/if}

	<ul class="footer_links">
		<li><a href="{$productLink}">&laquo; {l s='Back to product page' mod='sendtoafriend'}</a></li>
	</ul>
</div>
