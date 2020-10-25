		<div class="navbar">
			<div class="inner"><span class="corners-top"><span></span></span>
			<ul class="linklist navlinks">
				<li class="icon-home"><a href="{U_INDEX}">{L_INDEX}</a></li>
			</ul>
			<!-- BEGIN switch_user_logged_in -->
			<ul class="linklist leftside">
				<li class="icon-ucp">
					<a href="{U_PROFILE}" title="{L_PROFILE}">{L_PROFILE}</a>
					 (<a href="{U_PRIVATEMSGS}">{PRIVATE_MESSAGE_INFO}</a>) &bull; 
					<a href="{U_SEARCH_SELF}">{L_SEARCH_SELF}</a>
				</li>
			</ul>
			<!-- END switch_user_logged_in -->
			<ul class="linklist rightside">
				<li class="icon-faq"><a href="{U_FAQ}">{L_FAQ}</a></li>
				<li class="icon-search"><a href="{U_SEARCH}">{L_SEARCH}</a></li>
				<!-- BEGIN switch_user_logged_in -->
				<li class="icon-members"><a href="{U_MEMBERLIST}">{L_MEMBERLIST}</a></li>
				<!-- END switch_user_logged_in -->
				<!-- BEGIN switch_user_logged_out -->
				<li class="icon-register"><a href="{U_REGISTER}">{L_REGISTER}</a></li>
				<!-- END switch_user_logged_out -->
				<li class="icon-logout"><a href="{U_LOGIN_LOGOUT}" title="{L_LOGIN_LOGOUT}">{L_LOGIN_LOGOUT}</a></li>
			</ul>
			<span class="corners-bottom"><span></span></span></div>
		</div>
	</div>
	<div id="page-body">
		<h2>{L_VIEWING_PROFILE}</h2>
		<div class="panel bg1">
			<div class="inner"><span class="corners-top"><span></span></span>
			<dl class="left-box">
				<dt>{AVATAR_IMG}</dt>
				<dd style="text-align: center;">{POSTER_RANK}</dd>
			</dl>
			<dl class="left-box details" style="width: 80%;">
				<dt>{L_USERNAME}: </dt><dd>{USERNAME}</dd>
				<dt>{L_LOCATION}: </dt><dd>{LOCATION}</dd>
				<dt>{L_OCCUPATION}: </dt><dd>{OCCUPATION}</dd>
				<dt>{L_INTERESTS}: </dt><dd>{INTERESTS}</dd>
<!-- BEGIN switch_upload_limits -->
		<tr> 
			<td valign="top" align="right" nowrap="nowrap"><span class="gen">{L_UPLOAD_QUOTA}:</span></td>
			<td> 
				<table width="175" cellspacing="1" cellpadding="2" border="0" class="bodyline">
				<tr> 
					<td colspan="3" width="100%" class="row2">
						<table cellspacing="0" cellpadding="1" border="0">
						<tr> 
							<td bgcolor="{T_TD_COLOR2}"><img src="templates/subSilver/images/spacer.gif" width="{UPLOAD_LIMIT_IMG_WIDTH}" height="8" alt="{UPLOAD_LIMIT_PERCENT}" /></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr> 
					<td width="33%" class="row1"><span class="gensmall">0%</span></td>
					<td width="34%" align="center" class="row1"><span class="gensmall">50%</span></td>
					<td width="33%" align="right" class="row1"><span class="gensmall">100%</span></td>
				</tr>
				</table>
				<b><span class="genmed">[{UPLOADED} / {QUOTA} / {PERCENT_FULL}]</span> </b><br />
				<span class="genmed"><a href="{U_UACP}" class="genmed">{L_UACP}</a></span></td>
			</td>
		</tr>
<!-- END switch_upload_limits -->
			</dl>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<div class="panel bg2">
			<div class="inner"><span class="corners-top"><span></span></span>
			<div class="column1">
				<h3>{L_CONTACT} {USERNAME}</h3>
				<dl class="details">
					<dt>{L_EMAIL_ADDRESS}: </dt><dd>{EMAIL}</dd>
					<dt>{L_WEBSITE}: </dt><dd>{WWW}</dd>
					<dt>{L_PM}: </dt><dd>{PM}</dd>
					<dt>{L_MESSENGER}: </dt><dd>{MSN}</dd>
					<dt>{L_YAHOO}: </dt><dd>{YIM}</dd>
					<dt>{L_AIM}: </dt><dd>{AIM}</dd>
					<dt>{L_ICQ_NUMBER}: </dt><dd>{ICQ}</dd>
				</dl>
			</div>
			<div class="column2">
			<h3>{USERNAME}</h3>
			<dl class="details">
				<dt>{L_JOINED}:</dt> <dd>{JOINED}</dd>
				<dt>{L_TOTAL_POSTS}:</dt>
				<dd>{POSTS} | <strong><a href="{U_SEARCH_USER}">{L_SEARCH_USER_POSTS}</a></strong><br />({POST_PERCENT_STATS} / {POST_DAY_STATS})</dd>
			</dl>
			</div>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		{JUMPBOX}
	</div>
	<div id="page-footer">
		<div class="navbar">
			<div class="inner"><span class="corners-top"><span></span></span>
			<ul class="linklist">
				<li class="icon-home"><a href="{U_INDEX}">{L_INDEX}</a></li>
				<li class="rightside">
				<!-- BEGIN switch_user_logged_in -->
				<a href="{U_GROUP_CP}">{L_USERGROUPS}</a> &bull; 
				<!-- END switch_user_logged_in -->
				{S_TIMEZONE}</li>
			</ul>
			<span class="corners-bottom"><span></span></span></div>
		</div>