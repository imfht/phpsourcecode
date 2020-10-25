				<div class="title">
<!-- BEGIN show_apcp -->
					<div class="left">
						<input type="submit" name="add_attachment_box" value="附件窗口"/>
					</div>
<!-- END show_apcp -->
<!-- BEGIN switch_posted_attachments -->
					<div class="right">
						<input type="submit" name="posted_attachments_box" value="附件列表" style="margin-right: 20px;" />
					</div>
<!-- END switch_posted_attachments -->
					<div class="clear"></div>
				</div>
				{S_HIDDEN}
<!-- BEGIN hidden_row -->
				{hidden_row.S_HIDDEN}
<!-- END hidden_row -->
				{POSTED_ATTACHMENTS_BODY}
				{ADD_ATTACHMENT_BODY}