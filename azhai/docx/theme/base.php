<!DOCTYPE html>
<!--[if lt IE 7]>       <html class="no-js ie6 oldie" lang="zh"> <![endif]-->
<!--[if IE 7]>          <html class="no-js ie7 oldie" lang="zh"> <![endif]-->
<!--[if IE 8]>          <html class="no-js ie8 oldie" lang="zh"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="zh">
<!--<![endif]-->
<head>
<title><?=$page['title'] . ' - ' . $options['title']?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="<?=$options['tagline'];?>" />
<meta name="author" content="<?=$options['author']?>">
<link rel="icon" href="<?=$assets_url?>/img/favicon.png" type="image/x-icon">
<link rel="stylesheet" href="<?=$assets_url?>/css/style.min.css">
<style rel="stylesheet">h1 a.html-hidden, div a.html-hidden<?=$html_hide?></style>
<!-- Mobile -->
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<!-- Docs -->
<?php if ($options['github_repo_name']): ?>
<a href="https://github.com/<?=$options['github_repo_name']?>" target="_blank" id="github-ribbon" class="hidden-print">
    <img src="<?=$assets_url?>/img/forkme_right_darkblue_121621.png" alt="Fork me on GitHub">
</a>
<?php endif; ?>
<div class="navbar navbar-fixed-top hidden-print">
    <div class="container-fluid">
        <a class="brand navbar-brand pull-left" href="<?=$urlpre . 'index' . $urlext?>"><?=$options['title']?></a>
        <p class="navbar-text pull-right"> <?php echo hp_rand_greeting($options['greetings']); ?> </p>
    </div>
</div>

<div class="container-fluid fluid-height wrapper">
    <div class="row columns content">
    
        <div class="left-column article-tree col-sm-3 hidden-print">
        <?php $this->includeTpl($theme_dir . '/navi.php'); ?>
        </div>

        <div class="right-column content-area col-sm-9">
        <?=$this->block('content')?>
        </div>

    </div>
</div>

<?php if ($options['google_analytics']): ?>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', '<?=$options['google_analytics'];?>', '<?php echo (isset($_SERVER['HTTP_HOST']))?$_SERVER['HTTP_HOST']:''; ?>');
    ga('send', 'pageview');

</script>
<?php endif; ?>

<script src="<?=$assets_url?>/js/jquery.min.js"></script> 
<script src="<?=$assets_url?>/js/custom.js"></script> 
<!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<?=$this->block('scripts')?>

</body>
</html>
