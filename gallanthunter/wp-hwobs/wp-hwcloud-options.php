<div class="wrap" style="margin: 10px;">
    <h2>华为云 OBS 设置</h2>
    <?php if ($settings_updated): ?>
        <div id="setting-error-settings-updated" class="updated settings-error">
            <p><strong>设置已保存。</strong></p></div>
    <?php endif; ?>
    <form name="form1" method="post"
          action="<?php echo wp_nonce_url('./options-general.php?page=' . plugin_basename(dirname(__FILE__)) . '/wp-hwcloud-obs.php'); ?>">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <legend>Bucket</legend>
                </th>
                <td>
                    <input type="text" name="bucket" value="<?php echo $obs_bucket; ?>" size="50"
                           placeholder="Bucket Name"/>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <legend>accessKey</legend>
                </th>
                <td><input type="text" name="access_key" value="<?php echo $obs_access_key; ?>" size="50"
                           placeholder="accessKey"/></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <legend>secretKey</legend>
                </th>
                <td>
                    <input type="text" name="secret_key" value="<?php echo $obs_secret_key; ?>" size="50"
                           placeholder="secretKey"/>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <legend>Endpoint</legend>
                </th>
                <td><select name="endpoint">
                        <option value="obs.cn-north-4.myhuaweicloud.com" <?php if ($obs_endpoint == 'obs.cn-north-4.myhuaweicloud.com') {
                            echo ' selected="selected"';
                        } ?>>华北-北京四
                        </option>
                        <option value="obs.cn-north-1.myhuaweicloud.com" <?php if ($obs_endpoint == 'obs.cn-north-1.myhuaweicloud.com') {
                            echo ' selected="selected"';
                        } ?>>华北-北京一
                        </option>
                        <option value="obs.cn-east-2.myhuaweicloud.com" <?php if ($obs_endpoint == 'obs.cn-east-2.myhuaweicloud.com') {
                            echo ' selected="selected"';
                        } ?>>华东-上海二
                        </option>
                        <option value="obs.cn-east-3.myhuaweicloud.com" <?php if ($obs_endpoint == 'obs.cn-east-3.myhuaweicloud.com') {
                            echo ' selected="selected"';
                        } ?>>华东-上海一
                        </option>
                        <option value="obs.cn-south-1.myhuaweicloud.com" <?php if ($obs_endpoint == 'obs.cn-south-1.myhuaweicloud.com') {
                            echo ' selected="selected"';
                        } ?>>华南-广州
                        </option>
                        <option value="obs.cn-southwest-2.myhuaweicloud.com" <?php if ($obs_endpoint == 'obs.cn-southwest-2.myhuaweicloud.com') {
                            echo ' selected="selected"';
                        } ?>>西南-贵阳一
                        </option>
                        <option value="obs.ap-southeast-2.myhuaweicloud.com" <?php if ($obs_endpoint == 'obs.ap-southeast-2.myhuaweicloud.com') {
                            echo ' selected="selected"';
                        } ?>>亚太-曼谷
                        </option>
                        <option value="obs.ap-southeast-1.myhuaweicloud.com" <?php if ($obs_endpoint == 'obs.ap-southeast-1.myhuaweicloud.com') {
                            echo ' selected="selected"';
                        } ?>>亚太-香港
                        </option>
                        <option value="obs.ap-southeast-3.myhuaweicloud.com" <?php if ($obs_endpoint == 'obs.ap-southeast-3.myhuaweicloud.com') {
                            echo ' selected="selected"';
                        } ?>>亚太-新加坡
                        </option>
                        <option value="obs.af-south-1.myhuaweicloud.com" <?php if ($obs_endpoint == 'obs.af-south-1.myhuaweicloud.com') {
                            echo ' selected="selected"';
                        } ?>>非洲-约翰内斯堡
                        </option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <legend>Bucket域名设置:</legend>
                </th>
                <td><input type="text" name="domain" value="<?php echo $obs_domain; ?>" size="50"
                           placeholder="http://">
                    <p class="description">请填写OBS Bucket绑定的域名（请根据实际情况填写"http://"或者"https://"前缀）</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <legend>是否上传传缩略图</legend>
                </th>
                <td>
                    <input type="checkbox" name="is_upload_thumb" <?php if ($obs_is_upload_thumb == true) {
                        echo 'checked="TRUE"';
                    } ?>/>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <legend>是否本地保留备份</legend>
                </th>
                <td>
                    <input type="checkbox" name="is_save_media_local" <?php if ($obs_is_save_media_local == true) {
                        echo 'checked="TRUE"';
                    } ?> />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <legend>更新选项</legend>
                </th>
                <td>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="保存更改"/>
                </td>
            </tr>
        </table>
        <input type="hidden" name="type" value="obs_set">
    </form>
</div>