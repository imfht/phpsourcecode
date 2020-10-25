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
		<h2>{L_PROFILE}</h2>
		<div class="panel bg3">
			<div class="inner"><span class="corners-top"><span></span></span>
			<div style="width:100%">
			<div id="cp-menu">
				<div id="navigation">
					<ul>
						<li>{INBOX}</li>
						<li>{SENTBOX}</li>
						<li>{OUTBOX}</li>
						<li>{SAVEBOX}</li>
					</ul>
				</div>
			</div>
			<div id="cp-main" class="ucp-main">
				<h2>{BOX_NAME} : {L_MESSAGE}</h2>
				<form method="post" action="{S_PRIVMSGS_ACTION}">
				<div class="post pm">
					<div class="inner"><span class="corners-top"><span></span></span>
					<div class="buttons">{REPLY_PM_IMG}</div>
					<div class="postbody">
						<ul class="profile-icons">{QUOTE_PM_IMG} {EDIT_PM_IMG}</ul>
						<h3 class="first">{POST_SUBJECT}</h3>
						<p class="author">
							<strong>{L_POSTED}:</strong> {POST_DATE}
							<br /><strong>{L_FROM}:</strong> {MESSAGE_FROM}
							<br /><strong>{L_TO}:</strong> {MESSAGE_TO}
						</p>
						<div class="content">{MESSAGE}
						<!-- BEGIN postrow -->
						{ATTACHMENTS}
						<!-- END postrow -->
						</div>
					</div>
					<dl class="postprofile">
						<dt><strong>{MESSAGE_FROM}</strong></dt>
						<dd><ul class="profile-icons">
							{PM_IMG}
							{PROFILE_IMG}
							{EMAIL_IMG}
							{WWW_IMG}
							{MSN_IMG}
							{ICQ_IMG}
							{YIM_IMG}
							{AIM_IMG}
						</ul></dd>
					</dl>
					<span class="corners-bottom"><span></span></span></div>
				</div>
				<fieldset class="display-options">
					{S_HIDDEN_FIELDS}<input type="submit" name="save" value="{L_SAVE_MSG}" class="button2" />&nbsp; <input type="submit" name="delete" value="{L_DELETE_MSG}" class="button2" />
					<!-- BEGIN switch_attachments -->
					&nbsp; 
					<input type="submit" name="pm_delete_attach" value="{L_DELETE_ATTACHMENTS}" class="button2" />
					<!-- END switch_attachments -->
				</fieldset>
				</form>
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