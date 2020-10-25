		<div class="navbar">
			<div class="inner"><span class="corners-top"><span></span></span>
			<ul class="linklist navlinks">
				<li class="icon-home"><a href="{U_INDEX}">{L_INDEX}</a>
				<!-- BEGIN switch_not_privmsg -->
				 <strong>&#8249;</strong> <a href="{U_VIEW_FORUM}">{FORUM_NAME}</a>
				<!-- END switch_not_privmsg -->
				</li>
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
		<!-- BEGIN switch_privmsg -->
		<h2>{L_PROFILE}</h2>
		<form id="postform" action="{S_POST_ACTION}" method="post" name="post" onsubmit="return checkForm(this)" {S_FORM_ENCTYPE}>
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
				{POST_PREVIEW_BOX}
				<h2>{L_POST_A}</h2>
				<div class="panel">
					<div class="inner"><span class="corners-top"><span></span></span>
					<fieldset class="fields1">
						{ERROR_BOX}
						<dl>
							<dt><label>{L_USERNAME}:</label><br /><span><a href="{U_FIND_USERNAME}" name="usersubmit" onclick="window.open('{U_SEARCH_USER}', '_phpbbsearch', 'HEIGHT=250,resizable=yes,WIDTH=400');return false;">{L_FIND_USERNAME}</a></span></dt>
							<dd><input type="text" name="username" size="25" value="{USERNAME}" class="inputbox autowidth" /></dd>
						</dl>
		<!-- END switch_privmsg -->
		<!-- BEGIN switch_not_privmsg -->
		<h2><a href="{U_VIEW_FORUM}">{FORUM_NAME}</a></h2>
		<form id="postform" action="{S_POST_ACTION}" method="post" name="post" onsubmit="return checkForm(this)" {S_FORM_ENCTYPE}>
		{POST_PREVIEW_BOX}
		<div class="panel">
			<div class="inner"><span class="corners-top"><span></span></span>
			<h3>{L_POST_A}</h3>
			<fieldset class="fields1">
				{ERROR_BOX}
		<!-- END switch_not_privmsg -->
				<!-- BEGIN switch_username_select -->
				<dl>
					<dt><label>{L_USERNAME}:</label></dt>
					<dd><input type="text" name="username" size="25" value="{USERNAME}" class="inputbox autowidth" /></dd>
				</dl>
				<!-- END switch_username_select -->
				<dl style="clear: left;">
					<dt><label>{L_SUBJECT}:</label></dt>
					<dd><input type="text" name="subject" size="45" maxlength="60" value="{SUBJECT}" class="inputbox autowidth" /></dd>
				</dl>
				<script type="text/javascript">
				// <![CDATA[
					var form_name = 'postform';
					var text_name = 'message';
				
					// Define the bbCode tags
					var bbcode = new Array();
					var bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[quote]','[/quote]','[code]','[/code]','[list]','[/list]','[list=]','[/list]','[img]','[/img]','[url]','[/url]','[size=]','[/size]');
					var imageTag = false;
				// ]]>
				</script>
				<script type="text/javascript" src="templates/prosilver/editor.js"></script>
				<div id="colour_palette" style="display: none;">
					<dl style="clear: left;">
						<dt><label>{L_FONT_COLOR}:</label></dt>
						<dd>
						<script type="text/javascript">
						// <![CDATA[
							function change_palette()
							{
								dE('colour_palette');
								e = document.getElementById('colour_palette');
								
								if (e.style.display == 'block')
								{
									document.getElementById('bbpalette').value = '{L_FONT_COLOR}';
								}
								else
								{
									document.getElementById('bbpalette').value = '{L_FONT_COLOR}';
								}
							}
				
							colorPalette('h', 15, 10);
						// ]]>
						</script>
						</dd>
					</dl>
				</div>
				<div id="format-buttons">
					<input type="button" class="button2" name="addbbcode0" value=" B " style="font-weight:bold; width: 30px" onclick="bbstyle(0)" title="{L_BBCODE_B_HELP}" />
					<input type="button" class="button2" name="addbbcode2" value=" i " style="font-style:italic; width: 30px" onclick="bbstyle(2)" title="{L_BBCODE_I_HELP}" />
					<input type="button" class="button2" name="addbbcode4" value=" u " style="text-decoration: underline; width: 30px" onclick="bbstyle(4)" title="{L_BBCODE_U_HELP}" />
					<input type="button" class="button2" name="addbbcode6" value="Quote" style="width: 50px" onclick="bbstyle(6)" title="{L_BBCODE_Q_HELP}" />
					<input type="button" class="button2" name="addbbcode8" value="Code" style="width: 40px" onclick="bbstyle(8)" title="{L_BBCODE_C_HELP}" />
					<input type="button" class="button2" name="addbbcode10" value="List" style="width: 40px" onclick="bbstyle(10)" title="{L_BBCODE_L_HELP}" />
					<input type="button" class="button2" name="addbbcode12" value="List=" style="width: 40px" onclick="bbstyle(12)" title="{L_BBCODE_O_HELP}" />
					<input type="button" class="button2" name="addbbcode14" value="Img" style="width: 40px" onclick="bbstyle(14)" title="{L_BBCODE_P_HELP}" />
					<input type="button" class="button2" name="addbbcode16" value="URL" style="text-decoration: underline; width: 40px" onclick="bbstyle(16)" title="{L_BBCODE_W_HELP}" />
					<select name="addbbcode20" onchange="bbfontstyle('[size=' + this.form.addbbcode20.options[this.form.addbbcode20.selectedIndex].value + ']', '[/size]');this.form.addbbcode20.selectedIndex = 2;" title="{L_BBCODE_F_HELP}">
						<option value="7">{L_FONT_TINY}</option>
						<option value="9">{L_FONT_SMALL}</option>
						<option value="12" selected="selected">{L_FONT_NORMAL}</option>
						<option value="18">{L_FONT_LARGE}</option>
						<option value="24">{L_FONT_HUGE}</option>
					</select>
					<input type="button" class="button2" name="bbpalette" value="{L_FONT_COLOR}" onclick="change_palette();" title="{L_BBCODE_S_HELP}" />
				</div>
				<div id="smiley-box">
					<strong>{L_EMOTICONS}</strong><br />
					<!-- BEGIN smilies_row -->
					<!-- BEGIN smilies_col -->
					<a href="#" onclick="insert_text('{smilies_row.smilies_col.SMILEY_CODE}', true); return false;"><img src="{smilies_row.smilies_col.SMILEY_IMG}" alt="{smilies_row.smilies_col.SMILEY_DESC}" title="{smilies_row.smilies_col.SMILEY_DESC}" /></a>
					<!-- END smilies_col -->
					<!-- END smilies_row -->
					<!-- BEGIN switch_smilies_extra -->
					<br /><a href="{U_MORE_SMILIES}" onclick="popup(this.href, 300, 350, '_phpbbsmilies'); return false;">{L_MORE_SMILIES}</a>
					<!-- END switch_smilies_extra -->
					<hr />
					{BBCODE_STATUS}<br />
					{SMILIES_STATUS}
					<!-- BEGIN switch_confirm -->
					<br/><br/><b>Код подтверждения:</b><br/>
					{CONFIRM_IMG}<br/>
					<input type="text" name="confirm_code" value="" />
					<!-- END switch_confirm -->
				</div>
				<div id="message-box">
					<textarea name="message" rows="15" cols="76" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" class="inputbox">{MESSAGE}{DRAFT_MESSAGE}{SIGNATURE}</textarea>
				</div>
			</fieldset>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<div class="panel bg2">
			<div class="inner"><span class="corners-top"><span></span></span>
			<fieldset class="submit-buttons">
				{S_HIDDEN_FORM_FIELDS}
				<input type="submit" name="preview" value="{L_PREVIEW}" class="button1" />&nbsp; 
				<input type="submit" name="post" value="{L_SUBMIT}" class="button1" />
			</fieldset>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<div class="panel bg3" id="options-panel">
			<div class="inner"><span class="corners-top"><span></span></span>
			<fieldset class="fields1">
			<!-- BEGIN switch_html_checkbox -->
				<div style="display:none"><label><input type="checkbox" name="disable_html" checked="checked" /> {L_DISABLE_HTML}</label></div>
			<!-- END switch_html_checkbox -->
			<!-- BEGIN switch_bbcode_checkbox -->
				<div><label><input type="checkbox" name="disable_bbcode" {S_BBCODE_CHECKED} /> {L_DISABLE_BBCODE}</label></div>
			<!-- END switch_bbcode_checkbox -->
			<!-- BEGIN switch_smilies_checkbox -->
				<div><label><input type="checkbox" name="disable_smilies" {S_SMILIES_CHECKED} /> {L_DISABLE_SMILIES}</label></div>
			<!-- END switch_smilies_checkbox -->
			<!-- BEGIN switch_signature_checkbox -->
				<div><label><input type="checkbox" name="attach_sig" {S_SIGNATURE_CHECKED} /> {L_ATTACH_SIGNATURE}</label></div>
			<!-- END switch_signature_checkbox -->
			<!-- BEGIN switch_notify_checkbox -->
				<div><label><input type="checkbox" name="notify" {S_NOTIFY_CHECKED} /> {L_NOTIFY_ON_REPLY}</label></div>
			<!-- END switch_notify_checkbox -->
			<!-- BEGIN switch_delete_checkbox -->
				<div><label><input type="checkbox" name="delete" /> {L_DELETE_POST}</label></div>
			<!-- END switch_delete_checkbox -->
			<!-- BEGIN switch_type_toggle -->
				<hr class="dashed" />
				{S_TYPE_TOGGLE}
			<!-- END switch_type_toggle -->
			</fieldset>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		{ATTACHBOX}
		{POLLBOX}
		{TOPIC_REVIEW_BOX}
		<!-- BEGIN switch_privmsg -->
			</div>
			<div class="clear"></div>
			</div>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<!-- END switch_privmsg -->
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