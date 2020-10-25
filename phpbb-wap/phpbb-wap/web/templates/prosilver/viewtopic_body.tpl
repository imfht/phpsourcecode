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
		<h2><a href="{U_VIEW_TOPIC}">{TOPIC_TITLE}</a></h2><br />
		<div class="topic-actions">
			<div class="buttons"><a href="{U_POST_REPLY_TOPIC}" title="{L_POST_REPLY_TOPIC}"><img src="{REPLY_IMG}" alt="{L_POST_REPLY_TOPIC}" /></a></div>
			<div class="pagination">{PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span></div>
		</div>
		<div class="clear"></div>
		{POLL_DISPLAY}
		<!-- BEGIN postrow -->
		<div id="{postrow.U_POST_ID}" class="post {postrow.ROW_CLASS}">
			<div class="inner"><span class="corners-top"><span></span></span>
			<div class="postbody">
				<ul class="profile-icons">
					{postrow.QUOTE_IMG}
					{postrow.EDIT_IMG}
					{postrow.DELETE_IMG}
					{postrow.IP_IMG}
				</ul>
				<h3 class="first"><a href="{postrow.U_MINI_POST}">{postrow.POST_SUBJECT}</a></h3>
				<p class="author"><a href="{postrow.U_MINI_POST}"><img src="{postrow.MINI_POST_IMG}" alt="" /></a> <strong>{postrow.POSTER_NAME}</strong> &raquo; {postrow.POST_DATE}</p>
				<div class="content">{postrow.MESSAGE}{postrow.ATTACHMENTS}</div>
				{postrow.EDITED_MESSAGE}{postrow.SIGNATURE}
			</div>
			<dl class="postprofile">
				<dt>{postrow.POSTER_AVATAR}</a><br /><strong>{postrow.POSTER_NAME}</strong></dt>
				<dd>{postrow.POSTER_RANK}<br />{postrow.RANK_IMAGE}</dd>
				<dd>&nbsp;</dd>
				<dd>{postrow.POSTER_POSTS}</dd>
				<dd>{postrow.POSTER_JOINED}</dd>
				<dd>{postrow.POSTER_FROM}</dd>
				<dd><ul class="profile-icons">
					{postrow.PM_IMG}
					{postrow.PROFILE_IMG}
					{postrow.EMAIL_IMG}
					{postrow.WWW_IMG}
					{postrow.MSN_IMG}
					{postrow.ICQ_IMG}
					{postrow.YIM_IMG}
					{postrow.AIM_IMG}
				</ul></dd>
			</dl>
			<div class="back2top"><a href="#wrap" class="top" title="{L_BACK_TO_TOP}">{L_BACK_TO_TOP}</a></div>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<hr class="divider" />
		<!-- END postrow -->
		<form method="post" action="{S_POST_DAYS_ACTION}">
		<fieldset class="display-options" style="margin-top: 0; ">
			<label>{L_DISPLAY_POSTS}: {S_SELECT_POST_DAYS}&nbsp;{S_SELECT_POST_ORDER}&nbsp;<input type="submit" name="sort" value="{L_GO}" class="button2" /></label>
		</fieldset>
		</form>
		<hr />
		<div class="topic-actions">
			<div class="buttons"><a href="{U_POST_REPLY_TOPIC}" title="{L_POST_REPLY_TOPIC}"><img src="{REPLY_IMG}" alt="{L_POST_REPLY_TOPIC}" /></a></div>
			<div class="pagination">{PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span></div>
		</div>
		<p></p><p><a href="{U_VIEW_FORUM}" class="left-box left">{FORUM_NAME}</a></p>
		{JUMPBOX}
		{S_TOPIC_ADMIN}
		<p>{S_AUTH_LIST}</p>
	</div>
	<div id="page-footer">
		<div class="navbar">
			<div class="inner"><span class="corners-top"><span></span></span>
			<ul class="linklist">
				<li class="icon-home"><a href="{U_INDEX}">{L_INDEX}</a>
				<!-- BEGIN switch_user_logged_in -->
				<li class="icon-bookmark">{S_WATCH_TOPIC}</li>
				<!-- END switch_user_logged_in -->
				<li class="rightside">
				<!-- BEGIN switch_user_logged_in -->
				<a href="{U_GROUP_CP}">{L_USERGROUPS}</a> &bull; 
				<!-- END switch_user_logged_in -->
				{S_TIMEZONE}</li>
			</ul>
			<span class="corners-bottom"><span></span></span></div>
		</div>