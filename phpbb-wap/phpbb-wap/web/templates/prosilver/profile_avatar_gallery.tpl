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
				<h2>{L_AVATAR_GALLERY}</h2>
				<div class="panel">
					<div class="inner"><span class="corners-top"><span></span></span>
					<fieldset>
						<label>{L_CATEGORY}: {S_CATEGORY_SELECT}</label>
						<input type="submit" value="{L_GO}" name="avatargallery" class="button2" />
					</fieldset>
					<table id="gallery">
						<!-- BEGIN avatar_row -->
						<tr>
							<!-- BEGIN avatar_column -->
							<td><img src="{avatar_row.avatar_column.AVATAR_IMAGE}" alt="" /><br /></td>
							<!-- END avatar_column -->
						</tr>
						<tr>
							<!-- BEGIN avatar_option_column -->
							<td><input type="radio" name="avatarselect" value="{avatar_row.avatar_option_column.S_OPTIONS_AVATAR}" /></td>
							<!-- END avatar_option_column -->
						</tr>
						<!-- END avatar_row -->
					</table>
					<span class="corners-bottom"><span></span></span></div>
				</div>
				<fieldset class="submit-buttons">
					{S_HIDDEN_FIELDS}<input type="submit" value="{L_SELECT_AVATAR}" name="submitavatar" class="button2" />&nbsp; <input type="submit" name="cancelavatar" value="{L_RETURN_PROFILE}" class="button2" />
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