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
	<td align="right"> 
	<!-- BEGIN switch_box_size_notice -->
	<table width="175" cellspacing="1" cellpadding="2" border="0" class="bodyline">
	<tr> 
		<td colspan="3" width="175" class="row1" nowrap="nowrap"><span class="gensmall">{ATTACH_BOX_SIZE_STATUS}</span></td>
	</tr>
	<tr> 
		<td colspan="3" width="175" class="row2">
			<table cellspacing="0" cellpadding="1" border="0">
			<tr> 
				<td bgcolor="{T_TD_COLOR2}"><img src="templates/subSilver/images/spacer.gif" width="{ATTACHBOX_LIMIT_IMG_WIDTH}" height="8" alt="{ATTACH_LIMIT_PERCENT}" /></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr> 
		<td width="33%" class="row1"><span class="gensmall">0%</span></td>
		<td width="34%" align="center" class="row1"><span class="gensmall">50%</span></td>
		<td width="33%" align="right" class="row1"><span class="gensmall">100%</span></td>
	</tr>
	</table>
	<!-- END switch_box_size_notice -->
	</td>
	<div id="page-body">
		<script type="text/javascript"> <!--
			function select_switch(status)
			{
				for (i = 0; i < document.privmsg_list.length; i++)
				{
					document.privmsg_list.elements[i].checked = status;
				}
			}
		//--></script>
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
				<h2>{BOX_SIZE_STATUS}</h2>
				<form method="post" name="privmsg_list" action="{S_PRIVMSGS_ACTION}">
				<div class="panel">
					<div class="inner"><span class="corners-top"><span></span></span>
					<div class="buttons">{POST_PM_IMG}</div>
					<ul class="linklist">
						<li class="rightside pagination">{PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span></li>
					</ul>
					<ul class="topiclist">
						<li class="header">
							<dl>
								<dt>{L_MESSAGE}</dt>
								<dd class="mark">{L_MARK}</dd>
							</dl>
						</li>
					</ul>
					<ul class="topiclist cplist pmlist">
					<!-- BEGIN listrow -->
						<li class="row {listrow.ROW_CLASS}">
							<dl class="icon">
								<dt style="background-image: url({listrow.PRIVMSG_FOLDER_IMG}); background-repeat: no-repeat;"><a href="{listrow.U_READ}" class="topictitle">{listrow.PRIVMSG_ATTACHMENTS_IMG}{listrow.SUBJECT}</a><br /><a href="{listrow.U_FROM_USER_PROFILE}" class="name">{listrow.FROM}</a> &raquo; {listrow.DATE}</dt>
								<dd class="mark"><input type="checkbox" name="mark[]2" value="{listrow.S_MARK_ID}" /></dd>
							</dl>
						</li>
					<!-- END listrow -->
					</ul>
					<!-- BEGIN switch_no_messages -->
					<p><strong>{L_NO_MESSAGES}</strong></p>
					<!-- END switch_no_messages -->
					<fieldset class="display-actions">
						{S_HIDDEN_FIELDS}<input type="submit" name="save" value="{L_SAVE_MARKED}" class="button2" />&nbsp; <input type="submit" name="delete" value="{L_DELETE_MARKED}" class="button2" />&nbsp; <input type="submit" name="deleteall" value="{L_DELETE_ALL}" class="button2" />
						<div><a href="javascript:select_switch(true);">{L_MARK_ALL}</a> &bull; <a href="javascript:select_switch(false);">{L_UNMARK_ALL}</a></div>
					</fieldset>
					<hr />
					<ul class="linklist">
						<li class="rightside pagination">{PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span></li>
					</ul>
					<span class="corners-bottom"><span></span></span></div>
				</div>
				<fieldset class="display-options">
					<label>{L_DISPLAY_MESSAGES}: <select name="msgdays">{S_SELECT_MSG_DAYS}</select> <input type="submit" value="{L_GO}" name="submit_msgdays" class="button2" /></label>
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