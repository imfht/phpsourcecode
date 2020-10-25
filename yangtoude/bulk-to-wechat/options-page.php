<?php
$opts = get_option('ptw_opts');
?>

<div class="wrap">
    <div id="icon-options-general" class="icon32">
        <br>
    </div>
    <h2>Push To Wechat设置</h2>
    <div id="setting-error-settings_dismissible">
        <p><strong id="s_err"></strong></p>
    </div>
    <?php if ($updated): ?>
    <div id="setting-error-settings_updated" class="updated settings-error">
        <p><strong>设置已保存。</strong></p>
    </div>
    <?php endif; ?>
    <form name="form1" method="post" action="<?php echo wp_nonce_url('./options-general.php?page=' . plugin_basename(dirname(__FILE__)) . '/bulk-to-wechat.php'); ?>" onsubmit="return validate_form(this)">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row">
                        <label for="mp_app_id">订阅号的AppID</label>
                    </th>
                    <td>
                        <input name="mp_app_id" type="text" id="mp_app_id" value="<?php echo $opts['mp_app_id']; ?>" class="regular-text" placeholder="请输入AppID">

                        <p class="description">访问 <a href="https://mp.weixin.qq.com" target="_blank">微信公众平台</a>，在“开发”-“基本配置”页面查看订阅号的AppID(应用ID)，填写以上内容。</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="mp_app_key">订阅号的AppSecret</label>
                    </th>
                    <td>
                        <input name="mp_app_key" type="text" id="mp_app_key" value="<?php echo $opts['mp_app_key']; ?>" class="regular-text" placeholder="请输入AppSecret">

                        <p class="description">访问 <a href="https://mp.weixin.qq.com" target="_blank">微信公众平台</a>，在“开发”-“基本配置”页面查看订阅号的AppSecret(应用密钥)，填写以上内容。</p>
                    </td>
                </tr>
                <tr style="display:none;" valign="top">
                    <th scope="row">
                        <label for="wx_header">微信文章头部html标签和CSS样式（选填）</label>
                    </th>
                    <td>
                        <textarea rows="10" cols="80" name="wx_header" id="wx_header" value="<?php echo $opts['wx_header']; ?>" class="regular-text" placeholder="请输入需要添加的头部html标签和CSS样式代码">
                        </textarea>

                        <p class="description">注意：CSS样式则应为内联样式。</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="qr_url">微信公众号二维码图片url地址</label>
                    </th>
                    <td>
                        <input style="width:1000px" type="text" name="qr_url" id="qr_url" value="<?php echo $opts['qr_url']; ?>" class="regular-text" placeholder="请输入微信公众号二维码图片在素材库的url地址">
                        </textarea>

                        <p class="description">访问<a href="https://mp.weixin.qq.com" target="_blank">微信公众平台</a>，在“管理”-“素材管理”-“二维码”页面查看需要插入的二维码图片url地址。</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label>推送类型</label>
                    </th>
                    <td>
                        <input name="push_type" type="radio" id="upload" value="upload" <?=$opts[ 'push_type']=='upload' ? 'checked' : '' ?>>只上传到微信素材库，不推送
                        <input name="push_type" type="radio" id="push" value="push" <?=$opts[ 'push_type']=='push' ? 'checked' : '' ?>>上传并推送到微信
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wx_wxid">接收消息的微信号</label>
                    </th>
                    <td>
                        <input name="wx_wxid" type="text" id="wx_wxid" value="<?php echo isset($opts['wx_wxid']) ? $opts['wx_wxid'] : ''; ?>" class="regular-text" placeholder="请输入微信号">

                        <p class="description"><strong>请注意：只有选择“上传并推送到微信”时才需要填写。</strong>测试发送请填写对应的微信号，正式群发请填写all。</p>
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
 * 获取radios表单元素
 *
 * @param {Array} radios radio对象数组
 * @param {string} alerttxt 提示信息
 * @return {boolean}
 */
function validate_radio(radios, alerttxt) {
	var tag = false;
	for (radio in radios) {
		if (radios[radio].checked) {
			tag = true;
			break;
		}
	}
	if (!tag) {
		document.getElementById('setting-error-settings_dismissible')
			.setAttribute('class', 'error notice is-dismissible');
		document.getElementById('s_err').innerHTML = alerttxt;
	}
	return tag;
}

/**
 * 验证表单元素
 *
 * @param {Object} thisform 验证的表单
 * @return {boolean}
 */
function validate_form(thisform) {
	// form.field_id 表单根据id来获取对应的表单域
	if (validate_required(thisform.mp_app_id, "订阅号的AppID必须填写!") == false) {
		mp_app_id.focus();
		return false;
	}
	if (validate_required(thisform.mp_app_key, "订阅号的AppSecret必须填写!") == false) {
		mp_app_key.focus();
		return false;
	}
	if (validate_required(thisform.qr_url, "微信公众号二维码图片url地址!") == false) {
		qr_url.focus();
		return false;
	}
	if (validate_radio(document.getElementsByName('push_type'), "推送类型必须选择") == false) {
		// 验证radios数组
		return false;
	}
	if (document.getElementById('push').checked) {
		// upload radio被选中后验证
		if (validate_required(thisform.wx_wxid, "接收消息的微信号必须填写!") == false) {
			wx_wxid.focus();
			return false;
		}
	}
}
</script>
