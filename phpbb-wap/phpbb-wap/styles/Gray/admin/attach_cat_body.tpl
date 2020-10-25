			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;特殊类别</div>
				{ERROR_BOX}
				<form action="{S_ATTACH_ACTION}" method="post">
					<div class="title">特殊类别</div>
					<div class="module bm-gray">
						<label>分配到小组：</label>
						{S_ASSIGNED_GROUP_IMAGES}
					</div>
					<div class="module bm-gray">
						<label>直接显示附件图片：</label>
						<div><input type="radio" name="img_display_inlined" value="1" {DISPLAY_INLINED_YES} /> 是</div>
						<div><input type="radio" name="img_display_inlined" value="0" {DISPLAY_INLINED_NO} /> 否</div>
					</div>
<!-- BEGIN switch_thumbnail_support -->
					<div class="module bm-gray">
						<label>缩略图：</label>
						<p>这个功能几乎推翻在这个特殊类别全部的设定，除了最大图片尺寸之外。使用这个功能将使缩略图被显示于发表的文章中，使用者可以点击缩略图来开启图片。请注意这功能需要安装 Imagick，如果没有安装 Imagick 而且假如已启用安全模式，则 PHP 的 GD-Extention 将被使用。如果图片类型是 PHP 不支援的，这个功能将无法使用。</p>
						<div><input type="radio" name="img_create_thumbnail" value="1" {CREATE_THUMBNAIL_YES} /> 是</div>
						<div><input type="radio" name="img_create_thumbnail" value="0" {CREATE_THUMBNAIL_NO} /> 否</div>
					</div>
					<div class="module bm-gray">
						<label>缩略图最小限制：</label>
						<p>当文件大小大于多少Byte时才创建缩略图</p>
						<div><input type="text" size="7" maxlength="15" name="img_min_thumb_filesize" value="{IMAGE_MIN_THUMB_FILESIZE}" /> Byte</div>
					</div>
					<div class="module bm-gray">
						<label>使用GD2扩展：</label>
						<p>PHP将可以使用GD1或GD2扩展功能对图片进行操作. 要正确生成缩略图，并不合使用imagemagick,附件Mod提供了两种不同的方法，可以由您来进行选择. 如果缩图质量差或图片过大，您可以尝试改变设置</p>
						<div><input type="radio" name="use_gd2" value="1" {USE_GD2_YES} /> 是</div>
						<div><input type="radio" name="use_gd2" value="0" {USE_GD2_NO} /> 否</div>
					</div>
<!-- END switch_thumbnail_support -->
					<div class="module bm-gray">
						<label>Imagick 缩略图程序路径：</label>
						<p>请输入完整的路径，这功能需要安装 imagemagick 才可以使用！</p>
						<p>一般情况下是：/usr/local/bin/convert</p>
						<p>windows系统：c:/imagemagick/convert.exe</p>
						<div><input type="text" size="20" maxlength="200" name="img_imagick" value="{IMAGE_IMAGICK_PATH}" /></div>
					</div>
					<div class="module bm-gray">
						<label>图片的最大限制（单位/像素）</label>
						<br />
						<input type="text" size="3" maxlength="4" name="img_max_width" value="{IMAGE_MAX_WIDTH}" />
						x
						<input type="text" size="3" maxlength="4" name="img_max_height" value="{IMAGE_MAX_HEIGHT}" />
					</div>
					<div class="module">
						<label>链接图片的最大限制（单位/像素）</label>
						<br />
						<input type="text" size="3" maxlength="4" name="img_link_width" value="{IMAGE_LINK_WIDTH}" />
						x
						<input type="text" size="3" maxlength="4" name="img_link_height" value="{IMAGE_LINK_HEIGHT}" />
					</div>
					{S_HIDDEN_FIELDS}
					<div class="center">
						<input type="submit" name="submit" value="保存" />
						<input type="submit" name="search_imagick" value="搜索imagick" />
						<input type="submit" name="cat_settings" value="测试" />
					</div>
				</form>
			</div>