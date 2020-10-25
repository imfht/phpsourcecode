<!DOCTYPE html>
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php $this->options->charset(); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>
    <?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ' - '); ?>
    <?php $this->options->title(); ?>
    </title>
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link href="/apple-touch-icon-precomposed.png" rel="apple-touch-icon">
    <link rel="stylesheet" href="<?php echo (string)$this->options->CdnUrl().'main.min.css'; ?>" />

    <?php $this->header(); ?>
    <script src="<?php echo (string)$this->options->CdnUrl.'head-scripts.min.js'; ?>" async></script>
    <link rel="canonical" href="<?php $this->permalink(); ?>" />
    <script type="text/javascript">
      window.GHOSTIUM = {};
      GHOSTIUM.haveGA = typeof ga_ua !== 'undefined' && ga_ua !== 'UA-XXXXX-X';
      GHOSTIUM.haveDisqus = typeof disqus_shortname !== 'undefined' && disqus_shortname !== 'example';
      GHOSTIUM.enablePjax = typeof enable_pjax !== 'undefined' ? enable_pjax : true;
    </script>
    </head>
    