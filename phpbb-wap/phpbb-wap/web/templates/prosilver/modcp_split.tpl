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
		<h2>MCP</h2>
		<div class="panel bg3">
			<div class="inner"><span class="corners-top"><span></span></span>
			<div style="width:100%">
			<div id="cp-main" class="mcp-main">
				<h2>{L_SPLIT_TOPIC}</h2>
				<form method="post" name="mcp_list" action="{S_MODCP_ACTION}">
				<div class="panel">
					<div class="inner"><span class="corners-top"><span></span></span>
					<fieldset id="split-panel" class="fields2">
						<p>{L_SPLIT_TOPIC_EXPLAIN}</p>
						<dl>
							<dt><label>{L_SPLIT_SUBJECT}:</label></dt>
							<dd><input type="text" name="subject" size="45" maxlength="64" value="" title="{L_SPLIT_SUBJECT}" class="inputbox" /></dd>
						</dl>
						<dl>
							<dt><label>{L_SPLIT_FORUM}:</label></dt>
							<dd>{S_FORUM_SELECT}</dd>
						</dl>
					</fieldset>
					<span class="corners-bottom"><span></span></span></div>
				</div>
				<div class="panel">
					<div class="inner"><span class="corners-top"><span></span></span>
					<h3 id="review">
						<span class="right-box"><a href="#review" onclick="viewableArea(getElementById('topicreview'), true); var rev_text = getElementById('review').getElementsByTagName('a').item(0).firstChild; if (rev_text.data == '+ / -'){rev_text.data = '+ / -'; } else if (rev_text.data == '+ / -'){rev_text.data = '+ / -'};">+ / -</a></span>
							{L_MESSAGE}
					</h3>
					<div id="topicreview">
					<!-- BEGIN postrow -->
						<div class="post {postrow.ROW_CLASS}">
							<div class="inner"><span class="corners-top"><span></span></span>
							<div class="postbody">
								<div class="right-box">{postrow.S_SPLIT_CHECKBOX}</div>
								<h3>{postrow.POST_SUBJECT}</h3>
								<p class="author"><img src="templates/prosilver/images/icon_post_target.gif" alt="" /> <strong>{postrow.POSTER_NAME}</strong> &raquo; {postrow.POST_DATE}</p>
								<div class="content">{postrow.MESSAGE}</div>
							</div>
							<span class="corners-bottom"><span></span></span></div>
						</div>
					<!-- END postrow -->
					</div>
					<hr />
					<span class="corners-bottom"><span></span></span></div>
				</div>
				<fieldset class="display-actions">
					{S_HIDDEN_FIELDS}
					<input class="button2" type="submit" name="split_type_all" value="{L_SPLIT_POSTS}" />&nbsp; <input class="button2" type="submit" name="split_type_beyond" value="{L_SPLIT_AFTER}" />
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