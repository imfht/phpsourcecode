			<div id="main">
				<div class="title">图片评价</div>
				<form name="rateform" action="{S_ALBUM_ACTION}" method="post">
					<div class="module">
						标题：<a href="{U_PIC}" {TARGET_BLANK}>{PIC_TITLE}</a><br />
						<a href="{U_PIC}" {TARGET_BLANK}><img src="{U_THUMBNAIL}" alt="" /></a><br />
						描述：{PIC_DESC}<br />
						上传者: {POSTER}<br />
						上传时间：{PIC_TIME}<br />
						浏览次数：{PIC_VIEW}
					</div>
					<div class="module">
						他人评价：{PIC_RATING} 分<br />
						添加评价：
						<select name="rate">
							<option value="-1">{S_RATE_MSG}</option>
<!-- BEGIN rate_row -->
							<option value="{rate_row.POINT}">{rate_row.POINT}</option>
<!-- END rate_row -->
						</select>
					</div>
					<input type="submit" name="submit" value="评价" />
				</form>
			</div>