#!/usr/bin/env php

<?php
/**
 * @author dogstar 20150408
 */

if ($argc < 3) {
    echo "Usage: $argv[0] <md_file> <save_folder> \n\n";
    exit(1);
}

$file = trim($argv[1]);
if (!file_exists($file)) {
    echo "Miss $file !\n\n";
    exit(2);
}

$md_type = isset($argv[3]) ? $argv[3]: 'wiki'; // wiki/book/xiaobai

require_once dirname(__FILE__) . '/Parsedown.php';

$Parsedown = new Parsedown();

$rs = $Parsedown->text(file_get_contents($file));

// 样式调整
$rs = str_replace('<table>', '<table class="table table-bordered">', $rs);

if ($md_type == 'wiki') {
$rs .= '<div style="float: left">
<h4>
<a href="http://qa.phalapi.net/">还有疑问？欢迎到社区提问！</a>
&nbsp;&nbsp;
<a href="http://docs.phalapi.net/#/v2.0/">切换到PhalApi 2.x 开发文档。</a>
</h4>
</div>';
}

$folder = trim($argv[2]);
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$info = pathinfo($file);

//$title_maps = array(
//    'wiki' => '官方文档',
//    'book' => '初识PhalApi',
//    'xiaobai' => '小白接口',
//    'wiki2' => '官方文档2.x',
//);

// SEO优化

$header = '';
$sub_nav = '';
$footer = '';

$_ds_id = md5($info['filename']);
$_ds_title = $info['filename'];
$_ds_url = 'http://www.phalapi.net/wikis/' . urlencode($info['filename']) . '.html';

$content = sprintf(
    "%s
    <div id=\"content\">
        <div class=\"container\">

            %s

            <div class=\"row row-md-flex row-md-flex-wrap\">
                %s
            </div>
        </div>
    </div>

 %s",
    $header, $sub_nav, $rs , $footer 
);

file_put_contents($folder . '/' . $info['filename'] . '.html', $content);

