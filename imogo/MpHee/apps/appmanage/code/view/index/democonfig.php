<h2>示例APP配置：</h2>
<hr class="mb10"></hr>

<form method="post" action="#">
    <div class="form_box">
        <table>
            <tr>
				<th>应用名称：</th>
				<td><input class="input w200" value="{$democonfiginfo['APP_NAME']}" type="text" name="APP_NAME"></td>
            </tr>
            <tr>
				<th>应用作者：</th>
				<td><input class="input w200" value="{$democonfiginfo['APP_AUTHOR']}" type="text" name="APP_AUTHOR"></td>
            </tr>
            <tr>
				<th>应用版本：</th>
				<td><input class="input w200" value="{$democonfiginfo['APP_VER']}" type="text" name="APP_VER">请遵循“版本号.年.月日”的规则，例：1.0.2014.0910</td>
            </tr>
            <tr>
				<th>应用排序：</th>
				<td><input class="input w200" value="{$democonfiginfo['APP_SORT']}" type="text" name="APP_SORT">请使用整型数字</td>
            </tr>
            <tr>
				<th>应用数据表：</th>
				<td><input class="input w400" value="{$democonfiginfo['APP_TABLES']}" type="text" name="APP_TABLES">若有多个，用“,”分割</td>
            </tr>
        </table>
	</div>
	<div class="btn">
		<input class="button" value="确定" type="submit">
		<input class="button" value="重置" type="reset">
	</div>
</form>