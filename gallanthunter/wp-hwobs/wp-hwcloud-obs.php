<?php
/*
Plugin Name: 华为云对象存储服务OBS
Plugin URI: http://jungedushu.com
Description: 使用华为云对象存储服务OBS作为附件存储空间
Version: 1.0.0
Author: gallanthunter
Author URI: http://jungedushu.com
License: GPL v3

{Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

{Plugin Name} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {License URI}.
*/
// 引入依赖库
require 'sdk/obs-autoloader.php';
require 'sdk/vendor/autoload.php';
require_once(ABSPATH . 'wp-admin/includes/image.php');

// 声明命名空间
use Obs\ObsClient;
use Obs\ObsException;

if (!defined('WP_PLUGIN_URL')) {
    define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
}
define('OBS_BASENAME', plugin_basename(__FILE__));
define('OBS_BASEFOLDER', plugin_basename(dirname(__FILE__)));
// 初始化参数
register_activation_hook(__FILE__, 'obs_set_options');
function obs_set_options()
{
    $options = array(
        'bucket'              => "",
        'access_key'          => "",
        'secret_key'          => "",
        'endpoint'            => "",
        'domain'              => "",
        'is_upload_thumb'     => "true",
        'is_save_media_local' => "true",
    );
    add_option('obs_options', $options, '', 'yes');
}

// 创建ObsClient实例
function init_obs_client()
{
    $obs_options = get_option('obs_options', true);
    $obClient    = ObsClient::factory([
        'key'      => $obs_options['access_key'],
        'secret'   => $obs_options['secret_key'],
        'endpoint' => $obs_options['endpoint'],
    ]);
    return $obClient;
}

/**
 * 将图片上传到OBS
 *
 * @param Array $metadata _wp_attachment_metadata
 * @return String $object
 */
function obs_file_upload($metadata)
{
    $obsClient     = init_obs_client();
    $obs_options   = get_option('obs_options', true);
    $obs_bucket    = $obs_options['bucket'];
    $wp_upload_dir = wp_upload_dir();
    $file          = $wp_upload_dir['basedir'] . '/' . $metadata['file'];
    $object        = $metadata['file'];
    error_log(sprintf('%s - %s: metadata[\'file\'] = %s', __FUNCTION__, __LINE__, $metadata['file']));
    //如果文件不存在，直接返回FALSE
    if (!file_exists($file)) {
        error_log(sprintf('%s - %s: File %s does not exist', __FUNCTION__, __LINE__, $file));
        return new WP_Error('exception', sprintf('%s - %s: File %s does not exist', __FUNCTION__, __LINE__, $file));
    }
    if (file_exists($file)) {
        try {
            $obsClient->putObject([
                'Bucket'     => $obs_bucket,
                'Key'        => $object,
                'SourceFile' => $file
            ]);
            
        } catch (ObsException $e) {
            $error_msg = sprintf('%s - %s: Error uploading %s to OBS: %s', __FUNCTION__, __LINE__, $file, $e->getMessage());
            error_log($error_msg);
        }
        if (is_upload_thumb()) {
            obs_thumb_upload($metadata);
        }
        if (!is_save_media_local()) {
            del_local_file($file);
            del_local_thumb($metadata);
        }
    }
    return $object;
}

// 钩子函数: 调用上传函数并将上传的原图在bucket下的路径信息保存到数据库
function update_attachment_metadata($metadata, $post_id)
{
    if (!$metadata['file']) {
        return;
    }
    $object = obs_file_upload($metadata);
    // 将原始图片在OBS bucket下的路径信息(object信息)添加到数据库
    add_post_meta($post_id, 'obs_info', $object);
    return $metadata;
}

//避免上传插件/主题时出现同步到OBS的情况
// if (substr_count($_SERVER['REQUEST_URI'], '/update.php') <= 0) {
//     add_filter('wp_handle_upload', 'update_attachment_metadata', 50, 2);
// }
/**
 * 将缩略图上传到OBS
 *
 * @param Array $metadata _wp_attachment_metadata
 * @return String $object
 */
function obs_thumb_upload($metadata)
{
    $obsClient     = init_obs_client();
    $obs_options   = get_option('obs_options', true);
    $wp_upload_dir = wp_upload_dir();
    if (isset($metadata['sizes']) && count($metadata['sizes']) > 0) {
        foreach ($metadata['sizes'] as $key => $thumb_data) {
            $thumb_path   = $wp_upload_dir['basedir'] . '/' . substr($metadata['file'], 0, 8) . $thumb_data['file'];
            $thumb_object = substr($metadata['file'], 0, 8) . $thumb_data['file'];
            if (file_exists($thumb_path)) {
                try {
                    $obsClient->putObject([
                        'Bucket'     => $obs_options['bucket'],
                        'Key'        => $thumb_object,
                        'SourceFile' => $thumb_path
                    ]);
                } catch (Exception $e) {
                    $error_msg = sprintf('Error uploading %s to BOS: %s', $thumb_path, $e->getMessage());
                    error_log($error_msg);
                }
            }
        }
    }
}

//避免上传插件/主题时出现同步到OBS的情况
// if (substr_count($_SERVER['REQUEST_URI'], '/update.php') <= 0) {
//     add_filter('wp_generate_attachment_metadata', 'obs_thumb_upload', 100);
// }
/**
 * 删除本地缩略图
 *
 * @param Array $metadata _wp_attachment_metadata
 * @return String $object
 */
function del_local_thumb($metadata)
{
    $wp_upload_dir = wp_upload_dir();
    if (isset($metadata['sizes']) && count($metadata['sizes']) > 0) {
        foreach ($metadata['sizes'] as $key => $thumb_data) {
            $thumb_path = $wp_upload_dir['basedir'] . '/' . substr($metadata['file'], 0, 8) . $thumb_data['file'];
            if (file_exists($thumb_path)) {
                try {
                    unlink($thumb_path);
                } catch (Exception $e) {
                    $error_msg = sprintf('Error removing local file %s: %s', $thumb_path, $e->getMessage());
                    error_log($error_msg);
                }
            }
        }
    }
}

/**
 * 是否需要上传缩略图
 * @return bool
 */
function is_upload_thumb()
{
    $obs_options = get_option('obs_options', true);
    return (esc_attr($obs_options['is_upload_thumb']) == 'true');
}

/**
 * 是否需要删除本地文件
 * @return bool
 */
function is_save_media_local()
{
    $obs_options = get_option('obs_options', true);
    return (esc_attr($obs_options['is_save_media_local']) == 'true');
}

/**
 * 删除本地文件
 * @param $file 本地文件路径
 * @return bool
 */
function del_local_file($file)
{
    try {
        //文件不存在
        if (!file_exists($file)) {
            return true;
        }
        //删除文件
        if (!@unlink($file)) {
            return false;
        }
        return true;
    } catch (Exception $e) {
        $error_msg = sprintf('Error removing local file %s: %s', $file, $e->getMessage());
        error_log($error_msg);
    }
}

/**
 * 钩子函数: 删除OBS上的附件
 *
 * @param string $file 附件的本地路径
 * @return string $file
 */
function obs_del_file($file)
{
    $arr         = explode('/', $file);
    $n           = count($arr);
    $obs_options = get_option('obs_options', true);
    $object      = $arr[$n - 3] . '/' . $arr[$n - 2] . '/' . $arr[$n - 1];
    $obsClient   = init_obs_client();
    try {
        $obsClient->deleteObject([
            'Bucket' => $obs_options['bucket'],
            'Key'    => $object
        ]);
    } catch (Exception $e) {
        $error_msg = sprintf('%s - %s: Error removing files %s from OBS: %s', __FUNCTION__, __LINE__, $object, $e->getMessage());
        error_log($error_msg);
    }
    return $file;
}

/**
 * 钩子函数: 获取附件的url
 *
 * @param string $url 本地图片url
 * @return string $url OBS图片url
 */
function get_file_url($url, $post_id)
{
    $obs_options = get_option('obs_options', true);
    $arr         = parse_url($url);
    $file_name   = preg_replace('/^.+[\\\\\\/]/', '', $url);
    $file_path   = $_SERVER['DOCUMENT_ROOT'] . $arr['path'];
    if (!file_exists($file_path)) {
        $arr2   = explode('/', $arr['path']);
        $n      = count($arr2);
        $object = $arr2[$n - 3] . '/' . $arr2[$n - 2] . '/' . $file_name;
        $url    = obs_get_object_url($object);
    }
    return $url;
}

/**
 * 获取OBS上object的url
 * @param string $object
 * @return string $url
 */
function obs_get_object_url($object)
{
    $obs_options = get_option('obs_options', true);
    if (isset($obs_options['domain']) && $obs_options['domain'] != '') {
        $url = $obs_options['domain'] . '/' . $object;
    } else {
        $url = $obs_options['endpoint'] . '/' . $object;
    }
    return $url;
}

/**
 * 钩子函数: 对responsive images srcset重新设置，wp原来的函数是从本地获取的url
 *
 * @param array $sources
 * @return array $sources
 */
function calculate_image_srcset($sources)
{
    $obs_options = get_option('obs_options', true);
    if (is_array($sources)) {
        foreach ($sources as $key => &$value) {
            // $file_name = basename($value['url']); 获取中文文件名
            $file_name = preg_replace('/^.+[\\\\\\/]/', '', $value['url']);
            $arr       = parse_url($value['url']);
            $file_path = wp_upload_dir()['path'] . $arr['path'];
            if (!file_exists($file_path)) {
                $arr2         = explode('/', $arr['path']);
                $n            = count($arr2);
                $object       = $arr2[$n - 3] . '/' . $arr2[$n - 2] . '/' . $file_name;
                $value['url'] = obS_get_object_url($object);
            }
        }
    }
    return $sources;
}

/**
 * 函数功能：执行 cURL 会话，从远端服务器获取图片
 * @param string $src
 * @param resource $img
 */
function curl_get($src, $img)
{
    // 脚本执行不限制时间
    set_time_limit(30);
    $options = array(
        CURLOPT_HEADER         => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        // 抓取时如果发生301，302跳转，则进行跳转抓取
        CURLOPT_FOLLOWLOCATION => true,
        // 最多跳转20次
        CURLOPT_MAXREDIRS      => 20,
        // 发起连接前最长等待时间
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_URL            => $src,
        CURLOPT_FILE           => $img
    );
    $ch      = curl_init();
    curl_setopt_array($ch, $options);
    try {
        curl_exec($ch);
    } catch (Exception $e) {
        $error_msg = sprintf('%s - %s: Error downloading images: %s', __FUNCTION__, __LINE__, $e->getMessage());
        error_log($error_msg);
    }
    $into = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    if (strpos($into, '404') !== false) {
        error_log(sprintf('%s - %s: Exception: file %s does not exist', __FUNCTION__, __LINE__, $src));
        return new WP_Error('exception', sprintf('%s - %s: File %s does not exist', __FUNCTION__, __LINE__, $src));
    }
    curl_close($ch);
    return $into;
}

/**
 * 钩子函数：将post_content中OBS外的img上传至OBS并替换url
 *
 * @param int $post_id
 * @param resource $post
 *
 */
function obs_save_post($post_id, $post)
{
    // wordpress 全局变量 wpdb类
    global $wpdb;
    // 只有在点击发布/更新时才执行以下动作
    if ($post->post_status == 'publish') {
        replace_post_pics_url($post);
        replace_post_data_full_url($post);
        // 更新posts数据表的post_content字段
        $wpdb->update($wpdb->posts, array('post_content' => $post->post_content), array('ID' => $post->ID));
    }
}

/**
 * 功能描述：替换post中图片url
 * @param resource $post
 * @return resource $post
 */
function replace_post_pics_url($post)
{
    // 匹配<img>、src，存入$matches数组,
    $p   = '/<img.*[\s]src=[\"|\'](.*)[\"|\'].*>/iU';
    $num = preg_match_all($p, $post->post_content, $matches);
    if ($num) {
        // OBS参数(数组)，用来构造url
        $obs_options = get_option('obs_options', true);
        // 本地上传路径信息(数组)，用来构造url
        $wp_upload_dir = wp_upload_dir();
        $endpoint      = parse_url($obs_options['endpoint'], PHP_URL_HOST);
        $domain        = parse_url($obs_options['domain'], PHP_URL_HOST);
        foreach ($matches[1] as $src) {
            // 判断文章媒体链接是否是OBS链接或本站链接，如果是，修改图片链接为OBS链接，如果不是，下载图片，并替换文章中链接为OBS链接
            if (isset($src) && strpos($src, $endpoint) === false
                && strpos($src, $domain) === false
                && strpos($src, $wp_upload_dir['baseurl']) === false) {
                // 注意：如果url中有扩展名但格式为webp，那么返回的file_info数组为 ['ext' =>'','type' =>'']
                $file_info = wp_check_filetype(basename($src), null);
                if ($file_info['ext'] == false) {
                    // webp格式的图片也会被作为无扩展名文件处理
                    date_default_timezone_set('PRC');
                    $file_name = date('YmdHis-') . dechex(mt_rand(0, 10000)) . '.tmp';
                } else {
                    // 重新给文件名防止与本地文件名冲突
                    $file_name = date('YmdHis-') . dechex(mt_rand(0, 10000)) . '.' . $file_info['ext'];
                }
                // 抓取图片, 将图片写入本地文件
                $file_path = $wp_upload_dir['path'] . '/' . $file_name;
                $img       = fopen($file_path, 'wb');
                $t         = curl_get($src, $img);
                fclose($img);
                if (file_exists($file_path) && filesize($file_path) > 0) {
                    // 将扩展名为tmp和webp的图片转换为jpeg文件并重命名
                    $arr = explode('/', $t);
                    // 对url地址中没有扩展名或扩展名为webp的图片进行处理
                    if (pathinfo($file_path, PATHINFO_EXTENSION) == 'tmp') {
                        error_log(sprintf("webp 文件"));
                        $file_path = handle_ext($file_path, $arr[1], $wp_upload_dir['path'], $file_name, 'tmp');
                        error_log(sprintf("%s, filepath:%s", __LINE__, $file_path));
                    } elseif (pathinfo($file_path, PATHINFO_EXTENSION) == 'webp') {
                        $file_path = handle_ext($file_path, $arr[1], $wp_upload_dir['path'], $file_name, 'webp');
                    }
                    $post = replace_media_original_url($src, $file_path, $post);
                    // 构造附件post参数并插入媒体库(作为一个post插入到数据库)
                    $attachment = get_attachment_post(basename($file_path), $wp_upload_dir['url'] . '/' . basename($file_path));
                    // 生成并更新图片的metadata信息
                    $attach_id   = wp_insert_attachment($attachment, ltrim($wp_upload_dir['subdir'] . '/' . basename($file_path), '/'), 0);
                    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
                    // 将metadata信息写入数据库，会调用上传OBS的函数
                    wp_update_attachment_metadata($attach_id, $attach_data);
                }
            } else {
                $post = replace_media_original_url($src, null, $post);
            }
        }
    }
    return $post;
}

/**
 * 功能描述：替换post中data-full-url为OBS url
 * @param resource $post
 */
function replace_post_data_full_url($post)
{
    $ori_dfu       = '/data-full-url="(.*\..*)"/iU';
    $num           = preg_match_all($ori_dfu, $post->post_content, $matches);
    $obs_options   = get_option('obs_options', true);
    $wp_upload_dir = wp_upload_dir();
    if ($num) {
        foreach ($matches[1] as $src) {
            if (isset($obs_options['domain']) && $obs_options['domain'] != '') {
                $url = $obs_options['domain'] . $wp_upload_dir['subdir'] . '/' . basename($src);
                error_log(sprintf("%s- url:%s", __LINE__, $url));
            } else {
                $url = $obs_options['bucket'] . '.' . $obs_options['endpoint'] . '/' . $wp_upload_dir['subdir'] . '/' . basename($src);
            }
            $post->post_content = str_replace($src, $url, $post->post_content);
        }
    }
    return $post;
}

/**
 * 功能描述：替换post中data-link为OBS url
 * @param resource $post
 */
function replace_post_data_link($post)
{
    $ori_dl      = '/data-link="(.*\/)"/iU';
    $num         = preg_match_all($ori_dl, $post->post_content, $matches);
    $obs_options = get_option('obs_options', true);
    if ($num) {
        foreach ($matches[1] as $src) {
            if (isset($obs_options['domain']) && $obs_options['domain'] != '') {
                $url = $obs_options['domain'] . parse_url($src, PHP_URL_PATH);
                error_log(sprintf("%s- url:%s", __LINE__, $url));
            } else {
                $url = $obs_options['bucket'] . '.' . $obs_options['endpoint'] . '/' . parse_url($src, PHP_URL_PATH);
            }
            $post->post_content = str_replace($src, $url, $post->post_content);
        }
    }
    return $post;
}

/**
 * 函数功能：使用endpoint或domain替换文章内媒体原始url
 * @param string $ori_url
 * @param string $new_url
 * @param resource $post
 */
function replace_media_original_url($ori_url, $new_url, $post)
{
    $obs_options   = get_option('obs_options', true);
    $wp_upload_dir = wp_upload_dir();
    if ($new_url == null) {
        $new_url = $ori_url;
    }
    // OBS上图片的url地址(绑定的CDN加速域名地址)
    if (isset($obs_options['domain']) && $obs_options['domain'] != '') {
        $url = $obs_options['domain'] . $wp_upload_dir['subdir'] . '/' . basename($new_url);
        error_log(sprintf("%s- url:%s", __LINE__, $url));
    } else {
        $url = $obs_options['bucket'] . '.' . $obs_options['endpoint'] . '/' . $wp_upload_dir['subdir'] . '/' . basename($new_url);
    }
    // 替换文章内容中的src
    $post->post_content = str_replace($ori_url, $url, $post->post_content);
    return $post;
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
function handle_ext($file, $type, $file_dir, $file_name, $ext)
{
    switch ($ext) {
        case 'tmp':
            if (rename($file, str_replace('tmp', $type, $file))) {
                if ('webp' == $type) {
                    // 将webp格式的图片转换为jpeg格式
                    return image_convert('webp', 'png', $file_dir . '/' . str_replace('tmp', $type, $file_name));
                }
                return $file_dir . '/' . str_replace('tmp', $type, $file_name);
            }
            if ('webp' == $type) {
                // 将webp格式的图片转换为jpeg格式
                return image_convert('webp', 'png', $file);
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
 * 图片格式转换，从webp转换为png
 *
 * @param string $from
 * @param string $to
 * @param string $image 图片本地绝对路径
 * @return string 转换后的图片绝对路径
 */
function image_convert($from = 'webp', $to = 'png', $image)
{
    // 加载 WebP 文件
    $im = imagecreatefromwebp($image);
    // 以 100% 的质量转换成 jpeg 格式并将原webp格式文件删除
    if (imagepng($im, str_replace('webp', 'png', $image))) {
        try {
            unlink($image);
        } catch (Exception $e) {
            $error_msg = sprintf('%s - %s: Error removing local file %s: %s', __FUNCTION__, __LINE__, $image, $e->getMessage());
            error_log($error_msg);
        }
    }
    imagedestroy($im);
    return str_replace('webp', 'png', $image);
}

/**
 * 构造图片post参数
 *
 * @param string $filename
 * @param string $url
 * @return array 图片post参数数组
 */
function get_attachment_post($filename, $url)
{
    $file_info = wp_check_filetype($filename, null);
    return [
        'guid'           => $url,
        'post_type'      => 'attachement',
        'post_mime_type' => $file_info['type'],
        'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
        'post_content'   => '',
        'post_status'    => 'inherit'
    ];
}

function obs_setting_page()
{
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient privileges!');
    }
    $options          = array();
    $settings_updated = false;
    if (!empty($_POST) and $_POST['type'] == 'obs_set') {
        $options['bucket']              = (isset($_POST['bucket'])) ? trim(stripslashes($_POST['bucket'])) : '';
        $options['access_key']          = (isset($_POST['access_key'])) ? trim(stripslashes($_POST['access_key'])) : '';
        $options['secret_key']          = (isset($_POST['secret_key'])) ? trim(stripslashes($_POST['secret_key'])) : '';
        $options['endpoint']            = (isset($_POST['endpoint'])) ? trim(stripslashes($_POST['endpoint'])) : '';
        $options['domain']              = (isset($_POST['domain'])) ? trim(stripslashes($_POST['domain'])) : '';
        $options['is_upload_thumb']     = (isset($_POST['is_upload_thumb'])) ? 'true' : 'false';
        $options['is_save_media_local'] = (isset($_POST['is_save_media_local'])) ? 'true' : 'false';
        
    }
    if ($options !== array()) {
        // 更新数据库
        update_option('obs_options', $options);
        $settings_updated = true;
    }
    $obs_options             = get_option('obs_options', true);
    $obs_bucket              = esc_attr($obs_options['bucket']);
    $obs_access_key          = esc_attr($obs_options['access_key']);
    $obs_secret_key          = esc_attr($obs_options['secret_key']);
    $obs_endpoint            = esc_attr($obs_options['endpoint']);
    $obs_domain              = esc_attr($obs_options['domain']);
    $obs_is_upload_thumb     = esc_attr($obs_options['is_upload_thumb']);
    $obs_is_save_media_local = esc_attr($obs_options['is_save_media_local']);
    require 'wp-hwcloud-options.php';
}

// 钩子函数: 添加OBS设置菜单
function add_obs_setting_menu()
{
    add_options_page('华为云OBS设置', '华为云OBS设置', 'manage_options', __FILE__, 'obs_setting_page');
}

/**
 * 钩子函数: 增加设置链接
 *
 * @param array $links
 * @param string $file
 * @return array $links
 */
function plugin_action_links($links, $file)
{
    if ($file == plugin_basename(dirname(__FILE__) . '/wp-hwcloud-obs.php')) {
        $links[] = '<a href="options-general.php?page=' . plugin_basename(dirname(__FILE__)) . '/wp-hwcloud-obs.php">' . __('Settings') . '</a>';
    }
    return $links;
}

// 在设置下面添加OBS设置菜单
add_action('admin_menu', 'add_obs_setting_menu');
// 插件列表中的 启用/编辑/设置 链接设置
add_filter('plugin_action_links', 'plugin_action_links', 10, 2);
// 增加删除OBS文件的钩子
add_filter('wp_delete_file', 'obs_del_file', 110, 2);
// 更新数据库中的meta时，将OBS上的object信息存到数据库
add_filter('wp_update_attachment_metadata', 'update_attachment_metadata', 10, 2);
// 获取OBS上的图片url
add_filter('wp_get_attachment_url', 'get_file_url', 99, 2);
// 增加设置responsive images srcset的钩子，解决obs上的图片在文章页无法显示的问题
add_filter('wp_calculate_image_srcset', 'calculate_image_srcset', 99, 2);
// 钩子, 发布/草稿/预览时触发
add_action('save_post', 'obs_save_post', 100, 2);
?>