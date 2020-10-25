<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_GROUP_PERMISSIONS_TITLE}</div>
<span class="genmed">{L_GROUP_PERMISSIONS_EXPLAIN}</span>
<form method="post" action="{A_PERM_ACTION}">
<div class="catSides">{L_ALLOWED_FORUMS}</div>
<div class="row1">
<!-- BEGIN allow_option_values -->
<input type="checkbox" name="entries[]" value="{allow_option_values.VALUE}" /> {allow_option_values.OPTION}<br/>
<!-- END allow_option_values -->
<input type="submit" name="del_forum" value="{L_REMOVE_SELECTED}" />
<input type="submit" name="close_perm" value="{L_CLOSE_WINDOW}" />
<input type="hidden" name="e_mode" value="perm" />
</div>
</form>
<form method="post" action="{A_PERM_ACTION}">
<div class="catSides">{L_ADD_FORUMS}</div>
<div class="row1">
<!-- BEGIN forum_option_values -->
<input type="checkbox" name="entries[]" value="{forum_option_values.VALUE}" /> {forum_option_values.OPTION}<br/>
<!-- END forum_option_values -->
<input type="submit" name="add_forum" value="{L_ADD_SELECTED}" />
<input type="hidden" name="e_mode" value="perm" />
</div>
</form>