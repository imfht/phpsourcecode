<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/17 15:30
// +----------------------------------------------------------------------
// | TITLE: this to do?
// +----------------------------------------------------------------------
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>YII-ADMIN</title>

    <meta name="description" content="Static &amp; Dynamic Tables" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/bootstrap.css" />
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/font-awesome.css" />

    <!-- page specific plugin styles -->

    <!-- text fonts -->
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/ace-fonts.css" />

    <!-- ace styles -->
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/ace.css" class="ace-main-stylesheet" id="main-ace-style" />

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/ace-part2.css" class="ace-main-stylesheet" />
    <![endif]-->

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/ace-ie.css" />
    <![endif]-->

    <!-- inline styles related to this page -->

    <!-- ace settings handler -->
    <script src="<?= Url::base() ?>/aceAdmin/assets/js/ace-extra.js"></script>

    <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

    <!--[if lte IE 8]>
    <script src="<?= Url::base() ?>/aceAdmin/assets/js/html5shiv.js"></script>
    <script src="<?= Url::base() ?>/aceAdmin/assets/js/respond.js"></script>
    <![endif]-->
    <!-- 自定义CSS -->
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/style.css"/>

    <?php if (isset($this->blocks['head'])): ?>
        <?= $this->blocks['head'] ?>
    <?php else: ?>

    <?php endif; ?>

</head>

<body class="no-skin">
<script src="<?= Url::base() ?>/aceAdmin/assets/js/jquery-2.0.3.min.js"></script>

<?= $content ?>
<!-- basic scripts -->

<!--[if !IE]> -->
<script type="text/javascript">
    window.jQuery || document.write("<script src='<?= Url::base() ?>/aceAdmin/assets/js/jquery.js'>"+"<"+"/script>");
</script>

<!-- <![endif]-->

<!--[if IE]>
<script type="text/javascript">
    window.jQuery || document.write("<script src='<?= Url::base() ?>/aceAdmin/assets/js/jquery1x.js'>"+"<"+"/script>");
</script>
<![endif]-->
<script type="text/javascript">
    if('ontouchstart' in document.documentElement) document.write("<script src='<?= Url::base() ?>/aceAdmin/assets/js/jquery.mobile.custom.js'>"+"<"+"/script>");
</script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/bootstrap.js"></script>

<!-- inline scripts related to this page -->

<!-- page specific plugin scripts -->
<script src="<?= Url::base() ?>/aceAdmin/assets/js/jquery.dataTables.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/jquery.dataTables.bootstrap.js"></script>

<!-- ace scripts -->
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/elements.scroller.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/elements.colorpicker.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/elements.fileinput.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/elements.typeahead.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/elements.wysiwyg.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/elements.spinner.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/elements.treeview.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/elements.wizard.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/elements.aside.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/ace.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/ace.ajax-content.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/ace.touch-drag.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/ace.sidebar.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/ace.sidebar-scroll-1.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/ace.submenu-hover.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/ace.widget-box.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/ace.settings.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/ace.settings-rtl.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/ace.settings-skin.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/ace.widget-on-reload.js"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/ace/ace.searchbox-autocomplete.js"></script>
<script src="<?= Url::base() ?>/layer/layer.js"></script>
<?php if (isset($this->blocks['footer'])): ?>
    <?= $this->blocks['footer'] ?>
<?php else: ?>

<?php endif; ?>
</body>
</html>

