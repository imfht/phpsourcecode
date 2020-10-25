
    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br></div>
        <h2>百度云BOS存储设置</h2>
        <?php if ($settings_updated): ?>
            <div id="setting-error-settings_updated" class="updated settings-error">
                <p><strong>设置已保存。</strong></p></div>
        <?php endif; ?>
        <form name="form1" method="post"
              action="<?php echo wp_nonce_url('./options-general.php?page=' . plugin_basename(dirname(__FILE__)) . '/wp-bos.php'); ?>">
            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <th scope="row"><label for="bucket">Bucket设置</label></th>
                    <td>
                        <input name="bucket" type="text" id="bucket" value="<?php echo $bos_bucket; ?>"
                               class="regular-text" placeholder="请输入云存储使用的 Bucket">

                        <p class="description">访问 <a href="http://console.bce.baidu.com/bos/" target="_blank">百度开放云对象存储BOS</a> 创建
                            Bucket ，设置权限为“公有读”，填写以上内容。</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="ak">Access Key / API key(AK)</label></th>
                    <td><input name="ak" type="text" id="ak"
                               value="<?php echo $bos_ak; ?>" class="regular-text">

                        <p class="description">访问“安全认证”->“ <a href="http://console.bce.baidu.com/iam/#/iam/accesslist"
                                                     target="_blank">AccessKey</a>”，获取 AK和SK。</p></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="sk">Secret Key (SK)</label></th>
                    <td><input name="sk" type="text" id="sk"
                               value="<?php echo $bos_sk; ?>" class="regular-text">
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="host">HOST设置</label></th>
                    <td><input name="host" type="text" id="host"
                               value="<?php echo $bos_host; ?>" class="regular-text">

                        <p class="description">根据地域设置HOST，例如“华北 - 北京”为bj.bcebos.com（请根据实际情况填写"http://"或"https://"前缀）</p></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="domain">BOS Bucket CDN域名设置(选填)</label></th>
                    <td><input name="domain" type="text" id="domain"
                               value="<?php echo $bucket_domain; ?>" class="regular-text">

                        <p class="description">请填写BOS Bucket绑定的CDN加速域名（请根据实际情况填写"http://"或者"https://"前缀）</p></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="path">上传文件夹设置</label></th>
                    <td><input name="path" type="text" id="path"
                               value="<?php echo $upload_path; ?>" class="regular-text">

                        <p class="description">填写需要上传到bucket下文件夹的名称</p></td>
                </tr>
                </tbody>
            </table>
            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="保存更改">
            </p>
        </form>
    </div>
