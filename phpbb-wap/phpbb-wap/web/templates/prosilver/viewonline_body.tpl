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
		<h2>{TOTAL_REGISTERED_USERS_ONLINE}</h2>
		<p>{TOTAL_GUEST_USERS_ONLINE}</p>
		<div class="forumbg">
			<div class="inner"><span class="corners-top"><span></span></span>
			<table class="table1" cellspacing="1">
				<thead>
					<tr>
						<th class="name">{L_USERNAME}</th>
						<th class="info">{L_FORUM_LOCATION}</th>
						<th class="active">{L_LAST_UPDATE}</th>
					</tr>
				</thead>
				<tbody>
					<!-- BEGIN reg_user_row -->
					<tr class="{reg_user_row.ROW_CLASS}">
						<td><a href="{reg_user_row.U_USER_PROFILE}" class="title">{reg_user_row.USERNAME}</a></td>
						<td class="info"><a href="{reg_user_row.U_FORUM_LOCATION}">{reg_user_row.FORUM_LOCATION}</a></td>
						<td class="info">{reg_user_row.LASTUPDATE}</td>
					</tr>
					<!-- END reg_user_row -->
					<!-- BEGIN guest_user_row -->
					<tr class="{guest_user_row.ROW_CLASS}">
						<td>{guest_user_row.USERNAME}</td>
						<td class="info"><a href="{guest_user_row.U_FORUM_LOCATION}">{guest_user_row.FORUM_LOCATION}</a></td>
						<td class="info">{guest_user_row.LASTUPDATE}</td>
					</tr>
					<!-- END guest_user_row -->
				</tbody>
			</table>
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