			<div id="main">
				<form action="{S_ALBUM_ACTION}" method="post">
					<div class="title">移动图片</div>
					<div class="module">
						图片移动到 {S_CATEGORY_SELECT}
						<input type="submit" name="move" value="确认" />
					</div>
<!-- BEGIN pic_id_array -->
					<input type="hidden" name="pic_id[]" value="{pic_id_array.VALUE}" />
<!-- END pic_id_array -->
				</form>
			</div>