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
		<h2 class="titlespace">{L_SEND_EMAIL_MSG}</h2>
		<form action="{S_POST_ACTION}" method="post">
		<div class="panel">
			<div class="inner"><span class="corners-top"><span></span></span>
			<div class="content">
				<fieldset class="fields2">
					{ERROR_BOX}
					<dl>
						<dt><label>{L_RECIPIENT}:</label></dt>
						<dd><strong>{USERNAME}</strong></dd>
					</dl>
					<dl>
						<dt><label>{L_SUBJECT}:</label></dt>
						<dd><input class="inputbox autowidth" type="text" name="subject" size="50" value="{SUBJECT}" /></dd>
					</dl>
					<dl>
						<dt><label>{L_MESSAGE_BODY}:</label><br /><span>{L_MESSAGE_BODY_DESC}</span></dt>
						<dd><textarea class="inputbox" name="message" rows="15" cols="76">{MESSAGE}</textarea></dd>
					</dl>
					<dl>
						<dt>&nbsp;</dt>
						<dd><label><input type="checkbox" name="cc_email" value="1" checked="checked" tabindex="5" /> {L_CC_EMAIL}</label></dd>
					</dl>
				</fieldset>
			</div>
		<span class="corners-bottom"><span></span></span></div>
		</div>
		<div class="panel">
			<div class="inner"><span class="corners-top"><span></span></span>
			<div class="content">
				<fieldset class="submit-buttons">
					<input type="submit" tabindex="6" name="submit" class="button1" value="{L_SEND_EMAIL}" />
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