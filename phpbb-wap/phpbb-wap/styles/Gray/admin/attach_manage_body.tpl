			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;附件配置</div>
				{ERROR_BOX}
				<p>在这里您可以调整一些关于附件功能的配制</p>
				<form action="{S_ATTACH_ACTION}" method="post">
					<div class="title">一般配置</div>
					<div class="module bm-gray">
						<label>附件功能开关：</label>
						<p>关闭后将不能使用附件功能</p>
						<div><input type="radio" name="disable_mod" value="0" {DISABLE_MOD_NO} /> 开启</div>
						<div><input type="radio" name="disable_mod" value="1" {DISABLE_MOD_YES} /> 关闭</div>
					</div>
					<div class="module bm-gray">
						<label>文件的上传目录：</label>
						<div id="upload-dir"><input type="text" maxlength="100" name="upload_dir" value="{UPLOAD_DIR}" /></div>
					</div>
					<div class="module bm-gray">
						<label>附件图标：</label>
						<p>这是在文件名前面的图标，这个设置会覆盖扩展名组中的图标设置，留空则不显示</p>
						<div id="upload-img"><input type="text" maxlength="100" name="upload_img" value="{ATTACHMENT_IMG_PATH}" /></div>
					</div>
					<div class="module bm-gray">
						<label>帖子附件图标：</label>
						<p>这是帖子标题前面的图标，留空则不显示</p>
						<div id="topic-icon"><input type="text" maxlength="100" name="topic_icon" value="{TOPIC_ICON}" /></div>
					</div>
					<div class="module bm-gray">
						<label>附件的显示顺序：</label>
						<div id="display-order-desc"><input type="radio" name="display_order" value="0" {DISPLAY_ORDER_DESC} /> 新的在前</div>
						<div id="display-order-asc"><input type="radio" name="display_order" value="1" {DISPLAY_ORDER_ASC} /> 旧的在前</div>
					</div>
					<div class="module bm-gray">
						<label>积分的名称：</label>
						<div id="download-cut-points"><input type="text" size="3" name="points_name" value="{POINTS_NAME}" /></div>
					</div>
					<div class="module bm-gray">
						<label>附件下载方式：</label>
						<p>直接读取不会暴露文件的真实地址</p>
						<div id="download-mode-yes"><input type="radio" name="download_mode" value="1" {DOWNLOAD_MODE_YES} /> 直接读取</div>
						<div id="download-mode-no"><input type="radio" name="download_mode" value="0" {DOWNLOAD_MODE_NO} /> 跳转到文件</div>
					</div>
					<div class="module bm-gray">
						<label>下载积分设置：</label>
						<p>为了防止恶意下载和减轻服务器负担，每次下载附件需要扣取一定的积分</p>
						<div id="download-cut-points"><input type="text" size="3" name="download_cut_points" value="{DOWNLOAD_CUT_POINTS}" /></div>
					</div>
					<div class="module bm-gray">
						<label>下载积分设置：</label>
						<p>为了提高上传者的积极性，当其他用户下载附件时，上传者会获得一定的积分</p>
						<div id="download-add-points"><input type="text" size="3" maxlength="3" name="download_add_points" value="{DOWNLOAD_ADD_POINTS}" /></div>
					</div>
					<div class="module bm-gray">
						<label>附件上传面板：</label>
						<div><input type="radio" name="show_apcp" value="1" {SHOW_APCP_YES} /> 简洁</div>
						<div><input type="radio" name="show_apcp" value="0" {SHOW_APCP_NO} /> 正常</div>
					</div>
					<div class="title">附件的限制设置</div>
					<div class="module bm-gray">
						<label>单个文件大小限制：</label>
						<p>0为不限制，但这功能会受PHP一些参数的限制！</p>
						<div max-filesize><input type="text" size="8" maxlength="15" name="max_filesize" value="{MAX_FILESIZE}" /> {S_FILESIZE}</div>
					</div>
					<div class="module bm-gray">
						<label>所有文件大小限制（0为不限制）：</label>
						<div id="attachment-quota"><input type="text" size="8" maxlength="15" name="attachment_quota" value="{ATTACHMENT_QUOTA}" /> {S_FILESIZE_QUOTA}</div>
					</div>
					<div class="module bm-gray">
						<label>默认上传限制设定：</label>
						<p>你可以选择一个默认限制给新注册的用户和没有指定限制的用户。选项 “没有限制” 表示没有不设定限制，而使用上面的附件大小限制设定。</p>
						<div id="selete-default-upload-limit">{S_DEFAULT_UPLOAD_LIMIT}</div>
					</div>
					<div class="module bm-gray">
						<label>帖子文件数限制（0为不限制）：</label>
						<div id="max-attachments"><input type="text" size="3" maxlength="3" name="max_attachments" value="{MAX_ATTACHMENTS}" /></div>
					</div>
					{S_HIDDEN_FIELDS}
					<input type="submit" name="submit" value="保存" />
				</form>
			</div>