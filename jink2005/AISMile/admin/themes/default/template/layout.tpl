{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{include file='header.tpl'}
{if isset($conf)}
	<div class="conf">
		{$conf}
	</div>
{/if}
{if count($errors) && (!isset($disableDefaultErrorOutPut) || $disableDefaultErrorOutPut == false)}
	<div class="error">
		<span style="float:right">
			<a id="hideError" href="#"><img alt="X" src="../img/admin/close.png" /></a>
		</span>
		
		{if count($errors) == 1}
			{$errors[0]}
		{else}
			{l s='%d errors' sprintf=$errors|count}
			<br/>
			<ol>
				{foreach $errors as $error}
					<li>{$error}</li>
				{/foreach}
			</ol>
		{/if}
	</div>
{/if}

{if isset($informations) && count($informations) && $informations}
	<div class="hint clear" style="display:block;">
		{foreach $informations as $info}
			{$info}<br />
		{/foreach}
	</div><br />
{/if}

{if isset($confirmations) && count($confirmations) && $confirmations}
	<div class="conf" style="display:block;">
		{foreach $confirmations as $conf}
			{$conf}<br />
		{/foreach}
	</div><br />
{/if}

{if count($warnings)}
	<div class="warn">
		<span style="float:right">
			<a id="hideWarn" href=""><img alt="X" src="../img/admin/close.png" /></a>
		</span>
		{if count($warnings) > 1}
			{l s='There are %d warnings.' sprintf=count($warnings)}
			<span style="margin-left:20px;" id="labelSeeMore">
				<a id="linkSeeMore" href="#" style="text-decoration:underline">{l s='Click here to see more'}</a>
				<a id="linkHide" href="#" style="text-decoration:underline;display:none">{l s='Hide warning'}</a>
			</span>
			<ul {if count($warnings) > 1}style="display:none;"{/if} id="seeMore">
			{foreach $warnings as $warning}
				<li>{$warning}</li>
			{/foreach}
			</ul>
		{else}
			<ul style="margin-top: 3px">
			{foreach $warnings as $warning}
				<li>{$warning}</li>
			{/foreach}
			</ul>
		{/if}
	</div>
{/if}

{$page}
{include file='footer.tpl'}
