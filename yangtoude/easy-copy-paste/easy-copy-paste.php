<?php
/*
Plugin Name: Easy Copy Paste
Plugin URI:
Description: 让你的复制粘贴更加容易。文章发布或更新时，抓取文章中外站的图片上传到媒体库并替换图片的src。
Version:     1.0.0
Author:      yangtoude
Author URI:
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset
*/


define('ECP_BASEFOLDER', plugin_basename(dirname(__FILE__)));

// BOS设置页面，将BOS存入数据库
function ecp_option_page() {
    $options = array();
    $updated = false;

    if (isset($_POST['host'])) {
        $options['host'] = trim(stripslashes($_POST['host']));
    }

	// 将设置写入数据库
	if (!empty($options)) {
		// 首次安装插件时将opts写入数据库
		if (false == get_option('ecp_options')) {
			update_option('ecp_options', $options);
		} else {
			$options_a = get_option('ecp_options');
			// 为了兼容 php5.5 之前的php版本
			$flag1 = array_diff_assoc($options, $options_a);
			$flag2 = array_diff_assoc($options_a, $options);
			// 比较输入的options和数据库中的$options_a是否相同，若相同则不更新数据库
			if (!empty($flag1) or !empty($flag2)) {
				update_option('ecp_options', $options);
				$updated = true;
			}
		}
	}
    // 从数据库中取出，供设置页面显示
    $ecp_options = get_option('ecp_options', true);
	require 'options-page.php';
}

// 钩子函数: 添加BOS设置菜单
function ecp_admin_menu() {
    add_options_page('Easy Copy Paste设置', 'Easy Copy Paste设置', 'manage_options', __FILE__,
        'ecp_option_page');
}

/**
 * 钩子函数: 增加设置链接
 *
 * @param array $links
 * @param string $file
 * @return array $links
 */
function ecp_plugin_action_links($links, $file) {
    if ($file == plugin_basename(dirname(__FILE__) . '/easy-copy-paste.php')) {
        $links[] = '<a href="options-general.php?page=' . ECP_BASEFOLDER
                 . '/easy-copy-paste.php">' . __('Settings') . '</a>';
    }
    return $links;
}

/**
 * 钩子函数：将post_content中本站服务器域名外的img上传至服务器并替换url
 *
 * @param Int    $post_id
 * @param Object $post
 *
 */
function ecp_save_post($post_id, $post) {
    // wordpress 全局变量 wpdb类
    global $wpdb;
    // 只有在点击发布/更新时才执行以下动作
    if($post->post_status == 'publish') {
        // 匹配<img>、src，存入$matches数组,
        $p   = '/<img.*[\s]src=[\"|\'](.*)[\"|\'].*>/iU';
        $num = preg_match_all($p, $post->post_content, $matches);

        if ($num) {
            // BOS参数(数组)，用来构造url
            $bos_options   = get_option('bos_options', true);
            // 本地上传路径信息(数组)，用来构造url
            $wp_upload_dir = wp_upload_dir();

            // 脚本执行不限制时间
            set_time_limit(0);

            // 构造curl，配置参数
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // 抓取时如果发生301，302跳转，则进行跳转抓取
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            // 最多跳转20次
            curl_setopt($ch, CURLOPT_MAXREDIRS,20);
            // 发起连接前最长等待时间
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

			$ecp_options = get_option('ecp_options', true);
			// $host = 'new.immachina.com';
			// $host = 'wpyhd.com';

            foreach ($matches[1] as $src) {
                if (isset($src) && strpos($src, $ecp_options['host']) === false) {
					// 如果图片域名不是immachina

                    // 检查src中的url有无扩展名，没有则重新给定文件名
					// 注意：如果url中有扩展名但格式为webp，那么返回的file_info数组为 ['ext' =>'','type' =>'']
                    $file_info = wp_check_filetype(basename($src), null);
                    if ($file_info['ext'] == false) {
						// 无扩展名和webp格式的图片会被作为无扩展名文件处理
                        date_default_timezone_set('PRC');
                        $file_name = date('YmdHis-').dechex(mt_rand(100000, 999999)).'.tmp';
                    } else {
						// 有扩展名的图片重新给定文件名防止与本地文件名冲突
                        $file_name = dechex(mt_rand(100000, 999999)) . '-' . basename($src);
                    }

                    // 抓取图片, 将图片写入本地文件
                    curl_setopt($ch, CURLOPT_URL, $src);
                    $file_path = $wp_upload_dir['path'] . '/' . $file_name;
                    $img       = fopen($file_path, 'wb');
                    // curl写入$img
                    curl_setopt($ch, CURLOPT_FILE, $img);
                    $img_data  = curl_exec($ch);
                    fclose($img);

                    if (file_exists($file_path) && filesize($file_path) > 0) {
						// 将扩展名为tmp和webp的图片转换为jpeg文件并重命名
						$t   = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
						$arr = explode('/', $t);
						// 对url地址中没有扩展名或扩展名为webp的图片进行处理
						if (pathinfo($file_path, PATHINFO_EXTENSION) == 'tmp') {
							$file_path = ecp_handle_ext($file_path, $arr[1], $wp_upload_dir['path'], $file_name, 'tmp');
						} elseif (pathinfo($file_path, PATHINFO_EXTENSION) == 'webp') {
							$file_path = ecp_handle_ext($file_path, $arr[1], $wp_upload_dir['path'], $file_name, 'webp');
						}

	                    // 替换文章内容中的src
	                    $post->post_content  = str_replace($src, $wp_upload_dir['url'] . '/' . basename($file_path), $post->post_content);
	                    // 构造附件post参数并插入媒体库(作为一个post插入到数据库)
						$attachment = ecp_get_attachment_post(basename($file_path), $wp_upload_dir['url'] . '/' . basename($file_path));
	                    // 生成并更新图片的metadata信息
	                    $attach_id   = wp_insert_attachment($attachment, ltrim($wp_upload_dir['subdir'] . '/' . basename($file_path), '/'), 0);
	                    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
						// 直接调用wordpress函数，将metadata信息写入数据库
	                    $ss = wp_update_attachment_metadata($attach_id, $attach_data);
                    }
                }
            }
            curl_close($ch);

            // 更新posts数据表的post_content字段
            $wpdb->update( $wpdb->posts, array('post_content' => $post->post_content), array('ID' => $post->ID));
        }
    }
}

/**
 * 处理没有扩展名的图片:转换格式或更改扩展名
 *
 * @param string $file 图片本地绝对路径
 * @param string $type 图片mimetype
 * @param string $file_dir 图片在本地的文件夹
 * @param string $file_name 图片名称
 * @param string $ext 图片扩展名
 * @return string 处理后的本地图片绝对路径
 */
function ecp_handle_ext($file, $type, $file_dir, $file_name, $ext) {
	switch ($ext) {
		case 'tmp':
			if (rename($file, str_replace('tmp', $type, $file))) {
				if ('webp' == $type) {
					// 将webp格式的图片转换为jpeg格式
					return ecp_image_convert('webp', 'jpeg', $file_dir . '/' . str_replace('tmp', $type, $file_name));
				}
				return $file_dir . '/' . str_replace('tmp', $type, $file_name);
			}
		case 'webp':
			if ('webp' == $type) {
				// 将webp格式的图片转换为jpeg格式
				return ecp_image_convert('webp', 'jpeg', $file);
			} else {
				if (rename($file, str_replace('webp', $type, $file))) {
					return $file_dir . '/' . str_replace('webp', $type, $file_name);
				}
			}
		default:
			return $file;
	}
}

/**
 * 图片格式转换，暂只能从webp转换为jpeg
 *
 * @param string $from
 * @param string $to
 * @param string $image 图片本地绝对路径
 * @return string 转换后的图片绝对路径
 */
function ecp_image_convert($from='webp', $to='jpeg', $image) {
	// 加载 WebP 文件
	$im = imagecreatefromwebp($image);
	// 以 100% 的质量转换成 jpeg 格式并将原webp格式文件删除
	if (imagejpeg($im, str_replace('webp', 'jpeg', $image), 100)) {
		try {
			unlink($image);
		} catch (Exception $e) {
			$error_msg = sprintf('Error removing local file %s: %s', $image,
				$e->getMessage());
			error_log($error_msg);
		}
	}
	imagedestroy($im);

	return str_replace('webp', 'jpeg', $image);
}

/**
 * 构造图片post参数
 *
 * @param string $filename
 * @param string $url
 * @return array 图片post参数数组
 */
function ecp_get_attachment_post($filename, $url) {
	$file_info  = wp_check_filetype($filename, null);
	return array(
		'guid'           => $url,
		'post_type'      => 'attachement',
		'post_mime_type' => $file_info['type'],
		'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
		'post_content'   => '',
		'post_status'    => 'inherit'
	);
}

// 在设置下面添加设置菜单
add_action('admin_menu', 'ecp_admin_menu', 120);
// 插件列表中的 启用/编辑/设置 链接设置
add_filter('plugin_action_links', 'ecp_plugin_action_links', 120, 2);
// 钩子, 发布/草稿/预览时触发
add_action('save_post', 'ecp_save_post', 120, 2);
