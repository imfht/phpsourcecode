		<div class="panel">
			<div class="inner"><span class="corners-top"><span></span></span>
			<h3>{L_POSTED_ATTACHMENTS}</h3>
			<fieldset class="fields1">
	<!-- BEGIN attach_row -->
				<dl>
					<dt><label>{L_FILE_NAME}</label></dt>
					<dd><a href="{attach_row.U_VIEW_ATTACHMENT}" target="_blank">{attach_row.FILE_NAME}</a></dd>
				</dl>
				<dl>
					<dt><label>{L_FILE_COMMENT}</label></dt>
					<dd><textarea name="comment_list[]" rows="3" cols="35" size="40" class="post">{attach_row.FILE_COMMENT}</textarea></dd>
				</dl>
				<dl>
					<dt><label>{L_OPTIONS}</label></dt>
					<dd><input type="submit" name="edit_comment[{attach_row.ATTACH_FILENAME}]" value="{L_UPDATE_COMMENT}" class="button1" /> 
	<!-- BEGIN switch_update_attachment -->
			&nbsp; <input type="submit" name="update_attachment[{attach_row.ATTACH_ID}]" value="{L_UPLOAD_NEW_VERSION}" class="button1" /> 
	<!-- END switch_update_attachment -->
			&nbsp; <input type="submit" name="del_attachment[{attach_row.ATTACH_FILENAME}]" value="{L_DELETE_ATTACHMENT}" class="button1" /> 
	<!-- BEGIN switch_thumbnail -->
			&nbsp; <input type="submit" name="del_thumbnail[{attach_row.ATTACH_FILENAME}]" value="{L_DELETE_THUMBNAIL}" class="button1" /> 
	<!-- END switch_thumbnail -->
					</dd>
				</dl>
		{attach_row.S_HIDDEN}	
	<!-- END attach_row -->
			</fieldset>
			<span class="corners-bottom"><span></span></span></div>
		</div>