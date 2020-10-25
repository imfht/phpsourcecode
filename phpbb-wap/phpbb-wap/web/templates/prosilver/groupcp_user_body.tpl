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
		<h2>{L_USERGROUPS}</h2>
		<!-- BEGIN switch_groups_joined -->
		<div class="panel">
			<div class="inner"><span class="corners-top"><span></span></span>	
			<h3>{L_GROUP_MEMBERSHIP_DETAILS}</h3>
			<fieldset>
			<!-- BEGIN switch_groups_member -->
			<form method="get" action="{S_USERGROUP_ACTION}">
				<dl>
					<dt>{L_YOU_BELONG_GROUPS}:</dt>
					<dd>{GROUP_MEMBER_SELECT} <input type="submit" value="{L_VIEW_INFORMATION}" class="button2" />{S_HIDDEN_FIELDS}</dd>
				</dl>
			</form>
			<!-- END switch_groups_member -->
			<!-- BEGIN switch_groups_pending -->
			<form method="get" action="{S_USERGROUP_ACTION}">
				<dl>
					<dt>{L_PENDING_GROUPS}:</dt>
					<dd>{GROUP_PENDING_SELECT} <input type="submit" value="{L_VIEW_INFORMATION}" class="button2" />{S_HIDDEN_FIELDS}</dd>
				</dl>
			</form>
			<!-- END switch_groups_pending -->
			</fieldset>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<!-- END switch_groups_joined -->
		<!-- BEGIN switch_groups_remaining -->
		<form method="get" action="{S_USERGROUP_ACTION}">
		<div class="panel">
			<div class="inner"><span class="corners-top"><span></span></span>	
			<h3>{L_JOIN_A_GROUP}</h3>
			<fieldset>
				<dl>
					<dt>{L_SELECT_A_GROUP}:</dt>
					<dd>{GROUP_LIST_SELECT} <input type="submit" value="{L_VIEW_INFORMATION}" class="button2" />{S_HIDDEN_FIELDS}</dd>
				</dl>
			</fieldset>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		</form>
		<!-- END switch_groups_remaining -->
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