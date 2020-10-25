<!DOCTYPE html>
<html lang="zh-CN" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title><?= $site_title ?></title>
    <link rel="pingback" href="/pingback/">
    <link rel="stylesheet" href="<?= $asset_url ?>/css/style.css" type="text/css" media="all"/>
    <link rel="stylesheet" href="<?= $asset_url ?>/css/prettyprint.css" type="text/css" media="all"/>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?= $asset_url ?>/js/html5.js"></script>
    <![endif]-->
</head>
<body class="home blog">

<div id="page" class="hfeed site">
    <a class="skip-link screen-reader-text" href="#content">跳至内容</a>

    <div id="sidebar" class="sidebar">

        <header id="masthead" class="site-header" role="banner">
            <div class="site-branding">
                <h1 class="site-title"><a href="/" rel="home"><?= $site_title ?></a></h1>
            </div>
            <!-- .site-branding -->
        </header>
        <!-- .site-header -->

        <div id="secondary" class="secondary">
            <div id="widget-area" class="widget-area" role="complementary">
                <aside class="widget widget_recent_entries">
                    <?= $sidebar . "\n" ?>
                </aside>
            </div>
            <!-- .widget-area -->
        </div>
        <!-- .secondary -->

    </div>
    <!-- .sidebar -->

    <div id="content" class="site-content">

        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
                <article class="post hentry">
                    <?= $content . "\n" ?>
                </article>
            </main>
            <!-- .site-main -->
        </div>
        <!-- .content-area -->

    </div>
    <!-- .site-content -->

    <footer id="colophon" class="site-footer" role="contentinfo">
        <div class="site-info">
            <?= _('Powered by ') . $site_title ?>
        </div>
        <!-- .site-info -->
    </footer>
    <!-- .site-footer -->

</div>
<!-- .site -->

<script type="text/javascript" src="<?= $asset_url ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?= $asset_url ?>/js/prettyprint.js"></script>
<script type="text/javascript">
    $(function () {
        $('#apilist').find('a').click(function () {
            $('#apilist').find('a.current').removeClass('current');
            var url = $(this).addClass('current').attr('title');
            $.getJSON(url, function (result) {
                $('#url').html('GET <?= $site_domain ?>' + url);
                $('#result').html(library.json.prettyPrint(result));
            });
        });
    });
</script>
</body>
</html>
