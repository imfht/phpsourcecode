<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="assets/favicon.ico">
<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/favicon.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/favicon.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/favicon.png">
<link rel="apple-touch-icon-precomposed" href="assets/favicon.png">
<link rel="shortcut icon" href="assets/favicon.png">

<title>ApiCloud云端管理平台</title>

<link rel="stylesheet" type="text/css" href="assets/css/required/bootstrap/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.min.css" />
<link rel="stylesheet" type="text/css" href="assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.structure.min.css" />
<link rel="stylesheet" type="text/css" href="assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.theme.min.css" />
<link rel="stylesheet" type="text/css" href="assets/fonts/metrize-icons/styles-metrize-icons.css" />
<link rel="stylesheet" type="text/css" href="assets/css/animate.css" />
<!-- Optional CSS Files -->
<!-- add CSS files here -->

<!-- More Required CSS Files -->
<link rel="stylesheet" type="text/css" href="assets/css/styles-core.css" />
<link rel="stylesheet" type="text/css" href="assets/css/styles-core-responsive.css" />

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script type="text/javascript" src="assets/js/required/misc/ie10-viewport-bug-workaround.js"></script>

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="assets/css/required/misc/style-ie7.css" />
<script type="text/javascript" src="assets/fonts/lte-ie7.js"></script>
<![endif]-->
<!--[if IE 8]>
<link rel="stylesheet" type="text/css" href="assets/css/required/misc/style-ie8.css" />
<![endif]-->
<!--[if lte IE 8]>
<script type="text/javascript" src="assets/css/required/misc/excanvas.min.js"></script>
<![endif]-->
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script type="text/javascript" src="assets/html5shiv/3.7.2/html5shiv.min.js"></script>
<script type="text/javascript" src="assets/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<script type="text/javascript" src="assets/js/required/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/js/required/bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/required/jquery.easing.1.3-min.js"></script>

<script type="text/javascript" src="assets/ueditor/ueditor.min.config.js"></script>
<script type="text/javascript" src="assets/ueditor/ueditor.all.min.js"></script>
<link rel="stylesheet" type="text/css" href="assets/ueditor/themes/default/css/ueditor.css" />


</head>
<body>
<style type="text/css">
.jstree-default .jstree-node{
    margin-left: 0;
}    
.table-responsive{
    overflow-x: auto;
}
#left-column{
    background-color: transparent !important;
    background-image: none !important;
}
#header-container .header-bar{
    background-color: transparent;
    background-image: none !important;
}
.navbar-inverse{
    background-color: transparent !important;
}
body{
    background-image: url('assets/main2_bg.jpg');
    /*background-repeat: no-repeat;*/
    background-size: 100%;
}
.left_area .block-content-inner{
    min-height: 500px;
}
#mainnav-horizontal{
    background-color: #fafafa;
}
.page-body{
    /*margin-left: -26px;*/
    /*margin-right: -20px;*/
}
.col{
    /*padding-left:0;*/
    /*padding-right:0;*/
}
.logo{
    width: 156px;
    padding-left: 20px;
}
</style>

<div class="container-fluid">
    <div id="header-container">
        <div class="header-bar navbar navbar-inverse" role="navigation"> 
            <div class="container">
                <div class="navbar-header">
                    
                    <!-- END logo -->

                    <!-- START Mobile Menu Toggle -->
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                   

                    <div class="header-info">
                        <div class="header-profile"> 
                            <ul class="header-profile-menu">
                                <li>
                                    <a href="#" class="top">
                                        <span class="header-profile-menu-icon">
                                            <img class="list-thumbnail" src="assets/avastr.jpg" width="39" height="39" alt="profile-pic-4" />
                                        </span>
                                        <span class="main-menu-text">
                                            管理员
                                            <i class="icon icon-arrow-down-bold-round icon-size-small"></i>
                                        </span>
                                    </a>
                                    <ul>
                                        
                                        <li>
                                            <a href="<?php echo U("Admin/Public/logout");?>">
                                                <span aria-hidden="true" class="icon icon-arrow-curve-right"></span>
                                                <span class="main-text">退出</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="header-notifications"> 
                            <ul class="header-notifications-inner">
                                <li class="notifications-alert-info notifications-alert">
                                    <a data-refresh href="#">
                                        <span class="icon icon-refresh" ></span>
                                    </a>
                                </li>
                                <li class="notifications-alert-info notifications-alert notifications-alert-mobile">
                                    <a href="#">
                                        <span aria-hidden="true" class="icon icon-three-points">1212</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Header Container -->

    <div id="body-container">
        
        <div id="right-column">
            <div class="mainnav-horizontal-outer">
                <div class="menu-text-only" id="mainnav-horizontal-inner">
                    <div id="mainnav-horizontal">
                        <ul class="mainnav-horizontal">
                        <li class="menu-item-top">
                            <a class="top" href="<?php echo U('Index/index');?>">
                                <span class="main-menu-icon">
                                    <span class="icon icon-grid-big icon-size-medium" aria-hidden="true"></span>
                                </span>
                                <span class="main-menu-text">应用列表</span>
                            </a>
                        </li>
                        <?php if(!empty($app)): ?><li class="menu-item-top">                            
                            <a class="top" href="#">
                                <i class="icon icon-arrow-down-bold-round icon-size-small"></i>
                                <span class="main-menu-text">数据云</span>
                            </a>
                            <ul>
                                <li><a data-pjax href="<?php echo U("AcModel/index");?>">模型</a></li>
                                <li><a data-pjax href="<?php echo U("AcFile/index");?>">云文件</a></li>
                            </ul>
                        </li><?php endif; ?>
                        <?php if(is_array($Menus)): $i = 0; $__LIST__ = $Menus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i; if(empty($m[items])): ?><li class="menu-item-top">
                                <a class="top" href="index.php?c=<?php echo (parse_name($m["name"],1)); ?>&a=index">
                                    <span class="main-menu-icon">
                                        <span class="icon icon-grid-big icon-size-medium" aria-hidden="true"></span>
                                    </span>
                                    <span class="main-menu-text"><?php echo ($m["title"]); ?></span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="menu-item-top">                            
                                <a class="top" href="#">
                                    <i class="icon icon-arrow-down-bold-round icon-size-small"></i>
                                    <span class="main-menu-text"><?php echo ($m["title"]); ?></span>
                                </a>
                                <ul>
                                <?php if(is_array($m[items])): $i = 0; $__LIST__ = $m[items];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mm): $mod = ($i % 2 );++$i;?><li><a data-pjax href="<?php echo U($mm[controller].'/index');?>"><?php echo ($mm["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="right-column-content">
                
                <div class="row">
                    <!-- <div class="col-md-6">
                        <h1>
                            <span aria-hidden="true" class="icon icon-documents"></span>
                            <span class="main-text">
                                Blank Page
                            </span>
                        </h1>
                    </div> -->
                    <div class="col-md-6">
                        <!-- START Main Buttons -->
                        <!-- put main page buttons here -->
                        <!-- END Main Buttons -->
                    </div>
                </div>
                <div class="row page-body">
                    <div class="page-container">
                        <div class="page animated bounceInDown">

<div class="col col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block">
                        <div  class="block-heading">
                        <div class="main-text h2">
                        <div class="caption">应用列表</div></div><div id="modelObj-action" class="block-controls"><a data-dialog class="btn btn-primary btn-sm" href="<?php echo U("Index/add");?>">新增</a></div>
                    </div>
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            
<div class="table-responsive">
<?php if(is_array($apps)): $i = 0; $__LIST__ = $apps;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ap): $mod = ($i % 2 );++$i;?><div class="col-sm-6 col-md-6">
        <div class="c-widget c-widget-custom c-widget-facebook c-widget-size-normal">
            <div class="c-widget-icon">
                <span class="icon icon-copy-document "></span>
            </div>
            <div class="c-wdiget-content-block">
                <div class="c-widget-content-heading">
                    <?php echo ($ap["name"]); ?>
                </div>
                <div class="c-widget-content-body">
                    <table class="table">
                        <tr>
                            <td>API_ID</td>
                            <td><?php echo ($ap["app_id"]); ?></td>
                        </tr>
                        <tr>
                            <td>API_KEY</td>
                            <td><?php echo ($ap["app_key"]); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="c-widget-content-sub">
                    <a href="<?php echo U("AcModel/index?code=$ap[code]");?>">进入管理</a>
                    <a data-dialog href="<?php echo U("Index/edit?code=$ap[code]");?>">修改</a>
                    <a data-delete href="<?php echo U("Index/delete?code=$ap[code]");?>">删除</a>
                </div>
            </div>
        </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<div id="modelObj-pager" class="widget-footer"></div>
                        </div>
                        </div>
                    </div>
                </div>


    
</div>

<script type="text/javascript">
$(function(){
    $('[data-delete]').confirmation({
        container: 'body',
        href:false,
        onConfirm:function(event, element){
            event.preventDefault();
            el  = $(element);
            var url = el.attr('href');
            $.get(url,function(req){
                $.bootstrapGrowl(req.info);
                var st = req.status;
                if (req.url == 'reload') {
                    var container = $('.page-container');
                    $.pjax.reload(container, {});
                };
            })
        }
    });
})    
</script>

                    </div>
                </div>
            </div>

            <div id="footer-container">
                <div class="footer-content">
                    &copy; <a href="#">ayhome</a> 技术支持 
                </div>
            </div>
        </div>
    </div>
</div>





<script type="text/javascript" src="assets/js/required/misc/retina.min.js"></script>
<script type="text/javascript" src="assets/js/required/circloid-functions.js"></script>

<link rel="stylesheet" type="text/css" href="assets/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="assets/daterangepicker/daterangepicker-bs3.css" />
<script type="text/javascript" src="assets/daterangepicker/moment.js"></script>
<script type="text/javascript" src="assets/daterangepicker/daterangepicker.js"></script>

<script type="text/javascript" src="assets/nprogress/nprogress.js"></script>
<link rel="stylesheet" type="text/css" href="assets/nprogress/nprogress.css" />
<script type="text/javascript" src="assets/js/bootstrap-confirmation.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-dialog.js"></script>
<script type="text/javascript" src="assets/js/jquery.bootstrap-growl.js"></script>
<link rel="stylesheet" type="text/css" href="assets/jstree/themes/default/style.min.css" />

<link rel="stylesheet" type="text/css" href="assets/fileinput/css/fileinput.min.css" />
<script type="text/javascript" src="assets/fileinput/js/fileinput.min.js"></script>
<script type="text/javascript" src="assets/fileinput/js/fileinput_locale_chs.js"></script>


<script type="text/javascript" src="assets/js/jquery.pjax.js"></script>

<script type="text/javascript" src="assets/jquery-bsgrid/js/grid.all.js"></script>
<script type="text/javascript" src="assets/jquery-bsgrid/js/lang/grid.zh-CN.js"></script>
<link rel="stylesheet" type="text/css" href="assets/jquery-bsgrid/css/grid.css" />

<script type="text/javascript" src="assets/js/apps.js"></script>
<script type="text/javascript" src="assets/js/function.js"></script>

</body>
</html>