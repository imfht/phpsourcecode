{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

	<div class="pageTitleHome">
		<span><h3>{l s='Dashboard'}</h3></span>
	</div>
<div id="dashboard">
<div id="homepage">


	<div id="column_left">
		{if $upgrade}
		<div id="blockNewVersionCheck">
		{if $upgrade->need_upgrade}
			<div class="warning warn" style="margin-bottom:10px;"><h3>{l s='New version of MileBiz is available'} : <a style="text-decoration: underline;" href="{$upgrade->link}" target="_blank">{l s='Download'} {$upgrade->version_name}</a> !</h3></div>
		{/if}
		</div>
	{else}
		<p>{l s='Update notification unavailable'}</p>
		<p>&nbsp;</p>
		<p>{l s='To receive MileBiz update warnings, you need to activate'} <b>allow_url_fopen</b> [<a href="http://www.php.net/manual/{$isoUser}/ref.filesystem.php">{l s='more info on php.net'}</a>]</p>
		<p>{l s='If you don\'t know how to do this, please contact your hosting provider!'}</p><br />
	{/if}

{if $employee->bo_show_screencast}
<div id="adminpresentation" style="display:block">
<h2>{l s='Video'}</h2>
		<div id="video">
			<a href="{$protocol}://screencasts.milebiz.com/v1.5/screencast.php?iso_lang={$isoUser}" id="screencast_fancybox"><img height="128" width="220" src="../img/admin/preview_fr.jpg" /><span class="mask-player"></span></a>
		</div>
			<div id="video-content">
			<p>{l s='Take part in the e-commerce adventure with MileBiz, the fast, powerful, and customizable e-commerce solution. With more than 275 features, MileBiz will help you create a world of opportunities without limits. Discover the solution that already powers more than 120,000 active stores worldwide!'}</p>
			</div>
	<div id="footer_iframe_home">
		<!--<a href="#">{l s='View more video tutorials'}</a>-->
		<input type="checkbox" id="screencast_dont_show_again">
		<label for="screencast_dont_show_again">{l s='Do not show again'}</label>
	</div>
				<div class="separation"></div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#screencast_dont_show_again').click(function() {
		if ($(this).is(':checked'))
		{
			$.ajax({
				type : 'POST',
				data : {
					ajax : '1',
					controller : 'AdminHome',
					token : '{$token}',
					id_employee : '{$employee->id}',
					action : 'hideScreencast'
				},
				url: 'ajax-tab.php',
				dataType : 'json',
				success: function(data) {
					if (!data)
						jAlert("TECHNICAL ERROR - no return status found");
					else if (data.status != "ok")
						jAlert("TECHNICAL ERROR: "+data.msg);

					$('#adminpresentation').slideUp('slow');
					
				},
				error: function(data, textStatus, errorThrown)
				{
					jAlert("TECHNICAL ERROR: "+data);
				}
			});
		}
	});
});
</script>
{/if}

<h2>{l s='Quick links'}</h2>
		<ul class="F_list clearfix">
		{foreach from=$quick_links key=k item=link}
		<li id="{$k}_block">
			<a href="{$link.href}">
				<h4>{$link.title}</h4>
				<p>{$link.description}</p>
			</a>
		</li>
		{/foreach}
		{hook h="displayAdminHomeQuickLinks"}
		</ul>

	<div id="partner_preactivation">
		<p class="center"><img src="../img/loader.gif" alt="" /></p>
	</div>

	<div class="separation"></div>


	{$tips_optimization}
	<div id="discover_milebiz"><p class="center"><img src="../img/loader.gif" alt="" />{l s='Loading...'}</p></div>

	{hook h="displayAdminHomeInfos"}
	{hook h="displayBackOfficeHome"} {*old name of the hook*}

</div>


	<div id="column_right">
	<h2>{l s='Your Information'}</h2>
		{$monthly_statistics}
		{$customers_service}
		{$stats_sales}
		{$last_orders}
		{hook h="displayAdminHomeStatistics"}
	</div>

</div>
	<div class="clear">&nbsp;</div>
	
	</div>

<script type="text/javascript">
$(document).ready(function() {
	if ({$refresh_check_version})
	{
		$('#blockNewVersionCheck').hide();
		$.ajax({
			type : 'POST',
			data : {
				ajax : '1',
				controller : 'AdminHome',
				token : '{$token}',
				id_employee : '{$employee->id}',
				action : 'refreshCheckVersion'
			},
			url: 'ajax-tab.php',
			dataType : 'json',
			success: function(data) {
				if (!data)
					jAlert("TECHNICAL ERROR - no return status found");
				else if (data.status != "ok")
					jAlert("TECHNICAL ERROR: "+data.msg);
				if(data.upgrade.need_upgrade)
				{
					$('#blockNewVersionCheck').children("a").attr('href',data.upgrade.link);
					$('#blockNewVersionCheck').children("a").html(data.upgrade.link+"pouet");
					$('#blockNewVersionCheck').fadeIn('slow');
				}

				
			},
			error: function(data, textStatus, errorThrown)
			{
				jAlert("TECHNICAL ERROR: "+data);
			}
		});
	}
	$.ajax({
		url: "ajax-tab.php",
		type: "POST",
		data:{
			token: "{$token}",
			ajax: "1",
			controller : "AdminHome",
			action: "getAdminHomeElement"
		},
		dataType: "json",
		success: function(json) {
		{if $employee->bo_show_screencast}
			if (json.screencast != 'NOK')
				$('#adminpresentation').fadeIn('slow');
			else
				$('#adminpresentation').fadeOut('slow');
		{/if}
			$('#partner_preactivation').fadeOut('slow', function() {
				if (json.partner_preactivation != 'NOK')
					$('#partner_preactivation').html(json.partner_preactivation);
				else
					$('#partner_preactivation').html('');
				$('#partner_preactivation').fadeIn('slow');
			});

			$('#discover_milebiz').fadeOut('slow', function() {
				if (json.discover_milebiz != 'NOK')
					$('#discover_milebiz').replaceWith(json.discover_milebiz);
				else
					$('#discover_milebiz').html('');
				$('#discover_milebiz').fadeIn('slow');
			});
		},
		error: function(XMLHttpRequest, textStatus, errorThrown)
		{
			// don't show/hide screencast if it's deactivated
			{if $employee->bo_show_screencast}
			$('#adminpresentation').fadeOut('slow');
			{/if}
			$('#partner_preactivation').fadeOut('slow');
			$('#discover_milebiz').fadeOut('slow');
		}
	});
	$('#screencast_fancybox').bind('click', function(event)
	{
		$.fancybox(
			this.href,
			{
				'width'				: 	660,
				'height'			: 	384,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type' 				: 'iframe',
				'scrolling'			: 'no',
				'onComplete'		: function()
					{
						// Rewrite some css properties of Fancybox
						$('#fancybox-wrap').css('width', '');
						$('#fancybox-content').css('background-color', '');
						$('#fancybox-content').css( 'border', '');
					}
			});

		event.preventDefault();
	});
});
</script>
