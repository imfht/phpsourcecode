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
		<h2>{L_FAQ_TITLE}</h2>
		<div class="panel bg1" id="faqlinks">
			<div class="inner"><span class="corners-top"><span></span></span>
			<!-- BEGIN faq_block_link -->
			<dl class="faq">
				<dt><strong>{faq_block_link.BLOCK_TITLE}</strong></dt>
				<!-- BEGIN faq_row_link -->
					<dd><a href="{faq_block_link.faq_row_link.U_FAQ_LINK}">{faq_block_link.faq_row_link.FAQ_LINK}</a></dd>
				<!-- END faq_row_link -->
				</dl>
			<!-- END faq_block_link -->
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<div class="clear"></div>
		<!-- BEGIN faq_block -->
		<div class="panel {faq_block.faq_row.ROW_CLASS}">
			<div class="inner"><span class="corners-top"><span></span></span>
				<div class="content">
					<h2>{faq_block.BLOCK_TITLE}</h2>
					<!-- BEGIN faq_row -->
						<dl class="faq" id="{faq_block.faq_row.U_FAQ_ID}">
							<dt><strong>{faq_block.faq_row.FAQ_QUESTION}</strong></dt>
							<dd>{faq_block.faq_row.FAQ_ANSWER}</dd>
							<dd><a href="#faqlinks" class="top2">{L_BACK_TO_TOP}</a></dd>
						</dl>
						<hr class="dashed" />
					<!-- END faq_row -->
				</div>
				<span class="corners-bottom"><span></span></span></div>
			</div>
		<!-- END faq_block -->
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