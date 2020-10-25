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
		<h2>{L_SEARCH_MATCHES}</h2>
		<ul class="linklist">
			<li class="rightside pagination">
				{L_SEARCH_MATCHES} &bull; {PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span>
			</li>
		</ul>
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
			<!-- BEGIN searchresults -->
				<li class="row bg1">
					<dl class="icon" style="background-image: url({searchresults.TOPIC_FOLDER_IMG}); background-repeat: no-repeat;">
						<dt>
							{searchresults.NEWEST_POST_IMG}{searchresults.TOPIC_TYPE}
							<a href="{searchresults.U_VIEW_TOPIC}" class="topictitle">{searchresults.TOPIC_TITLE}</a> {searchresults.GOTO_PAGE}
							<br />{searchresults.TOPIC_AUTHOR} &raquo; {searchresults.FIRST_POST_TIME} 
							(<a href="{searchresults.U_VIEW_TOPIC}">{searchresults.TOPIC_TITLE}</a>)
						</dt>
						<dd class="posts">{searchresults.REPLIES}</dd>
						<dd class="views">{searchresults.VIEWS}</dd>
						<dd class="lastpost"><span>
							{searchresults.LAST_POST_AUTHOR}
							{searchresults.LAST_POST_IMG}<br />{searchresults.LAST_POST_TIME}<br /> </span>
						</dd>
					</dl>
				</li>
			<!-- END searchresults -->
			</ul>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<ul class="linklist">
			<li class="rightside pagination">
				{L_SEARCH_MATCHES} &bull; {PAGE_NUMBER}&nbsp; <span>{PAGINATION}</span>
			</li>
		</ul>
		{JUMPBOX}
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