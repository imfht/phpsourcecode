<?php
/**
 * Created by PhpStorm.
 * User: wangjunfeng
 * Date: 15-6-13
 * Time: 下午2:26
 */
if (!current_user_can('manage_options')) {
    wp_die('Insufficient privileges!');
}

if (!empty($_POST)) {
    $options = array();
    check_admin_referer('update-options');
    foreach ($_POST as $k => $v) {
        if (isset($default_options[$k])) {
            if (is_string($v) && $v) {
                $options[$k] = $v ? trim(stripslashes($v)) : $default_options[$k];
            }
            if (is_array($v)) {
                foreach ($v as $key=>$val) {
                    $options[$k][] = $val ? trim(stripslashes($val)) : $default_options[$k][$key];
                }
            }
        }
    }
    $options = array_merge($default_options, $options);
    update_option('jw_wp_level_useragent_options', $options);
    echo '<div class="updated"><p><strong>设置已保存！</strong></p></div>';
}

$jw_wlu_options = get_option('jw_wp_level_useragent_options');
$jw_wlu_options = $jw_wlu_options ? $jw_wlu_options : $default_options;
$level_name = $jw_wlu_options['level_name'];
$level_count = $jw_wlu_options['level_count'];
?>
<div class="wrap">
    <h2>WP-Level-UserAgent选项设置</h2>

    <form method="POST" action="options-general.php?page=wp-level-useragent/wp-level-useragent.php">
        <?php
        wp_nonce_field('update-options');
        ?>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label for="show_level">是否显示等级</label></th>
                <td>
                    <label title="显示"><input type="radio" <?php checked($jw_wlu_options['show_level'], '1'); ?> value="1"
                                             name="show_level">显示</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label title="不显示"><input type="radio" <?php checked($jw_wlu_options['show_level'], '0'); ?> value="0"
                                              name="show_level">不显示</label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="show_os">是否显示系统</label></th>
                <td>
                    <label title="显示"><input type="radio" <?php checked($jw_wlu_options['show_os'], '1'); ?> value="1"
                                             name="show_os">显示</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label title="不显示"><input type="radio" <?php checked($jw_wlu_options['show_os'], '0'); ?> value="0"
                                              name="show_os">不显示</label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="show_browser">是否显示浏览器</label></th>
                <td>
                    <label title="显示"><input type="radio" <?php checked($jw_wlu_options['show_browser'], '1'); ?> value="1"
                                             name="show_browser">显示</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label title="不显示"><input type="radio" <?php checked($jw_wlu_options['show_browser'], '0'); ?> value="0"
                                              name="show_browser">不显示</label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="display_position">UserAgent显示位置</label></th>
                <td>
                    <select id="display_position" name="display_position">
                        <option value="before" <?php selected($jw_wlu_options['display_position'], 'before'); ?>>评论内容之前</option>
                        <option value="after" <?php selected($jw_wlu_options['display_position'], 'after'); ?>>评论内容之后</option>
                        <option value="customer" <?php selected($jw_wlu_options['display_position'], 'customer'); ?>>自定义</option>
                    </select><br>
                    <em>若选择自定义,则在展示的地方插入相应代码,代码实例如下</em><br/><br/>

                    <div style="padding-left:20px;">
                        <link rel='stylesheet' type='text/css' href='http://tools.oschina.net/js/syntaxhighlighter_3.0.83/styles/shCoreDefault.css'/><div id="highlighter_945007" class="syntaxhighlighter  php"><div class="toolbar"><span><a href="#" class="toolbar_item command_help help">?</a></span></div><table border="0" cellpadding="0" cellspacing="0"><tbody><tr><td class="gutter"><div class="line number1 index0 alt2">1</div><div class="line number2 index1 alt1">2</div><div class="line number3 index2 alt2">3</div></td><td class="code"><div class="container"><div class="line number1 index0 alt2"><code class="php plain">&lt;?php&nbsp;</code><code class="php keyword">foreach</code>&nbsp;<code class="php plain">(</code><code class="php variable">$comments</code>&nbsp;<code class="php keyword">as</code>&nbsp;<code class="php variable">$comment</code><code class="php plain">)&nbsp;:&nbsp;?&gt;</code></div><div class="line number2 index1 alt1"><code class="php plain">&lt;cite&gt;&lt;?php&nbsp;comment_author_link()&nbsp;?&gt;&lt;/cite&gt;&nbsp;&lt;?php&nbsp;jw_level_useragent_output_custom();&nbsp;?&gt;&nbsp;says:&lt;br&nbsp;/&gt;</code></div><div class="line number3 index2 alt2"><code class="php plain">&lt;?php&nbsp;comment_text()&nbsp;?&gt;</code></div></div></td></tr></tbody></table></div>
                    </div>
                </td>
            </tr>
            <tr>
                <th><label for="user_level">评论等级</label></th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span>评论等级</span></legend>
                        <p>
                            <label for="comment_user_level">LV1:评论条数>0,
                                等级名称:
                                <input type="text" class="small-text"
                                       value="<?php echo esc_attr($level_name[0]); ?>"
                                       id="comment_user_level_name" name="level_name[]">
                            </label>
                        </p>

                        <p>
                            <label for="comment_max_links">LV2:评论条数>
                                <input type="number" class="small-text"
                                       value="<?php echo esc_attr($level_count[0]); ?>" id="comment_user_level"
                                       min="0" step="1" name="level_count[]">
                                等级名称:
                                <input type="text" class="small-text"
                                       value="<?php echo esc_attr($level_name[1]); ?>"
                                       id="comment_user_level_name" name="level_name[]">
                            </label>
                        </p>

                        <p>
                            <label for="comment_max_links">LV3:评论条数>
                                <input type="number" class="small-text"
                                       value="<?php echo esc_attr($level_count[1]); ?>" id="comment_user_level"
                                       min="0" step="1" name="level_count[]">
                                等级名称:
                                <input type="text" class="small-text"
                                       value="<?php echo esc_attr($level_name[2]); ?>"
                                       id="comment_user_level_name" name="level_name[]">
                            </label>
                        </p>

                        <p>
                            <label for="comment_max_links">LV4:评论条数>
                                <input type="number" class="small-text"
                                       value="<?php echo esc_attr($level_count[2]); ?>" id="comment_user_level"
                                       min="0" step="1" name="level_count[]">
                                等级名称:
                                <input type="text" class="small-text"
                                       value="<?php echo esc_attr($level_name[3]); ?>"
                                       id="comment_user_level_name" name="level_name[]">
                            </label>
                        </p>

                        <p>
                            <label for="comment_max_links">LV5:评论条数>
                                <input type="number" class="small-text"
                                       value="<?php echo esc_attr($level_count[3]); ?>" id="comment_user_level"
                                       min="0" step="1" name="level_count[]">
                                等级名称:
                                <input type="text" class="small-text"
                                       value="<?php echo esc_attr($level_name[4]); ?>"
                                       id="comment_user_level_name" name="level_name[]">
                            </label>
                        </p>

                        <p>
                            <label for="comment_max_links">LV6:评论条数>
                                <input type="number" class="small-text"
                                       value="<?php echo esc_attr($level_count[4]); ?>" id="comment_user_level"
                                       min="0" step="1" name="level_count[]">
                                等级名称:
                                <input type="text" class="small-text"
                                       value="<?php echo esc_attr($level_name[5]); ?>"
                                       id="comment_user_level_name" name="level_name[]">
                            </label>
                        </p>

                        <p>
                            <label for="comment_max_links">LV7:评论条数>
                                <input type="number" class="small-text"
                                       value="<?php echo esc_attr($level_count[5]); ?>" id="comment_user_level"
                                       min="0" step="1" name="level_count[]">
                                等级名称:
                                <input type="text" class="small-text"
                                       value="<?php echo esc_attr($level_name[6]); ?>"
                                       id="comment_user_level_name" name="level_name[]">
                            </label>
                        </p>

                        <p>
                            <label for="comment_max_links">LV8:评论条数>
                                <input type="number" class="small-text"
                                       value="<?php echo esc_attr($level_count[6]); ?>" id="comment_user_level"
                                       min="0" step="1" name="level_count[]">
                                等级名称:
                                <input type="text" class="small-text"
                                       value="<?php echo esc_attr($level_name[7]); ?>"
                                       id="comment_user_level_name" name="level_name[]">
                            </label>
                        </p>

                        <p>
                            <label for="comment_max_links">LV9:评论条数>
                                <input type="number" class="small-text"
                                       value="<?php echo esc_attr($level_count[7]); ?>" id="comment_user_level"
                                       min="0" step="1" name="level_count[]">
                                等级名称:
                                <input type="text" class="small-text"
                                       value="<?php echo esc_attr($level_name[8]); ?>"
                                       id="comment_user_level_name" name="level_name[]">
                            </label>
                        </p>

                        <p>
                            <label for="comment_max_links">LV10:评论条数>
                                <input type="number" class="small-text"
                                       value="<?php echo esc_attr($level_count[8]); ?>" id="comment_user_level"
                                       min="0" step="1" name="level_count[]">
                                等级名称:
                                <input type="text" class="small-text"
                                       value="<?php echo esc_attr($level_name[9]); ?>"
                                       id="comment_user_level_name" name="level_name[]">
                            </label>
                        </p>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><label for="admin_email">管理员(站长)邮箱</label></th>
                <td><input type="text" class="regular-text code" value="<?php echo esc_attr($jw_wlu_options['admin_email']); ?>"
                           id="admin_email" name="admin_email"></td>
            </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" value="保存更改" class="button button-primary" id="submit" name="submit"></p>
    </form>
</div>