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
		<h2 class="solo">{L_MEMBERLIST}</h2>
		<ul class="linklist">
			<li class="rightside pagination">
				{PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span>
			</li>
		</ul>
		<div class="forumbg">
			<div class="inner"><span class="corners-top"><span></span></span>
			<table class="table1" cellspacing="1">
			<thead>
			<tr>
				<th class="name">{L_USERNAME}</th>
				<th class="posts">{L_POSTS}</th>
				<th class="active">{L_WEBSITE}</th>
				<th class="info">{L_FROM}</th>
				<th class="joined">{L_JOINED}</th>
			</tr>
			</thead>
			<tbody>
			<!-- BEGIN memberrow -->
			<tr class="{memberrow.ROW_CLASS}">
				<td><a href="{memberrow.U_VIEWPROFILE}">{memberrow.USERNAME}</a></td>
				<td class="posts">{memberrow.POSTS}</td>
				<td>{memberrow.WWW}</td>
				<td class="info">{memberrow.FROM}</td>
				<td>{memberrow.JOINED}</td>
			</tr>
			<!-- END memberrow -->
			</tbody>
			</table>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<form method="post" action="{S_MODE_ACTION}">
		<fieldset class="display-options">
			<label>{L_SELECT_SORT_METHOD}: {S_MODE_SELECT}&nbsp; {L_ORDER}: {S_ORDER_SELECT}&nbsp; <input type="submit" name="submit" value="{L_SUBMIT}" class="button2" /></label>
		</fieldset>
		</form>
		<hr />
		<ul class="linklist">
			<li class="rightside pagination">
				{PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span>
			</li>
		</ul>
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