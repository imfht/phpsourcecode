<script type="text/javascript">
function showinfo(author, version, url, show, power, desc)
{
	var show_text = '';
	
	if ( show == 1 )
		show_text = '是';
	else
		show_text = '该MOD是不显示mods列表中的，需要打开指定地址才可以使用';
		
	alert('作者：' + author + '\n版本：' + version + '\n支持地址：' + url + '\n显示：' + show_text + '\n状态：' + power + '\n描述：' + desc)
}
</script>
			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;MODS列表</div>
				<div class="title">上传MODS</div>
				<form action="{S_UPLOAD}" method="post" enctype="multipart/form-data">
					URL上传：<input type="text" name="url" value="http://" /><input type="submit" name="import" value="上传" /><br />
					本地上传：<input type="file" name="file" /><input type="submit" value="上传" />
				</form>
				<div class="title">已安装的MOD</div>
<!-- BEGIN install_list -->
				<div class="module bm-gray">
					{install_list.MOD_NUMBER}、
	<!-- BEGIN admin -->
					<a href="{install_list.admin.U_ADMIN_MODS}">{install_list.admin.MOD_NAME}</a> 
					<div>
						【<a href="#" onclick="showinfo(
							'{install_list.admin.MOD_AUTHOR}',
							'{install_list.admin.MOD_VERSION}',
							'{install_list.admin.MOD_SUPPORT}',
							'{install_list.admin.MOD_SHOW}',
							'{install_list.admin.MOD_POWER}',
							'{install_list.admin.MOD_DESC}')">信息</a> /
	<!-- END admin -->
	
	<!-- BEGIN not_admin -->
					{install_list.not_admin.MOD_NAME}
					<div>
						【<a href="#" onclick="showinfo(
							'{install_list.not_admin.MOD_AUTHOR}',
							'{install_list.not_admin.MOD_VERSION}',
							'{install_list.not_admin.MOD_SUPPORT}',
							'{install_list.not_admin.MOD_POWER}',
							'{install_list.not_admin.MOD_DESC}')">信息</a> /
	<!-- END not_admin -->
						{install_list.S_MOD_SHOW} / {install_list.MOD_POWER} / <a href="{install_list.U_MOD_UNINSTALL}">卸载</a> / <a href="{install_list.U_MOD_DELETE}">删除</a>】
					</div>
				</div>
<!-- END install_list -->
				<div class="title">未安装的MOD</div>
<!-- BEGIN uninstall_list -->
				<div class="{uninstall_list.ROW_CLASS} module">
					<div>
						{uninstall_list.MOD_NUMBER}、{uninstall_list.MOD_NAME}
						【<a href="{uninstall_list.U_MOD_INSTALL}">安装</a> / <a href="{uninstall_list.U_MOD_DELETE}">删除</a>】</div>
					<div>MOD作者：{uninstall_list.MOD_AUTHOR}</div>
					<div>支持版本：{uninstall_list.MOD_VERSION}</div>
					<div>支持地址：{uninstall_list.MOD_SUPPORT}</div>
					<div>MOD描述：{uninstall_list.MOD_DESCRIPTION}</div>				
				</div>
<!-- END uninstall_list -->
			</div>