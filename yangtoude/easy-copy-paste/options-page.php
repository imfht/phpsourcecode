<div class="wrap">
    <div id="icon-options-general" class="icon32">
        <br>
    </div>
    <h2>Easy Copy Paste设置</h2>
    <div id="setting-error-settings_dismissible">
        <p><strong id="s_err"></strong></p>
    </div>
    <?php if ($updated): ?>
    <div id="setting-error-settings_updated" class="updated settings-error">
        <p><strong>设置已保存。</strong></p>
    </div>
    <?php endif; ?>
    <form name="form1" method="post" action="<?php echo wp_nonce_url('./options-general.php?page=' . plugin_basename(dirname(__FILE__)) . '/easy-copy-paste.php'); ?>" onsubmit="return validate_form(this)">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row">
                        <label for="host_id">网站主机域名</label>
                    </th>
                    <td>
                        <input name="host" type="text" id="host_id" value="<?php echo $ecp_options['host']; ?>" class="regular-text" placeholder="请输入网站主机域名">

                        <p class="description">请填写网站的主机域名(url地址)，比如，example.com。注意：不要带http://或https://前缀。</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="保存更改">
        </p>
    </form>
</div>

<script>
/**
 * 获取input表单元素，设置提示信息和样式
 *
 * @param {Object} field 表单元素
 * @param {string} alerttxt 提示信息
 * @return {boolean}
 */
function validate_required(field, alerttxt) {
	if (null == field.value || "" == field.value) {
		document.getElementById('setting-error-settings_dismissible')
			.setAttribute('class', 'error notice is-dismissible');
		document.getElementById('s_err').innerHTML = alerttxt;
		return false;
	} else {
		return true;
	}
}

/**
 * 验证表单元素
 *
 * @param {Object} thisform 验证的表单
 * @return {boolean}
 */
function validate_form(thisform) {
	// form.field_id 表单根据id来获取对应的表单域
	if (validate_required(thisform.host_id, "网站主机域名必须填写!") == false) {
		host_id.focus();
		return false;
	}
}
</script>
