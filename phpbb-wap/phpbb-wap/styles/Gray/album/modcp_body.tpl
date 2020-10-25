			<div id="main">
				<form name="modcp" action="{S_ALBUM_ACTION}" method="post">
					<div class="title">相册版主管理面板</div>
<!-- BEGIN no_pics -->
					<div class="row1">还没有任何图片</div>
<!-- END no_pics -->
<!-- BEGIN picrow -->
					<div class="module {picrow.ROW_CLASS}">
						<input type="checkbox" name="pic_id[]" value="{picrow.PIC_ID}" />
						{picrow.PIC_TITLE}<br />
						上传者：{picrow.POSTER}<br />
						上传时间：{picrow.TIME}<br />
						评价：{picrow.RATING}<br />
						评论：{picrow.COMMENTS}<br />
						状态：{picrow.LOCK}<br />
						审核：{picrow.APPROVAL}<br />
					</div>
<!-- END picrow -->
					<input type="hidden" name="mode" value="modcp" />
					<input type="submit" name="move" value="移动" />
					<input type="submit" name="lock" value="锁定" />
					<input type="submit" name="unlock" value="解锁" />
					{DELETE_BUTTON}
					{APPROVAL_BUTTON}
					{UNAPPROVAL_BUTTON}
				</form>
				{PAGINATION}
			</div>