<?php
/*
Plugin Name: 百度云存储（BOS）插件
Plugin URI:
Description: Baidu BOS Plugin for wordpress
Version:     1.0.6
Author:      Jacky Yang
Author URI:
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset
*/
include 'BaiduBce.phar';

use BaiduBce\BceClientConfigOptions;
use BaiduBce\Util\Time;
use BaiduBce\Util\MimeTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Services\Bos\BosClient;
use BaiduBce\Services\Bos\BosOptions;
use BaiduBCE\Auth\SignOptions;

define('BOS_BASEFOLDER', plugin_basename(dirname(__FILE__)));

// BOS设置页面，将BOS存入数据库
function bos_setting_page() {
    $options = [];
    $settings_updated = false;

    if (isset($_POST['bucket'])) {
        $options['bucket'] = trim(stripslashes($_POST['bucket']));
    }
    if (isset($_POST['ak'])) {
        $options['ak'] = trim(stripslashes($_POST['ak']));
    }
    if (isset($_POST['sk'])) {
        $options['sk'] = trim(stripslashes($_POST['sk']));
    }
    if (isset($_POST['host'])) {
        $options['host'] = trim(stripslashes($_POST['host']));
    }
    if (isset($_POST['path'])) {
        $options['path'] = trim(stripslashes($_POST['path']));
    }
    if (isset($_POST['domain'])) {
        $options['domain'] = trim(stripslashes($_POST['domain']));
    }

    if ($options !== []) {
        // 写入数据库
        update_option('bos_options', $options);
        $settings_updated = true;
    }

    // 从数据库中取出
    $bos_options   = get_option('bos_options', true);
    $bos_bucket    = esc_attr($bos_options['bucket']);
    $bos_ak        = esc_attr($bos_options['ak']);
    $bos_sk        = esc_attr($bos_options['sk']);
    $bos_host      = esc_attr($bos_options['host']);
    $upload_path   = esc_attr($bos_options['path']);
    $bucket_domain = esc_attr($bos_options['domain']);

	require 'options-page.php';
    ?>
<?php }

// 钩子函数: 添加BOS设置菜单
function add_setting_menu() {
    add_options_page('百度云BOS存储设置', '百度BOS设置', 'manage_options', __FILE__,
        'bos_setting_page');
}

/**
 * 获取BosClient对象
 *
 * @return Object $bos_client
 */
function get_bos_client() {
    $bos_options = get_option('bos_options', true);
    $config      = [
                       'credentials' => [
                           'ak' => $bos_options['ak'],
                           'sk' => $bos_options['sk'],
                   ],
                    'endpoint' => $bos_options['host'],
        ];
    $bos_client  = new BosClient($config);

    return $bos_client;
}

/**
 * 将图片上传到BOS并删除本地文件
 *
 * @param  Array   $data _wp_attachment_metadata
 * @param  Int     $post_id
 * @return String $ori_object
 */
function upload_attachement_to_bos($data, $post_id) {
    // 原图上传和删除
    $wp_upload_dir = wp_upload_dir();
    $file_path     = $wp_upload_dir['basedir'] . '/' . $data['file'];

    if (!file_exists($file_path)) {
        error_log('file does not exist');
        return new WP_Error('exception', sprintf('File %s does not exist',
            $file_path));
    }

    $bos_options = get_option('bos_options', true);
    $ori_object  = $bos_options['path'] . '/' . $data['file'];
    $bos_client  = get_bos_client();

    if (file_exists($file_path)) {
        try {
            $bos_client->putObjectFromFile($bos_options['bucket'], $ori_object,
                $file_path);
        } catch (Exception $e) {
            $error_msg = sprintf('Error uploading %s to BOS: %s',$file_path,
                $e->getMessage());
            error_log($error_msg);
        }
    }

    if (file_exists($file_path)) {
        try {
            unlink($file_path);
        } catch (Exception $e) {
            $error_msg = sprintf('Error removing local file %s: %s', $file_path,
                $e->getMessage());
            error_log($error_msg);
        }

    }

    // 缩略图上传和删除
    if (isset($data['sizes']) && count($data['sizes']) > 0) {
        foreach ($data['sizes'] as $key => $thumb_data) {
            $thumb_path   = $wp_upload_dir['basedir'] . '/' . substr($data['file'], 0, 8)
                . $thumb_data['file'];
            $thumb_object = $bos_options['path'] . '/' . substr($data['file'], 0, 8)
                . $thumb_data['file'];

            if (file_exists($thumb_path)) {
                try {
                    $bos_client->putObjectFromFile($bos_options['bucket'],
                        $thumb_object, $thumb_path);
                } catch (Exception $e) {
                    $error_msg = sprintf('Error uploading %s to BOS: %s',$thumb_path,
                        $e->getMessage());
                    error_log($error_msg);
                }
            }

            if (file_exists($thumb_path)) {
                try {
                    unlink($thumb_path);
                } catch (Exception $e) {
                    $error_msg = sprintf('Error removing local file %s: %s', $thumb_path,
                        $e->getMessage());
                    error_log($error_msg);
                }
            }
        }
    }

    return $ori_object;
}

// 钩子函数: 调用上传函数并将上传的原图在bucket下的路径信息保存到数据库
function update_attachment_metadata($data, $post_id) {
    $ori_object_key = upload_attachement_to_bos($data, $post_id);
    // 将原始图片在BOS bucket下的路径信息(object信息)添加到数据库，这个数据别处好像没有用到
    add_post_meta($post_id, 'bos_info', $ori_object_key);

    return $data;
}

/**
 * 钩子函数: 获取附件的url
 *
 * @param string $url 本地图片url
 * @return string $url BOS图片url
 */
function get_attachment_url($url, $post_id) {
    $bos_options   = get_option('bos_options', true);
    $arr           = parse_url($url);
    // $file_name     = basename($url); 中文文件名获取
    $file_name     = preg_replace('/^.+[\\\\\\/]/', '', $url);
    $file_path     = $_SERVER['DOCUMENT_ROOT'] . $arr['path'];

    if (!file_exists($file_path)) {
        $arr2   = explode('/', $arr['path']);
        $n      = count($arr2);
        $object = $bos_options['path'] . '/'. $arr2[$n-3] . '/' .$arr2[$n-2]
                . '/' . $file_name;

        $url = get_object_url($object);
    }

    return $url;
}

/**
 * 钩子函数: 对responsive images srcset重新设置，wp原来的函数是从本地获取的url
 *
 * @param array $sources
 * @return array $sources
 */
function calculate_image_srcset($sources) {
    $bos_options   = get_option('bos_options', true);

    foreach ($sources as $key => &$value) {
        // $file_name = basename($value['url']);中文文件名获取
        $file_name = preg_replace('/^.+[\\\\\\/]/', '', $value['url']);
        $arr       = parse_url($value['url']);
        $file_path = $_SERVER['DOCUMENT_ROOT'] . $arr['path'];

        if (!file_exists($file_path)) {
            $arr2   = explode('/', $arr['path']);
            $n      = count($arr2);
            $object = $bos_options['path'] . '/' . $arr2[$n-3] . '/'
                    . '/' . $arr2[$n-2] . '/' . $file_name;

            $value['url'] = get_object_url($object);
        }
    }

    return $sources;
}

/**
 * 钩子函数: 删除BOS上的附件
 *
 * @param string $file 附件的本地路径
 * @return string $file
 */
function del_attachments_from_bos($file) {
    $arr         = explode('/', $file);
    $n           = count($arr);
    $bos_options = get_option('bos_options', true);
    $object      = $bos_options['path'] . '/' . $arr[$n-3] . '/' . $arr[$n-2]
        . '/' . $arr[$n-1];
    $bos_client  = get_bos_client();
    $url         = get_object_url($object);

    // 检查远程文件是否存在
    $ch = curl_init();
    $timeout = 30;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $contents = curl_exec($ch);

    if (strpos($contents, '404') !== false) {
        // error_log(sprintf('Exception: file %s does not exist', $object));
        return new WP_Error('exception', sprintf('File %s does not exist',
            $object));
    }

    // 删除时会将原图和缩略图都删除，不知道为什么
    try {
        $bos_client->deleteObject($bos_options['bucket'], $object);
    } catch (Exception $e) {
        $error_msg = sprintf('Error removing files %s from BOS: %s', $object, $e->getMessage());
        error_log($error_msg);
    }

    return $file;
}

/**
 * 钩子函数: 增加设置链接
 *
 * @param array $links
 * @param string $file
 * @return array $links
 */
function plugin_action_links($links, $file) {
    if ($file == plugin_basename(dirname(__FILE__) . '/wp-bos.php')) {
        $links[] = '<a href="options-general.php?page=' . BOS_BASEFOLDER
                 . '/wp-bos.php">' . __('Settings') . '</a>';
    }
    return $links;
}

/**
 * 获取BOS上object的url
 * @param string $object
 * @return string $url
 */
function get_object_url($object) {
    $bos_options = get_option('bos_options', true);
    if (isset($bos_options['domain']) && $bos_options['domain'] != '') {
        $url = $bos_options['domain'] . '/' . $object;
    } else {
        $bos_client  = get_bos_client();
        $signOptions = [
            SignOptions::TIMESTAMP=>new \DateTime(),
            SignOptions::EXPIRATION_IN_SECONDS=>300,
        ];

        $url = $bos_client->generatePreSignedUrl($bos_options['bucket'], $object,
            [BosOptions::SIGN_OPTIONS => $signOptions]);

        $arr = explode('?', $url);

        $url = $arr[0];

    }

    return $url;
}

/**
 * 钩子函数：将post_content中BOS外的img上传至BOS并替换url
 *
 * @param int $post_id
 * @param object $post
 *
 */
function bos_save_post($post_id, $post) {
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

            foreach ($matches[1] as $src) {
                $host = parse_url($bos_options['host'], PHP_URL_HOST);
                $domain = parse_url($bos_options['domain'], PHP_URL_HOST);
                if (isset($src) && strpos($src, $host) === false
                   && strpos($src, $domain) === false) {
					// 如果图片域名不是百度云
                    $host_pos = strpos($src, $bos_options['host']);
                    $domain_pos = strpos($src, $bos_options['host']);

                    // 检查src中的url有无扩展名，没有则重新给定文件名
					// 注意：如果url中有扩展名但格式为webp，那么返回的file_info数组为 ['ext' =>'','type' =>'']
                    $file_info = wp_check_filetype(basename($src), null);
                    if ($file_info['ext'] == false) {
						// webp格式的图片也会被作为无扩展名文件处理
                        date_default_timezone_set('PRC');
                        $file_name = date('YmdHis-').dechex(mt_rand(100000, 999999)).'.tmp';
                    } else {
						// 重新给文件名防止与本地文件名冲突
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
							$file_path = handle_ext($file_path, $arr[1], $wp_upload_dir['path'], $file_name, 'tmp');
						} elseif (pathinfo($file_path, PATHINFO_EXTENSION) == 'webp') {
							$file_path = handle_ext($file_path, $arr[1], $wp_upload_dir['path'], $file_name, 'webp');
						}

						// BOS上图片的url地址(绑定的CDN加速域名地址)
	                    if (isset($bos_options['domain']) && $bos_options['domain'] != '') {
	                        $url = $bos_options['domain'] . '/' . $bos_options['path'] . $wp_upload_dir['subdir']
	                            . '/' . basename($file_path);
	                    } else {
	                        $url = $bos_options['host'] . '/' . $bos_options['bucket'] . '/' . $bos_options['path']
	                            . $wp_upload_dir['subdir']. '/' . basename($file_path);
	                    }

	                    // 替换文章内容中的src
	                    $post->post_content  = str_replace($src, $url, $post->post_content);
	                    // 构造附件post参数并插入媒体库(作为一个post插入到数据库)
						$attachment = get_attachment_post(basename($file_path), $wp_upload_dir['url'] . '/' . basename($file_path));
	                    // 生成并更新图片的metadata信息
	                    $attach_id   = wp_insert_attachment($attachment, ltrim($wp_upload_dir['subdir']
	                        . '/' . basename($file_path), '/'), 0);
	                    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
	                    // 将metadata信息写入数据库，会调用上传BOS的函数
	                    wp_update_attachment_metadata($attach_id, $attach_data);
                    }
                }
            }
            curl_close($ch);

            // 更新最新的revision的posts表中的post_content字段
            $revisions = wp_get_post_revisions( $post_id );
			krsort( $revisions );
			$newest_id = reset( array_keys( $revisions ) );
			$flag = $wpdb->update( $wpdb->posts, array('post_content' => $post->post_content), array('ID' => $newest_id) );
            // 更新posts数据表的post_content字段
            $wpdb->update( $wpdb->posts, array('post_content' => $post->post_content), array('ID' => $post->ID));
            // 刷新缓存
            wp_cache_flush();
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
function handle_ext($file, $type, $file_dir, $file_name, $ext) {
	switch ($ext) {
		case 'tmp':
			if (rename($file, str_replace('tmp', $type, $file))) {
				if ('webp' == $type) {
					// 将webp格式的图片转换为jpeg格式
					return image_convert('webp', 'jpeg', $file_dir . '/' . str_replace('tmp', $type, $file_name));
				}
				return $file_dir . '/' . str_replace('tmp', $type, $file_name);
			}
		case 'webp':
			if ('webp' == $type) {
				// 将webp格式的图片转换为jpeg格式
				return image_convert('webp', 'jpeg', $file);
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
function image_convert($from='webp', $to='jpeg', $image) {
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
function get_attachment_post($filename, $url) {
	$file_info  = wp_check_filetype($filename, null);
	return [
		'guid'           => $url,
		'post_type'      => 'attachement',
		'post_mime_type' => $file_info['type'],
		'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
		'post_content'   => '',
		'post_status'    => 'inherit'
	];
}

// 在设置下面添加BOS设置菜单
add_action('admin_menu', 'add_setting_menu', 100);
// 插件列表中的 启用/编辑/设置 链接设置
add_filter('plugin_action_links', 'plugin_action_links', 10, 2);
// 更新数据库中的meta时，将BOS上的object信息存到数据库
add_filter('wp_update_attachment_metadata', 'update_attachment_metadata', 10, 2);
// 获取BOS上的图片url
add_filter('wp_get_attachment_url', 'get_attachment_url', 99, 2);
// 增加设置responsive images srcset的钩子，解决bos上的图片在文章页无法显示的问题
add_filter('wp_calculate_image_srcset', 'calculate_image_srcset', 99, 2);
// 增加删除BOS文件的钩子
add_filter('wp_delete_file', 'del_attachments_from_bos', 110, 2);
// 钩子, 发布/草稿/预览时触发
add_action('save_post', 'bos_save_post', 100, 2);
