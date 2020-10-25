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
		<h2><a href="{U_VIEW_FORUM}">{FORUM_NAME}</a></h2>
		<p>{L_MODERATOR}: {MODERATORS}</p>
		<div class="topic-actions">
			<div class="buttons"><a href="{U_POST_NEW_TOPIC}" title="{L_POST_NEW_TOPIC}"><img src="{POST_IMG}" alt="{L_POST_NEW_TOPIC}" /></a></div>
			<div class="pagination">
				<!-- BEGIN switch_user_logged_in -->
				<a href="{U_MARK_READ}">{L_MARK_TOPICS_READ}</a> &bull; 
				<!-- END switch_user_logged_in -->
				<!-- BEGIN switch_no_topics -->
				0!
				<!-- END switch_no_topics -->
				{PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span>
			</div>
		</div>
		<div class="forumbg">
			<div class="inner"><span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt>{L_TOPICS}</dt>
						<dd class="posts">{L_REPLIES}</dd>
						<dd class="views">{L_VIEWS}</dd>
						<dd class="lastpost"><span>{L_LASTPOST}</span></dd>
					</dl>
				</li>
			</ul>
			<ul class="topiclist topics">
				<!-- BEGIN topicrow -->
				<li class="row {topicrow.ROW_CLASS}">
					<dl class="icon" style="background-image: url({topicrow.TOPIC_FOLDER_IMG}); background-repeat: no-repeat;">
						<dt title="{topicrow.L_TOPIC_FOLDER_ALT}">{topicrow.NEWEST_POST_IMG}{topicrow.TOPIC_ATTACHMENT_IMG}{topicrow.TOPIC_TYPE}<a href="{topicrow.U_VIEW_TOPIC}" class="topictitle">{topicrow.TOPIC_TITLE}</a>{topicrow.GOTO_PAGE}<br />{topicrow.TOPIC_AUTHOR} &raquo; {topicrow.FIRST_POST_TIME}</dt>
						<dd class="posts">{topicrow.REPLIES}</dd>
						<dd class="views">{topicrow.VIEWS}</dd>
						<dd class="lastpost"><span>{topicrow.LAST_POST_AUTHOR} {topicrow.LAST_POST_IMG}<br />{topicrow.LAST_POST_TIME}</span></dd>
					</dl>
				</li>
				<!-- END topicrow -->
			</ul>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<!-- BEGIN switch_no_topics -->
		<div class="panel">
			<div class="inner"><span class="corners-top"><span></span></span>
			<strong>{L_NO_TOPICS}</strong>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<!-- END switch_no_topics -->
		<form method="post" action="{S_TOPIC_ACTION}">
		<fieldset class="display-options">
			<label>{L_DISPLAY_TOPICS}: {S_SELECT_TOPIC_DAYS} <input type="submit" name="sort" value="{L_GO}" class="button2" /></label>
		</fieldset>
		</form>
		<hr />
		<div class="topic-actions">
			<div class="buttons"><a href="{U_POST_NEW_TOPIC}" title="{L_POST_NEW_TOPIC}"><img src="{POST_IMG}" alt="{L_POST_NEW_TOPIC}" /></a></div>
			<div class="pagination">
				<!-- BEGIN switch_user_logged_in -->
				<a href="{U_MARK_READ}">{L_MARK_TOPICS_READ}</a> &bull; 
				<!-- END switch_user_logged_in -->
				<!-- BEGIN switch_no_topics -->
				0!
				<!-- END switch_no_topics -->
				{PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span>
			</div>
		</div>
		<p></p><p><a href="{U_INDEX}" class="left-box left" accesskey="r">{L_INDEX}</a></p>
		{JUMPBOX}
		<!-- BEGIN switch_user_logged_in -->
		<h3><a href="{U_VIEWONLINE}">{L_WHO_IS_ONLINE}</a></h3>
		<p>{LOGGED_IN_USER_LIST}</p>
		<!-- END switch_user_logged_in -->
		<p>{S_AUTH_LIST}</p>
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