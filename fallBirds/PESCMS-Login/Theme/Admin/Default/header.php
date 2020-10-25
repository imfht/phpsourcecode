<!doctype html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?= $sitetile ?></title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="renderer" content="webkit">
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <link rel="icon" type="image/png" href="/favicon.ico">
        <link rel="apple-touch-icon-precomposed" href="<?= DOCUMENT_ROOT ?>/Theme/assets/i/app-icon72x72@2x.png">
        <meta name="apple-mobile-web-app-title" content="Amaze UI" />

        <script src="<?= DOCUMENT_ROOT ?>/Theme/assets/ueditor/ueditor.config.js"></script>
        <script src="<?= DOCUMENT_ROOT ?>/Theme/assets/ueditor/ueditor.all.js"></script>
        <script src="<?= DOCUMENT_ROOT ?>/Theme/assets/ueditor/lang/zh-cn/zh-cn.js"></script>
        
        <script src="<?= DOCUMENT_ROOT ?>/Theme/assets/js/jquery.min.js"></script>
        <script src="<?= DOCUMENT_ROOT ?>/Theme/assets/js/webuploader.js"></script>
        <script src="<?= DOCUMENT_ROOT ?>/Theme/assets/js/dialog-min.js"></script>
        <script src="<?= DOCUMENT_ROOT ?>/Theme/assets/js/dialog-plus-min.js"></script>
    </head>
    <body <?= MODULE == 'Index' && ACTION == 'index' ? 'class="am-with-fixed-header am-nbfc"' : '' ?>>