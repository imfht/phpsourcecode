<?php
/**
 * Description: 采集每日简书热门文章并打包成.mobi格式同步到kindle,或指定邮箱
 * Created by PhpStorm.
 * User: falcon
 * Date: 15/10/28
 * Time: 下午12:28
 */
require_once "./vendor/autoload.php";
require_once './config.php';
date_default_timezone_set("Asia/Shanghai");
set_time_limit(0);
ignore_user_abort(true);

define("JS_MAX_PAGE", $config["JS_MAX_PAGE"]); //最大页数:1~5
define("JS_KEEP_IMG", $config['JS_KEEP_IMG']);// 是否需要图片
define("JS_IMG_WIDTH", $config['JS_IMG_WIDTH']); // 简书图片缩放宽度 px
define("KD_SEND_ZIP", $config['KD_SEND_ZIP']); //todo: 是否发送经过zip压缩的电子书


$site_url = 'http://www.jianshu.com';
$curl = new Curl\Curl();
$curl->setHeader('X-Requested-With', 'XMLHttpRequest');
$curl->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:40.0) Gecko/20100101 Firefox/40.0');
$curl->setHeader('Accept-Language', 'zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3');
$curl->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
$curl->setReferrer('http://www.jianshu.com/');
$curl->get($site_url);
if ($curl->error) {
    echo $curl->error_code;
    exit;
}
$page_content = $curl->response;
$page_headers = $curl->response_headers;


$cookie_str = "";
$cookie_arr = array();
foreach ($page_headers as $header) {
    if (strpos($header, 'Set-Cookie: ') === 0) {
        $cookie_arr[] = substr($header, 12);
    }
}


$cookie_str .= implode($cookie_arr, '; ');
$xpid = "";
if (preg_match('/xpid:"(.*?)"/is', $page_content, $match)) { // 11.3日测试发现简书取消了xpid
    $xpid = $match[1];
}


preg_match('/<meta name="csrf-token" content="(.*?)"/is', $page_content, $match);
$csrf_token = $match[1];


preg_match('/data-url="(.*?)"/is', $page_content, $match);
$data_url = $site_url . $match[1];

$curl->setHeader('X-NewRelic-ID', $xpid);
$curl->setHeader('X-CSRF-Token', $csrf_token);
$curl->setHeader('Cookie', $cookie_str);
$curl->setHeader('Accept', 'text/javascript, application/javascript, application/ecmascript, application/x-ecmascript, */*; q=0.
01');


$data_url = 'http://www.jianshu.com/top/daily';
$all_html = "";
for ($page = 0; $page < JS_MAX_PAGE; $page++) {
    $current_page = $page + 1;
    $curl->get($data_url, array('page' => $current_page, '_' => time()));
    $html = str_replace(array("\\n", "\\"), "", $curl->response);
    if (preg_match('/data-url="(.*?)"/is', $html, $match)) {
        $data_url = $site_url . $match[1];
        $all_html .= $html;
    } else {
        break; // 如果没有加载下一页的url,退出,简书当前load more最多5页
    }
}


preg_match_all('#<h4 class="title">(.*?)</h4>#is', $all_html, $matches);
$items = $matches[1];


$article_arr = array();
$to_down_img_arr = array();
foreach ($items as $k => $item) {
    $article = array();
    preg_match('#href="(.*)"#', $item, $match);
    $article['url'] = $site_url . $match[1];
    $article['title'] = strip_tags($item);
    $curl->get($article['url']);
    $html = $curl->response;
    preg_match("#<script type='application/json' data-name='author'>(.*?)</script>#is", $html, $match);
    $usr_info = json_decode(trim($match[1]));
    $article['author'] = $usr_info->nickname;
    preg_match('#<div class="show-content">(.*)<div class="visitor_edit">#iUs', $html, $match);
    $temp_content = $match[1];
    $arr = explode("\n", $temp_content);

    $article['content'] = "<div class=\"show-content\">" . implode("\n", array_slice($arr, 0, count($arr) - 4));
    preg_match_all('#<img src="(.*?)"#is', $article['content'], $matches);
    $img_urls = $matches[1];
//    array_walk()
    $args = array();
    foreach ($img_urls as $img_url) {
        $args[] = '/images/jianshu/';
    }
    if (JS_KEEP_IMG) {
        $article['content'] = str_replace($img_urls, array_map('get_image_filename', $img_urls, $args), $article['content']);
        $article['content'] = str_replace('<img', '<img style="margin:0 auto;display:block;height:300px"', $article['content']);
        $to_down_img_arr = array_merge($to_down_img_arr, $img_urls);

    } else {
        $article['content'] = preg_replace("#<img.*>#iUs", "", $article['content']); //无图
    }

    $article_arr[] = $article;


}
foreach ($to_down_img_arr as $img_url) {
    download($img_url, __DIR__ . "/images/jianshu/");
}

$phindle = new Develpr\Phindle\Phindle(
    array(
        "title" => "每日简书" . date('md'),
        "publisher" => "FalconPress",
        "creator" => "Falcon",
        "language" => Develpr\Phindle\OpfRenderer::LANGUAGE_ZH,
        "subject" => "简书",
        "description" => "a new book",
        "path" => __DIR__ . "/ebooks/",
        "isdn" => "000000000",
        "staticResourcePath" => __DIR__,
        "cover" => './images/cover.jpg',
        "kindlegenPath" => $config['KINDLEGEN_PATH'],
        "downloadimage" => true
    )

);


foreach ($article_arr as $i => $article) {

    if ($article['content'] == "") continue;
    $content = new Develpr\Phindle\Content();
    $content->setTitle($article['title']);
    $content->setHtml('<meta http-equiv="Content-Type" content="text/html;charset=utf-8">' . "<h3>{$article['title']}</h3>" . $article['content']);
    $content->setPosition($i);
    $phindle->addContent($content);
}

$phindle->process();
$ebook_path = $phindle->getMobiPath();

$mail = new Nette\Mail\Message();
$mail->setFrom(sprintf('%s <%s>', $config['KD_SENDER']['from'], $config['KD_SENDER']['username']))
    ->setSubject($phindle->getAttribute('title'))
    ->addAttachment($ebook_path, null, 'application/octet-stream');

foreach ($config['KD_RECEIVER'] as $receiver) {
    $mail->addTo($receiver);
}

$mailer = new Nette\Mail\SmtpMailer($config['KD_SENDER']);
$result = $mailer->send($mail);


//获取图片本地存储路径
function get_image_filename($url, $local_prefix = "")
{
    $arr = parse_url($url);
    $basename = basename($arr['path']);
    if (strpos($basename, '.') === false) {
        return $local_prefix . $basename . '.jpg';
    } else {
        return $local_prefix . $basename;
    }
}

//下载图片
function download($url, $store_dir)
{
    $filename = get_image_filename($url, $store_dir);
    if (file_exists($filename)) return; //存在时不下载
    $curl = new Curl\Curl();
    $curl->setHeader('X-Requested-With', 'XMLHttpRequest');
    $curl->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:40.0) Gecko/20100101 Firefox/40.0');
    $curl->setHeader('Accept-Language', 'zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3');
    $curl->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
    @mkdir($store_dir, 0755, true);
    $curl->get($url);
    $data = $curl->response;
    file_put_contents($filename, $data);
    try {
        $image = new \Eventviva\ImageResize($filename);
        $image->resizeToWidth(JS_IMG_WIDTH);
        $image->save($filename);
    } catch (Exception $e) {

    }
    return $filename;
}
