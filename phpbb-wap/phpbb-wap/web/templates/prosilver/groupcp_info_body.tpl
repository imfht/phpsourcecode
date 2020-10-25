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
		<h2>{GROUP_NAME}</h2>
		<form action="{S_GROUPCP_ACTION}" method="post">
		<div class="panel">
			<div class="inner"><span class="corners-top"><span></span></span>
			<h3>{L_GROUP_INFORMATION}</h3>
			<fieldset>
				<dl>
					<dt>{L_GROUP_NAME}:</dt>
					<dd>{GROUP_NAME}</dd>
				</dl>
				<dl>
					<dt>{L_GROUP_DESC}:</dt>
					<dd>{GROUP_DESC}</dd>
				</dl>
				<dl>
					<dt>{L_GROUP_MEMBERSHIP}:</dt>
					<dd>{GROUP_DETAILS}&nbsp; 
					<!-- BEGIN switch_subscribe_group_input -->
					<input class="button2" type="submit" name="joingroup" value="{L_JOIN_GROUP}" />
					<!-- END switch_subscribe_group_input -->
					<!-- BEGIN switch_unsubscribe_group_input -->
					<input class="button2" type="submit" name="unsub" value="{L_UNSUBSCRIBE_GROUP}" />
					<!-- END switch_unsubscribe_group_input -->
					</dd>
				</dl>
				<!-- BEGIN switch_mod_option -->
				<dl>
					<dt>{L_GROUP_TYPE}:</dt>
					<dd><input type="radio" name="group_type" value="{S_GROUP_OPEN_TYPE}" {S_GROUP_OPEN_CHECKED} /> {L_GROUP_OPEN}&nbsp; <input type="radio" name="group_type" value="{S_GROUP_CLOSED_TYPE}" {S_GROUP_CLOSED_CHECKED} /> {L_GROUP_CLOSED}&nbsp; <input type="radio" name="group_type" value="{S_GROUP_HIDDEN_TYPE}" {S_GROUP_HIDDEN_CHECKED} /> {L_GROUP_HIDDEN}&nbsp; <input class="button2" type="submit" name="groupstatus" value="{L_UPDATE}" /></dd>
				</dl>
				<!-- END switch_mod_option -->
				</fieldset>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		{S_HIDDEN_FIELDS}
		</form>
		<form action="{S_GROUPCP_ACTION}" method="post" name="post">
		<div class="forumbg forumbg-table">
			<div class="inner"><span class="corners-top"><span></span></span>
			<table class="table1" cellspacing="1">
			<thead>
			<tr>
				<th class="name">{L_GROUP_MODERATOR}</th>
				<th class="posts">{L_POSTS}</th>
				<th class="active">{L_WEBSITE}</th>
				<th class="info">{L_FROM}</th>
				<th class="joined">{L_SELECT}</th>
			</tr>
			</thead>
			<tbody>
			<tr class="bg1">
				<td><a href="{U_MOD_VIEWPROFILE}">{MOD_USERNAME}</a></td>
				<td class="posts">{MOD_POSTS}</td>
				<td>{MOD_WWW}</td>
				<td class="info">{MOD_FROM}</td>
				<td></td>
			</tr>
			</tbody>
			</table>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<div class="forumbg forumbg-table">
			<div class="inner"><span class="corners-top"><span></span></span>
			<table class="table1" cellspacing="1">
			<thead>
			<tr>
				<th class="name">{L_GROUP_MEMBERS}</th>
				<th class="posts">{L_POSTS}</th>
				<th class="active">{L_WEBSITE}</th>
				<th class="info">{L_FROM}</th>
				<th class="joined">{L_SELECT}</th>
			</tr>
			</thead>
			<tbody>
			<!-- BEGIN member_row -->
			<tr class="{member_row.ROW_CLASS}">
				<td><a href="{member_row.U_VIEWPROFILE}">{member_row.USERNAME}</a></td>
				<td class="posts">{member_row.POSTS}</td>
				<td>{member_row.WWW}</td>
				<td class="info">{member_row.FROM}</td>
				<td><input type="checkbox" name="members[]" value="{member_row.USER_ID}" /></td>
			</tr>
			<!-- END member_row -->
			<!-- BEGIN switch_no_members -->
			<tr>
				<td class="bg1" colspan="5">{L_NO_MEMBERS}</td>
			</tr>
			<!-- END switch_no_members -->
			<!-- BEGIN switch_hidden_group -->
			<tr>
				<td class="bg1" colspan="5">{L_HIDDEN_MEMBERS}</td>
			</tr>
			<!-- END switch_hidden_group -->
			</tbody>
			</table>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<!-- BEGIN switch_mod_option -->
		<fieldset class="display-options">
			<label><input type="text" class="inputbox autowidth" name="username" size="35" maxlength="50" /> <input type="submit" name="add" value="{L_ADD_MEMBER}" class="button2" /> <input type="submit" name="usersubmit" value="{L_FIND_USERNAME}" class="button2" onclick="window.open('{U_SEARCH_USER}', '_phpbbsearch', 'height=250,resizable=yes,width=400');return false;" /> <input type="submit" name="remove" value="{L_REMOVE_SELECTED}" class="button2" /></label>
		</fieldset>
		<!-- END switch_mod_option -->
		<ul class="linklist">
			<li class="rightside pagination">{PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span></li>
		</ul>
		{PENDING_USER_BOX}
		{S_HIDDEN_FIELDS}
		</form>
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