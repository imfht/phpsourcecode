			<div id="main">
				<div class="title">编辑图片信息</div>
				<form name="editform" action="{S_ALBUM_ACTION}" method="post">
					<div class="module">
						标题：<br />
						<input type="text" name="pic_title" value="{PIC_TITLE}" />
					</div>
					<div class="module">
						描述：（不能超过 <b>{S_PIC_DESC_MAX_LENGTH}</b> 字节）<br />
						<textarea name="pic_desc" rows="5">{PIC_DESC}</textarea>
					</div>
					<input type="submit" name="submit" value="保存" />
				</form>
			</div>