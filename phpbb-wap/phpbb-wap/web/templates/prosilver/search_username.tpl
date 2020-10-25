		<script type="text/javascript"><!--
		function refresh_username(selected_username)
		{
			opener.document.forms['post'].username.value = selected_username;
			opener.focus();
			window.close();
		}
		//--></script>
		<form method="post" name="search" action="{S_SEARCH_ACTION}">
		<div class="panel">
			<div class="inner"><span class="corners-top"><span></span></span>
			<h3>{L_SEARCH_USERNAME}</h3>
			<div class="content">
				<p><input type="text" name="search_username" size="25" value="{USERNAME}" class="inputbox autowidth" />&nbsp; <input type="submit" name="search" value="{L_SEARCH}" class="button1" /><br />{L_SEARCH_EXPLAIN}</p>
				<!-- BEGIN switch_select_name -->
				<p>{L_UPDATE_USERNAME}<br /><select name="username_list">{S_USERNAME_OPTIONS}</select>&nbsp; <input type="submit" class="button2" onclick="refresh_username(this.form.username_list.options[this.form.username_list.selectedIndex].value);return false;" name="use" value="{L_SELECT}" /></p>
				<!-- END switch_select_name -->
				<p class="small"><a href="javascript:window.close();">{L_CLOSE_WINDOW}</a></p>
			</div>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		</form>