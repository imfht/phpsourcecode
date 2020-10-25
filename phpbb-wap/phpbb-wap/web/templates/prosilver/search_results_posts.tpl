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
		<!-- BEGIN searchresults -->
		<div class="search post bg1">
			<div class="inner"><span class="corners-top"><span></span></span>
			<div class="postbody">
				<h3><a href="{searchresults.U_TOPIC}">{searchresults.TOPIC_TITLE}</a></h3>
				<div class="content">{searchresults.MESSAGE}</div>
			</div>
			<dl class="postprofile">
				<dt class="author">{searchresults.POSTER_NAME}</dt>
				<dd>{searchresults.POST_DATE}</dd>
				<dd>&nbsp;</dd>
				<dd>{L_FORUM}: <a href="{searchresults.U_FORUM}">{searchresults.FORUM_NAME}</a></dd>
				<dd>{L_TOPIC}: <a href="{searchresults.U_TOPIC}">{searchresults.TOPIC_TITLE}</a></dd>
				<dd>{L_REPLIES}: <strong>{searchresults.TOPIC_REPLIES}</strong></dd>
				<dd>{L_VIEWS}: <strong>{searchresults.TOPIC_VIEWS}</strong></dd>
			</dl>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<!-- END searchresults -->
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