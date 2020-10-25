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
		<form action="{S_LOGIN_ACTION}" method="post">
		<div class="panel">
			<div class="inner"><span class="corners-top"><span></span></span>
			<div class="content">
				<h2>{L_ENTER_PASSWORD}</h2>
				<fieldset class="fields1">
					<dl>
						<dt><label>{L_USERNAME}:</label></dt>
						<dd><input type="text" name="username" size="25" value="{USERNAME}" class="inputbox autowidth" /></dd>
					</dl>
					<dl>
						<dt><label>{L_PASSWORD}:</label></dt>
						<dd><input type="password" name="password" size="25" class="inputbox autowidth" /></dd>
						<dd><a href="{U_SEND_PASSWORD}">{L_SEND_PASSWORD}</a></dd>
					</dl>
					<!-- BEGIN switch_allow_autologin -->
					<dl>
						<dd><label><input type="checkbox" name="autologin" /> {L_AUTO_LOGIN}</label></dd>
					</dl>
					<!-- END switch_allow_autologin -->
					<dl>
						<dt>&nbsp;</dt>
						<dd>{S_HIDDEN_FIELDS}<input type="submit" name="login" value="{L_LOGIN}" class="button1" /></dd>
					</dl>
				</fieldset>
			</div>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		</form>
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