		<div class="navbar">
			<div class="inner"><span class="corners-top"><span></span></span>
			<ul class="linklist navlinks">
				<li class="icon-home"><a href="{U_INDEX}">{L_INDEX}</a> <strong>&#8249;</strong> <a href="{U_VIEW_FORUM}">{FORUM_NAME}</a></li>
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
		<script type="text/javascript"> <!--
			function select_switch(status)
			{
				for (i = 0; i < document.mcp_list.length; i++)
				{
					document.mcp_list.elements[i].checked = status;
				}
			}
		//--></script>
		<h2>{L_MOD_CP}</h2>
		<div class="panel bg3">
			<div class="inner"><span class="corners-top"><span></span></span>
			<div style="width:100%">
			<div id="cp-main" class="mcp-main">
				{JUMPBOX}
				<form method="post" name="mcp_list" action="{S_MODCP_ACTION}">
				<div class="panel">
					<div class="inner"><span class="corners-top"><span></span></span>
					<ul class="linklist">
						<li class="rightside pagination">{PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span></li>
					</ul>
					<ul class="topiclist">
						<li class="header">
							<dl class="icon">
								<dt>{L_TOPICS}</dt>
								<dd class="posts">{L_REPLIES}</dd>
								<dd class="lastpost"><span>{L_LASTPOST}</span></dd>
								<dd class="mark">{L_SELECT}</dd>
							</dl>
						</li>
					</ul>
					<ul class="topiclist cplist">
					<!-- BEGIN topicrow -->
						<li class="row bg1">
							<dl class="icon" style="background-image: url({topicrow.TOPIC_FOLDER_IMG}); background-repeat: no-repeat;">
								<dt>{topicrow.TOPIC_ATTACHMENT_IMG}{topicrow.TOPIC_TYPE}<a href="{topicrow.U_VIEW_TOPIC}" class="topictitle">{topicrow.TOPIC_TITLE}</a></dt>
								<dd class="posts">{topicrow.REPLIES}</dd>
								<dd class="lastpost"><span>{topicrow.LAST_POST_TIME}</span></dd>
								<dd class="mark"><input type="checkbox" name="topic_id_list[]" value="{topicrow.TOPIC_ID}" /></dd>
							</dl>
						</li>
					<!-- END topicrow -->
					</ul>
					<ul class="linklist">
						<li class="rightside pagination">{PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span></li>
					</ul>
					<span class="corners-bottom"><span></span></span></div>
				</div>
				<fieldset class="display-actions">
				{S_HIDDEN_FIELDS}
					<input type="submit" name="delete" class="button2" value="{L_DELETE}" />&nbsp; <input type="submit" name="move" class="button2" value="{L_MOVE}" />&nbsp; <input type="submit" name="lock" class="button2" value="{L_LOCK}" />&nbsp; <input type="submit" name="unlock" class="button2" value="{L_UNLOCK}" />
					<div><a href="javascript:select_switch(true);">Mark all</a> &bull; <a href="javascript:select_switch(false);">Unmark all</a></div>
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