<h2>添加支付：</h2>
<hr class="mb10"></hr>

<form enctype="multipart/form-data" onsubmit="return check_form(document.add);" method="post" action="#">
	<div id="con_one_1" class="form_box">
		<table>
			<tr>
				<th>排序：</th>
				<td>
					<input class="input w100" type="text" name="sort">默认为0
				</td>
            </tr>
			<tr>
				<th>是否开启：</th>
				<td>
					<label class="attr"><input name="state" type="radio" value="0" checked="checked">开启</label>
					<label class="attr"><input name="state" type="radio" value="1">关闭</label>
				</td>
            </tr>
			<tr>
				<th>简单描述：</th>
				<td>
					<textarea class="w400 h100" type="text" name="description"></textarea>
				</td>
            </tr>
			<tr>
				<th>商户号/收款帐号：</th>
				<td>
					<input class="input w400" type="text" name="payid">
				</td>
			</tr>
            <tr>
				<th>PayKey：</th>
				<td>
					<input class="input w400" type="text" name="paykey">
				</td>
            </tr>
			<tr>
				<th>PaySignKey：</th>
				<td>
					<textarea class="w400 h100" type="text" name="paysignkey"></textarea>
				</td>
            </tr>
		</table>
	</div>
	<div class="btn">
		<input class="button" value="确定" type="submit">
        <input class="button" value="重置" type="reset">
	</div>
</form>