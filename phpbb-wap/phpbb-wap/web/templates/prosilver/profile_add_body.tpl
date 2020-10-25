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
			<div id="cp-main" class="ucp-main">
				<form action="{S_PROFILE_ACTION}" {S_FORM_ENCTYPE} method="post">
				<h2>{L_REGISTRATION_INFO}</h2>
				<div class="panel">
					<div class="inner"><span class="corners-top"><span></span></span>
					<fieldset>
						{ERROR_BOX}
						<!-- BEGIN switch_namechange_disallowed -->
						<dl>
							<dt><label>{L_USERNAME}:</label></dt>
							<dd><strong>{USERNAME}</strong></dd>
						</dl>
						<!-- END switch_namechange_disallowed -->
						<!-- BEGIN switch_namechange_allowed -->
						<dl>
							<dt><label>{L_USERNAME}:</label></dt>
							<dd><input type="text" name="username" value="{USERNAME}" class="inputbox" title="{L_USERNAME}" /></dd>
						</dl>
						<!-- END switch_namechange_allowed -->
						<dl>
							<dt><label>{L_EMAIL_ADDRESS}:</label></dt>
							<dd><input type="text" name="email" maxlength="100" value="{EMAIL}" class="inputbox" title="{L_EMAIL_ADDRESS}" /></dd>
						</dl>
						<!-- BEGIN switch_edit_profile -->
						<dl>
							<dt><label>{L_CURRENT_PASSWORD}:</label><br /><span>{L_CONFIRM_PASSWORD_EXPLAIN}</span></dt>
							<dd><input type="password" name="cur_password" maxlength="255" value="{CUR_PASSWORD}" class="inputbox" title="{L_CURRENT_PASSWORD}" /></dd>
						</dl>
						<!-- END switch_edit_profile -->
						<dl>
							<dt><label>{L_NEW_PASSWORD}:</label><br /><span>{L_PASSWORD_IF_CHANGED}</span></dt>
							<dd><input type="password" name="new_password" maxlength="255" value="{NEW_PASSWORD}" class="inputbox" title="{L_NEW_PASSWORD}" /></dd>
						</dl>
						<dl>
							<dt><label>{L_CONFIRM_PASSWORD}:</label><br /><span>{L_PASSWORD_CONFIRM_IF_CHANGED}</span></dt>
							<dd><input type="password" name="password_confirm" maxlength="255" value="{PASSWORD_CONFIRM}" class="inputbox" title="{L_CONFIRM_PASSWORD}" /></dd>
						</dl>
						<!-- BEGIN switch_user_logged_out -->
						<hr />
						<dl>
							<dt><label>{L_BOARD_LANGUAGE}:</label></dt>
							<dd>{LANGUAGE_SELECT}</dd>
						</dl>
						<dl>
							<dt><label>{L_TIMEZONE}:</label></dt>
							<dd>{TIMEZONE_SELECT}</dd>
						</dl>
						<!-- END switch_user_logged_out -->
						<!-- BEGIN switch_confirm -->
						<hr />
						<dl>
							<dt><label>{L_CONFIRM_CODE}:</label></dt>
							<dd>{CONFIRM_IMG}</dd>
							<dd><input type="text" name="confirm_code" size="8" maxlength="8" class="inputbox narrow" /></dd>
							<dd>{L_CONFIRM_CODE_EXPLAIN}</dd>
						</dl>
						<!-- END switch_confirm -->
					</fieldset>
					<span class="corners-bottom"><span></span></span></div>
				</div>
				<!-- BEGIN switch_user_logged_in -->
				<h2>{L_PROFILE_INFO}</h2>
				<div class="panel">
					<div class="inner"><span class="corners-top"><span></span></span>
					<fieldset>
						<dl>
							<dt><label>{L_ICQ_NUMBER}:</label></dt>
							<dd><input type="text" name="icq" maxlength="15" value="{ICQ}" class="inputbox" /></dd>
						</dl>
						<dl>
							<dt><label>{L_AIM}:</label></dt>
							<dd><input type="text" name="aim" maxlength="255" value="{AIM}" class="inputbox" /></dd>
						</dl>
						<dl>
							<dt><label>{L_MESSENGER}:</label></dt>
							<dd><input type="text" name="msn" maxlength="255" value="{MSN}" class="inputbox" /></dd>
						</dl>
						<dl>
							<dt><label>{L_YAHOO}:</label></dt>
							<dd><input type="text" name="yim" maxlength="255" value="{YIM}" class="inputbox" /></dd>
						</dl>
						<dl>
							<dt><label>{L_WEBSITE}:</label></dt>
							<dd><input type="text" name="website" maxlength="255" value="{WEBSITE}" class="inputbox" /></dd>
						</dl>
						<dl>
							<dt><label>{L_LOCATION}:</label></dt>
							<dd><input type="text" name="location" maxlength="255" value="{LOCATION}" class="inputbox" /></dd>
						</dl>
						<dl>
							<dt><label>{L_OCCUPATION}:</label></dt>
							<dd><textarea name="occupation" class="inputbox" rows="3" cols="30">{OCCUPATION}</textarea></dd>
						</dl>
						<dl>
							<dt><label>{L_INTERESTS}:</label></dt>
							<dd><textarea name="interests" class="inputbox" rows="3" cols="30">{INTERESTS}</textarea></dd>
						</dl>
					</fieldset>
					<span class="corners-bottom"><span></span></span></div>
				</div>
				<h2>{L_SIGNATURE}</h2>
				<div class="panel">
					<div class="inner"><span class="corners-top"><span></span></span>
					<p>{L_SIGNATURE_EXPLAIN}</p>
					<fieldset>
						<dl>
							<dt>{BBCODE_STATUS}<br />{SMILIES_STATUS}</dt>
							<dd><textarea name="signature" rows="6" cols="76" class="inputbox">{SIGNATURE}</textarea></dd>
						</dl>
					</fieldset>
					<span class="corners-bottom"><span></span></span></div>
				</div>
				<h2>{L_PREFERENCES}</h2>
				<div class="panel">
					<div class="inner"><span class="corners-top"><span></span></span>
					<fieldset>
						<dl>
							<dt><label>{L_PUBLIC_VIEW_EMAIL}:</label></dt>
							<dd><label><input type="radio" name="viewemail" value="1" {VIEW_EMAIL_YES} /> {L_YES}</label> <label><input type="radio" name="viewemail" value="0" {VIEW_EMAIL_NO} /> {L_NO}</label></dd>
						</dl>
						<dl>
							<dt><label>{L_HIDE_USER}:</label></dt>
							<dd><label><input type="radio" name="hideonline" value="1" {HIDE_USER_YES} /> {L_YES}</label> <label><input type="radio" name="hideonline" value="0" {HIDE_USER_NO} /> {L_NO}</label></dd>
						</dl>
						<dl>
							<dt><label>{L_NOTIFY_ON_REPLY}:</label></dt>
							<dd><label><input type="checkbox" name="notifyreply_to_pm"{NOTIFY_REPLY_TO_PM} /> {L_NOTIFY_ON_REPLY_TO_PM}</label><br /><label><input type="checkbox" name="notifyreply_to_email"{NOTIFY_REPLY_TO_EMAIL} /> {L_NOTIFY_ON_REPLY_TO_EMAIL}</label></dd>
						</dl>
						<dl>
							<dt><label>{L_NOTIFY_ON_PRIVMSG}:</label></dt>
							<dd><label><input type="radio" name="notifypm" value="1" {NOTIFY_PM_YES} /> {L_YES}</label> <label><input type="radio" name="notifypm" value="0" {NOTIFY_PM_NO} /> {L_NO}</label></dd>
						</dl>
						<dl>
							<dt><label>{L_POPUP_ON_PRIVMSG}:</label></dt>
							<dd><label><input type="radio" name="popup_pm" value="1" {POPUP_PM_YES} /> {L_YES}</label> <label><input type="radio" name="popup_pm" value="0" {POPUP_PM_NO} /> {L_NO}</label></dd>
						</dl>
						<dl>
							<dt><label>{L_ALWAYS_ADD_SIGNATURE}:</label></dt>
							<dd><label><input type="radio" name="attachsig" value="1" {ALWAYS_ADD_SIGNATURE_YES} /> {L_YES}</label> <label><input type="radio" name="attachsig" value="0" {ALWAYS_ADD_SIGNATURE_NO} /> {L_NO}</label></dd>
						</dl>
						<dl>
							<dt><label>{L_ALWAYS_ALLOW_BBCODE}:</label></dt>
							<dd><label><input type="radio" name="allowbbcode" value="1" {ALWAYS_ALLOW_BBCODE_YES} /> {L_YES}</label> <label><input type="radio" name="allowbbcode" value="0" {ALWAYS_ALLOW_BBCODE_NO} /> {L_NO}</label></dd>
						</dl>
						<dl style="display:none">
							<dt><label>{L_ALWAYS_ALLOW_HTML}:</label></dt>
							<dd><label><input type="radio" name="allowhtml" value="1" /> {L_YES}</label> <label><input type="radio" name="allowhtml" value="0" checked="checked" /> {L_NO}</label></dd>
						</dl>
						<dl>
							<dt><label>{L_ALWAYS_ALLOW_SMILIES}:</label></dt>
							<dd><label><input type="radio" name="allowsmilies" value="1" {ALWAYS_ALLOW_SMILIES_YES} /> {L_YES}</label> <label><input type="radio" name="allowsmilies" value="0" {ALWAYS_ALLOW_SMILIES_NO} /> {L_NO}</label></dd>
						</dl>
						<dl>
							<dt><label>{L_BOARD_LANGUAGE}:</label></dt>
							<dd>{LANGUAGE_SELECT}</dd>
						</dl>
						<dl>
							<dt><label>{L_BOARD_STYLE}:</label></dt>
							<dd>{STYLE_SELECT}</dd>
						</dl>
						<dl>
							<dt><label>{L_TIMEZONE}:</label></dt>
							<dd>{TIMEZONE_SELECT}</dd>
						</dl>
						<dl>
							<dt><label>{L_DATE_FORMAT}:</label><br /><span>{L_DATE_FORMAT_EXPLAIN}</span></dt>
							<dd><input type="text" name="dateformat" value="{DATE_FORMAT}" maxlength="30" class="inputbox narrow" /></dd>
						</dl>
					</fieldset>
					<span class="corners-bottom"><span></span></span></div>
				</div>
				<!-- END switch_user_logged_in -->
				<!-- BEGIN switch_avatar_block -->
				<h2>{L_AVATAR_PANEL}</h2>
				<div class="panel">
					<div class="inner"><span class="corners-top"><span></span></span>
					<fieldset>
						<dl>
							<dt><label>{L_CURRENT_IMAGE}:</label><br /><span>{L_AVATAR_EXPLAIN}</span></dt>
							<dd>{AVATAR}</dd>
							<dd><label><input type="checkbox" name="avatardel" /> {L_DELETE_AVATAR}</label></dd>
						</dl>
						<!-- BEGIN switch_avatar_local_upload -->
						<dl>
							<dt><label>{L_UPLOAD_AVATAR_FILE}:</label></dt>
							<dd><input type="hidden" name="MAX_FILE_SIZE" value="{AVATAR_SIZE}" /><input type="file" name="avatar" class="inputbox autowidth" /></dd>
						</dl>
						<!-- END switch_avatar_local_upload -->
						<!-- BEGIN switch_avatar_remote_upload -->
						<dl>
							<dt><label>{L_UPLOAD_AVATAR_URL}:</label><br /><span>{L_UPLOAD_AVATAR_URL_EXPLAIN}</span></dt>
							<dd><input type="text" name="avatarurl" value="" class="inputbox" /></dd>
						</dl>
						<!-- END switch_avatar_remote_upload -->
						<!-- BEGIN switch_avatar_remote_link -->
						<dl>
							<dt><label>{L_LINK_REMOTE_AVATAR}:</label><br /><span>{L_LINK_REMOTE_AVATAR_EXPLAIN}</span></dt>
							<dd><input type="text" name="avatarremoteurl" value="" class="inputbox" /></dd>
						</dl>
						<!-- END switch_avatar_remote_link -->
						<!-- BEGIN switch_avatar_local_gallery -->
						<dl>
							<dt><label>{L_AVATAR_GALLERY}:</label></dt>
							<dd><input type="submit" name="avatargallery" value="{L_SHOW_GALLERY}" class="button2" /></dd>
						</dl>
						<!-- END switch_avatar_local_gallery -->
					</fieldset>
					<span class="corners-bottom"><span></span></span></div>
				</div>
				<!-- END switch_avatar_block -->
				<fieldset class="submit-buttons">
					{S_HIDDEN_FIELDS}<input type="reset" value="{L_RESET}" name="reset" class="button2" />&nbsp; 
					<input type="submit" name="submit" value="{L_SUBMIT}" class="button1" />
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