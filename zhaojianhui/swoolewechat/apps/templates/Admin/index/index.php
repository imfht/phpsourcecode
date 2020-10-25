<!-- 头部开始部分代码 -->
<?php echo $this->fetch('common/header-start.php');?>
<!-- Toastr style -->
<link href="<?php echo $siteconf['cdnurl']?>/AdminInspinia/css/plugins/toastr/toastr.min.css" rel="stylesheet">

<!-- Gritter -->
<link href="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
<!-- 头部结束部分代码 -->
<?php echo $this->fetch('common/header-end.php');?>
<body>
<div id="wrapper">
    <!-- 主体内容导航栏 -->
    <?php echo $this->fetch('common/main-left-navbar.php');?>
    <div id="page-wrapper" class="gray-bg dashbard-1">
        <!-- 主体顶部导航 -->
        <?php echo $this->fetch('common/main-top-navbar.php');?>
        <!-- 主体内容 -->
        <div class="wrapper wrapper-content">

        </div>
        <!-- 主体页脚 -->
        <?php echo $this->fetch('common/main-footer.php');?>
    </div>
    <!-- 聊天窗口 -->
    <?php echo $this->fetch('common/small-chat-box.php');?>
    <!-- 右侧边栏 -->
    <?php echo $this->fetch('common/right-sidebar.php');?>
</div>
<!-- 文档页脚代码开始 -->
<?php echo $this->fetch('common/footer-start.php');?>
<!-- 文档页脚代码结束 -->
<?php echo $this->fetch('common/footer-end.php');?>