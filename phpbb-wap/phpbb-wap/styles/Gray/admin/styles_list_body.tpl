<script type="text/javascript">
function showinfo(name, path, copyright, version)
{
	alert('风格名称：' + name + '\n安装路径：' + path + '\n版本：' + version + '\n版权：' + copyright)
}
</script>
			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级管理面板</a>&gt;<a href="{U_ADMIN_INDEX}">管理面板导航</a>&gt;风格列表</div>
				<p>注意：删除风格之前请先卸载风格，请勿直接使用FTP删除文件</p>
				<div class="title">已安装的风格</div>
<!-- BEGIN styles -->
				<div class="{styles.ROW_CLASS} module">
					{styles.L_NUMBER}、
					<a href="#" onclick="showinfo('{styles.STYLE_NAME}', '{styles.STYLE_PATH}', '{styles.STYLE_COPYRIGHT}', '{styles.STYLE_VERSION}')">{styles.STYLE_NAME}</a>
					（<a href="{styles.U_STYLE_UNINSTALL}">卸载</a> . <a href="{styles.U_DOWNLOAD}">下载</a> . <a href="{styles.U_ZIP}">备份</a> . <a href="{styles.U_DEFAULT_STYLE}">默认</a>）
				</div>
<!-- END styles -->
				<div class="title">未安装的风格</div>
<!-- BEGIN notinstall_styles -->
				<div class="{notinstall_styles.ROW_CLASS} module">
					{notinstall_styles.NUMBER}、<a href="#" onclick="showinfo('{notinstall_styles.STYLE_NAME}', '{notinstall_styles.STYLE_PATH}', '{notinstall_styles.STYLE_COPYRIGHT}', '{notinstall_styles.STYLE_VERSION}')">{notinstall_styles.STYLE_NAME}</a>
					（<a href="{notinstall_styles.U_STYLES_INSTALL}">安装</a> . <a href="{notinstall_styles.U_STYLES_DELETE}">删除</a> . <a href="{notinstall_styles.U_DOWNLOAD}">下载</a> . <a href="{notinstall_styles.U_ZIP}">备份</a>）
				</div>
<!-- END notinstall_styles -->
				<div class="title">上传风格</div>
				<p>上传风格包，必需为.zip文件</p>
				<form action="{S_UPLOAD}" method="post" enctype="multipart/form-data">
					URL上传：<input type="text" name="url" value="http://" /><input type="submit" name="import" value="上传" /><br />
					本地上传：<input type="file" name="file" /><input type="submit" value="上传" />
				</form>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_ADMIN}">返回后台</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>