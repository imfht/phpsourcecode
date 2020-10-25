<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_USERS_ADMIN}">选择用户</a>&gt;{L_AVATAR_GALLERY}</div>
<span class="genmed">{L_USER_EXPLAIN}</span>
<div class="catSides">{L_AVATAR_GALLERY}</div>
<form action="{S_PROFILE_ACTION}" method="post">	
	<div class="row1">
		{L_CATEGORY}:<br/>
		<select name="avatarcategory">
			{S_OPTIONS_CATEGORIES}
		</select>
		<input type="submit" value="{L_GO}" name="avatargallery" />
	</div>
	<!-- BEGIN avatar_row -->
	<!-- BEGIN avatar_column -->
	<div class="{avatar_row.avatar_column.ROW_CLASS}">
		<img src="{avatar_row.avatar_column.AVATAR_IMAGE}" /><br />
		<input type="radio" name="avatarselect" value="{avatar_row.avatar_column.S_OPTIONS_AVATAR}" /> 选择
	</div>
	<!-- END avatar_column -->
	<!-- END avatar_row -->
	{S_HIDDEN_FIELDS} 
	<input type="submit" name="submitavatar" value="{L_SELECT_AVATAR}" /><br />
	<input type="submit" name="cancelavatar" value="{L_RETURN_PROFILE}" />
</form>