			<div id="main">
				<div class="title">上传照片</div>
				<form name="upload" action="{S_ALBUM_ACTION}" method="post" enctype="multipart/form-data">
<!-- BEGIN switch_user_logged_out -->
					<div class="module">
						用户名：<input type="text" name="pic_username" maxlength="32" />
					</div>
<!-- END switch_user_logged_out -->
					<div class="module">
						标题：<input type="text" name="pic_title" />
					</div>
					<div class="module">
						描述：（不能超过 <b>{S_PIC_DESC_MAX_LENGTH}</b> 字节）<br />
						<textarea name="pic_desc" rows="5" cols="15" style="width: 235px;"></textarea>
					</div>
					<div class="module">
						选择文件：<input type="file" name="pic_file" />
					</div>
<!-- BEGIN switch_manual_thumbnail -->
					<div class="module">
						缩略图：<input type="file" name="pic_thumbnail" /><br/>
						大小：<b>{S_THUMBNAIL_SIZE}</b>
					</div>
<!-- END switch_manual_thumbnail -->
					<div class="module">选择类型：{SELECT_CAT}</div>
					<div class="title">上传限制</div>
					<div class="row1">
						文件大小限制：<b>{S_MAX_FILESIZE}</b><br/>
						像素宽限制：<b>{S_MAX_WIDTH}</b><br/>
						像素高限制：<b>{S_MAX_HEIGHT}</b><br/>
						上传JPG图片：<b>{S_JPG}</b><br/>
						上传PNG图片：<b>{S_PNG}</b><br/>
						上传GIF图片：<b>{S_GIF}</b>
					</div>
					<input type="submit" name="submit" value="提交上传" />
				</form>
			</div>