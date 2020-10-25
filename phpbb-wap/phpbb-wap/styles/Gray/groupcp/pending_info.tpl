				<div class="title">审核用户</div>
<!-- BEGIN pending_members_row -->
				<div class="row1">
					<input type="checkbox" name="pending_members[]" value="{pending_members_row.USER_ID}" checked="checked" />
					<a href="{pending_members_row.U_VIEWPROFILE}">{pending_members_row.USERNAME}</a>
					[帖子：{pending_members_row.POSTS} - {pending_members_row.PM} - {pending_members_row.EMAIL}]
				</div>
<!-- END pending_members_row -->
				<input type="submit" name="approve" value="通过" />
				<input type="submit" name="deny" value="拒绝" />