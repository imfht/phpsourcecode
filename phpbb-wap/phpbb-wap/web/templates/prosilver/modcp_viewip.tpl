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
		<h2>MCP</h2>
		<div class="panel bg3">
			<div class="inner"><span class="corners-top"><span></span></span>
			<div style="width:100%">
			<div id="cp-main" class="mcp-main">
				<h2>{L_IP_INFO}</h2>
				<div class="panel" id="ip">
					<div class="inner"><span class="corners-top"><span></span></span>
					<p>{L_THIS_POST_IP}: {IP} (<a href="{U_LOOKUP_IP}">{L_LOOKUP_IP}</a>)</p>
					<table class="table1" cellspacing="1">
					<thead>
					<tr>
						<th class="name">{L_OTHER_USERS}</th>
						<th class="posts">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					<!-- BEGIN userrow -->
					<tr class="row {userrow.ROW_CLASS}">
						<td><a href="{userrow.U_PROFILE}">{userrow.USERNAME}</a></td>
						<td class="posts"><a href="{userrow.U_SEARCHPOSTS}" title="{userrow.L_SEARCH_POSTS}">{userrow.POSTS}</a></td>
					</tr>
					<!-- END userrow -->
					</tbody>
					</table>
					<table class="table1" cellspacing="1">
					<thead>
					<tr>
						<th class="name">{L_OTHER_IPS}</th>
						<th class="posts">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					<!-- BEGIN iprow -->
					<tr class="row {iprow.ROW_CLASS}">
						<td>{iprow.IP} (<a href="{iprow.U_LOOKUP_IP}">{L_LOOKUP_IP}</a>)</td>
						<td class="posts">{iprow.POSTS}</td>
					</tr>
					<!-- END iprow -->
					</tbody>
					</table>
					<span class="corners-bottom"><span></span></span></div>
				</div>
			</div>
			<div class="clear"></div>
			</div>
			<span class="corners-bottom"><span></span></span></div>
		</div>
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