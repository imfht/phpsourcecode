					<div>
						<div class="title">已上传附件</div>
<!-- BEGIN attach_row -->
						<div class="{attach_row.ROW_CLASS} module">
							<a href="{attach_row.U_VIEW_ATTACHMENT}">{attach_row.FILE_NAME}</a>
							<input class="subbutton" type="submit" name="del_attachment[{attach_row.ATTACH_FILENAME}]" value="删除"/>
	<!-- BEGIN switch_update_attachment -->
							<input class="subbutton" type="submit" name="update_attachment[{attach_row.ATTACH_ID}]" value="更新"/>
	<!-- END switch_update_attachment -->
	<!-- BEGIN switch_thumbnail -->
							<input class="subbutton" type="submit" name="del_thumbnail[{attach_row.ATTACH_FILENAME}]" value="删除缩略图"/>
	<!-- END switch_thumbnail -->
							<div>
								<label>文件描述：<label>
								<div><textarea name="comment_list[]" rows="2" size="40" class="post" style="width:99%;">{attach_row.FILE_COMMENT}</textarea></div>
							</div>
							{attach_row.S_HIDDEN}
						</div>
<!-- END attach_row -->
					</div>