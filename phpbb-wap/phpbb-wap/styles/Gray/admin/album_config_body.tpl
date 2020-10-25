			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;相册参数配置</div>
				<form action="{S_ALBUM_CONFIG_ACTION}" method="post">
					<div class="title">常规选项</div>
					<div class="module row1">
						相册最多允许上传
						<input type="text" maxlength="9" name="max_pics" value="{MAX_PICS}" size="3" />
						图片（-1表示无限）
					</div>
					<div class="module row2">
						用户相册最多允许上传
						<input type="text" maxlength="12" name="user_pics_limit" value="{USER_PICS_LIMIT}" size="3" />
						图片（-1表示无限）
					</div>
					<div class="module row1">
						版主相册最多允许上传
						<input type="text" maxlength="12" name="mod_pics_limit" value="{MOD_PICS_LIMIT}" size="3" /> 
						图片（-1表示无限）
					</div>
					<div class="module row2">
						单个图片大小不能超过
						<input type="text" maxlength="12" name="max_file_size" value="{MAX_FILE_SIZE}" size="3" />
						Byte
					</div>
					<div class="module row1">
						图片的像素宽不能超过
						<input type="text" maxlength="9" name="max_width" value="{MAX_WIDTH}" size="3" />
						像素
					</div>
					<div class="module row2">
						图片的像素高不能超过
						<input type="text" maxlength="9" name="max_height" value="{MAX_HEIGHT}" size="3" />
						像素
					</div>
					<div class="module row1">
						图片的描述文字不能超过
						<input type="text" name="desc_length" value="{PIC_DESC_MAX_LENGTH}" size="3" />
						字节
					</div>
					<div class="module row2">
						GD版本：
						<input type="radio" {NO_GD} name="gd_version" value="0" /> 无&nbsp;&nbsp;
						<input type="radio" {GD_V1} name="gd_version" value="1" /> GD1&nbsp;&nbsp;
						<input type="radio" {GD_V2} name="gd_version" value="2" /> GD2&nbsp;&nbsp;
					</div>
					<div class="module row1">
						允许上传JPG格式图片：
						<input type="radio" {JPG_ENABLED} name="jpg_allowed" value="1" /> 是&nbsp;&nbsp;
						<input type="radio" {JPG_DISABLED} name="jpg_allowed" value="0" /> 否
					</div>
					<div class="module row2">
						允许上传PNG格式图片：
						<input type="radio" {PNG_ENABLED} name="png_allowed" value="1" /> 是&nbsp;&nbsp;
						<input type="radio" {PNG_DISABLED} name="png_allowed" value="0" /> 否
					</div>
					<div class="module row1">
						允许上传GIF格式图片：
						<input type="radio" {GIF_ENABLED} name="gif_allowed" value="1" /> 是&nbsp;&nbsp;
						<input type="radio" {GIF_DISABLED} name="gif_allowed" value="0" /> 否
					</div>
					<div class="module row2">
						允许热点链接：
						<input type="radio" {HOTLINK_PREVENT_ENABLED} name="hotlink_prevent" value="1" /> 是&nbsp;&nbsp;
						<input type="radio" {HOTLINK_PREVENT_DISABLED} name="hotlink_prevent" value="0" /> 否
					</div>
					<div class="module row1">
						允许的热点链接：<input type="text" name="hotlink_allowed" value="{HOTLINK_ALLOWED}" />
					</div>
					<div class="title">缩略图选项</div>
					<div class="module row2">
						缩略图缓存：
						<input type="radio" {THUMBNAIL_CACHE_ENABLED} name="thumbnail_cache" value="1" /> 是&nbsp;&nbsp;
						<input type="radio" {THUMBNAIL_CACHE_DISABLED} name="thumbnail_cache" value="0" /> 否
					</div>
					<div class="module row1">
						缩略图略图大小 <input type="text" maxlength="4" name="thumbnail_size" value="{THUMBNAIL_SIZE}" size="3" /> （单位/像素）
					</div>
					<div class="module row2">
						缩略图的质量 <input type="text" maxlength="3" name="thumbnail_quality" value="{THUMBNAIL_QUALITY}" size="3" /> （0-100之间）
					</div>
					<div class="module row1">
						每页显示 <input type="text" maxlength="2" name="rows_per_page" value="{ROWS_PER_PAGE}" size="3" /> 个图片
					</div>
					<div class="module row2">
						默认排序方法
						<select name="sort_method">
							<option {SORT_TIME} value='pic_time'>上传时间</option>
							<option {SORT_PIC_TITLE} value='pic_title'>标题</option>
							<option {SORT_USERNAME} value='username'>用户名</option>
							<option {SORT_VIEW} value='pic_view_count'>浏览次数</option>
							<option {SORT_RATING} value='rating'>评价</option>
							<option {SORT_COMMENTS} value='comments'>评论</option>
							<option {SORT_NEW_COMMENT} value='new_comment'>最新评论</option>
						</select>
					</div>
					<div class="module row1">
						默认显示方式
						<select name="sort_order">
							<option {SORT_ASC} value='ASC'>从低到高</option>
							<option {SORT_DESC} value='DESC'>从高到低</option>
						</select>
					</div>
					<div class="module row2">
						在新窗口中浏览图片
						<input type="radio" {FULLPIC_POPUP_ENABLED} name="fullpic_popup" value="1" /> 是&nbsp;&nbsp;
						<input type="radio" {FULLPIC_POPUP_DISABLED} name="fullpic_popup" value="0" /> 否
					</div>
					<div class="title">权限设置</div>
					<div class="module row1">
						创建相册需要权限为
						<input type="radio" {PERSONAL_GALLERY_USER} name="personal_gallery" value="{S_USER}" /> 注册用户&nbsp;&nbsp;
						<input type="radio" {PERSONAL_GALLERY_PRIVATE} name="personal_gallery" value="{S_PRIVATE}" /> 私有用户&nbsp;&nbsp;
						<input type="radio" {PERSONAL_GALLERY_ADMIN} name="personal_gallery" value="{S_ADMIN}" /> 超级管理员
					</div>
					<div class="module row2">
						个人相册限制 <input type="text" maxlength="5" name="personal_gallery_limit" value="{PERSONAL_GALLERY_LIMIT}" size="3" />
					</div>
					<div class="module row1">
						<input type="radio" {PERSONAL_GALLERY_VIEW_ALL} name="personal_gallery_view" value="{S_GUEST}" /> 匿名用户&nbsp;&nbsp;
						<input type="radio" {PERSONAL_GALLERY_VIEW_REG} name="personal_gallery_view" value="{S_USER}" /> 注册用户&nbsp;&nbsp;
						<input type="radio" {PERSONAL_GALLERY_VIEW_PRIVATE} name="personal_gallery_view" value="{S_PRIVATE}" /> 私有用户
					</div>
					<div class="module row2">
						允许对图片进行评价
						<input type="radio" {RATE_ENABLED} name="rate" value="1" /> 是&nbsp;&nbsp;
						<input type="radio" {RATE_DISABLED} name="rate" value="0" /> 否
					</div>
					<div class="module row1">
						评价的总分为 
						<input type="text" name="rate_scale" value="{RATE_SCALE}" size="3" />
						分
					</div>
					<div class="module row2">
						允许对图片进行评论
						<input type="radio" {COMMENT_ENABLED} name="comment" value="1" /> 是&nbsp;&nbsp;
						<input type="radio" {COMMENT_DISABLED} name="comment" value="0" /> 否
					</div>
					{S_HIDDEN_FIELDS}
					<input type="submit" name="submit" value="保存修改" />
				</form>
			</div>