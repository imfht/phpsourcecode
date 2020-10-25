<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="assets/images/required/ico/favicon.ico">
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/required/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/required/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/required/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="assets/images/required/ico/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="assets/images/required/ico/favicon.png">

<title>浙江省文化填报系统</title>

<link rel="stylesheet" type="text/css" href="assets/css/required/bootstrap/bootstrap.min.css" />
<link href='http://fonts.useso.com/css?family=Roboto:400,300&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.min.css" />
<link rel="stylesheet" type="text/css" href="assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.structure.min.css" />
<link rel="stylesheet" type="text/css" href="assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.theme.min.css" />
<link rel="stylesheet" type="text/css" href="assets/css/required/mCustomScrollbar/jquery.mCustomScrollbar.min.css" />
<link rel="stylesheet" type="text/css" href="assets/css/required/icheck/all.css" />
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

</head>
<body>
<style type="text/css">
.jstree-default .jstree-node{
    margin-left: 0;
}    
.table-responsive{
    overflow-x: auto;
}
</style>

<div class="container-fluid">
    <div id="header-container">
        <div class="header-bar navbar navbar-inverse" role="navigation"> 
            <div class="container">
                <div class="navbar-header">
                    <div class="logo">
                        <a href="index.html">
                            <img class="default-logo" src="assets/images/required/logo-default.png" width="156" height="44" alt="Logo" />
                            <img class="small-logo" src="assets/images/required/logo-small.png" width="48" height="44" alt="Logo" />
                        </a>
                    </div>
                    <!-- END logo -->

                    <!-- START Mobile Menu Toggle -->
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!-- END Mobile Menu Toggle -->

                    <!-- START Search Bar -->
                    <div class="header-search"> 
                        <form role="form" class="icheck-square" method="post" action="pages-search-results.html">
                            <ul>
                                <li>
                                    <a href="#" class="search-closed">
                                        <span aria-hidden="true" class="icon icon-search"></span>
                                        <span class="main-text">Search</span>
                                    </a>
                                    <a href="#" class="search-opened">
                                        <span aria-hidden="true" class="icon icon-cross"></span>
                                        <span class="main-text">Search</span>
                                    </a>
                                    <ul>
                                        <li class="simple-search">
                                            <div class="simple-search-inner">
                                                <div class="simple-search-block">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="input-search" placeholder="Enter Search Terms...">
                                                        <span class="input-group-btn">
                                                            <button type="submit" class="btn btn-default">
                                                                <span class="main-text">Search</span>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" id="input-advanced-search"> Use Advanced Search
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="advanced-search">
                                            <div class="advanced-search-block">
                                                <div class="form-group">
                                                    <label for="input-advanced-select">Advanced Option as dropdown list</label>
                                                    <select id="input-advanced-select" class="form-control">
                                                        <option>Advanced Option 1</option>
                                                        <option>Advanced Option 2</option>
                                                        <option>Advanced Option 3</option>
                                                        <option>Advanced Option 4</option>
                                                    </select>
                                                </div>

                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="" id="input-advanced-checkbox-1">
                                                        Must contain at least one search term
                                                    </label>
                                                </div>

                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="input-advanced-radio-set-1" id="input-advanced-radio-1" value="option1" checked>
                                                        Must only be in "Themes" category
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="input-advanced-radio-set-1" id="input-advanced-radio-2" value="option2">
                                                        Must only be in "Plugins" category
                                                    </label>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </form>
                    </div>

                    <div class="header-info">
                        <div class="header-profile"> 
                            <ul class="header-profile-menu">
                                <li>
                                    <a href="#" class="top">
                                        <span class="header-profile-menu-icon">
                                            <img class="list-thumbnail" src="assets/images/required/profile/profile-pic-4.jpg" width="39" height="39" alt="profile-pic-4" />
                                        </span>
                                        <span class="main-menu-text">
                                            Ken Adams
                                            <i class="icon icon-arrow-down-bold-round icon-size-small"></i>
                                        </span>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="pages-profile.html">
                                                <span aria-hidden="true" class="icon icon-user"></span>
                                                <span class="main-text">我的资料</span>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="pages-profile.html">
                                                <span aria-hidden="true" class="icon icon-user"></span>
                                                <span class="main-text">修改密码</span>
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="pages-signin-1.html">
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
        <div id="left-column" > 
            <div id="mainnav">
            <div id="area">
                <ul>
                    <?php if(is_array($Areas)): $i = 0; $__LIST__ = $Areas;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><li><?php echo ($item["name"]); ?>
                        <ul>
                        <?php if(is_array($item[item1])): $i = 0; $__LIST__ = $item[item1];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item2): $mod = ($i % 2 );++$i;?><li><?php echo ($item2["name"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div>
            </div>
        </div>
        <div id="right-column">
            <div class="mainnav-horizontal-outer">
                <div class="menu-text-only" id="mainnav-horizontal-inner">
                    <div id="mainnav-horizontal">
                        <ul class="mainnav-horizontal">
                        <?php if(is_array($Menus)): $i = 0; $__LIST__ = $Menus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i; if(empty($m[items])): ?><li class="menu-item-top">
                                <a class="top" href="index.html">
                                    <span class="main-menu-icon">
                                        <span class="icon icon-grid-big icon-size-medium" aria-hidden="true"></span>
                                    </span>
                                    <span class="main-menu-text"><?php echo ($m["name"]); ?></span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="menu-item-top">                            
                                <a class="top" href="#">
                                    <i class="icon icon-arrow-down-bold-round icon-size-small"></i>
                                    <span class="main-menu-text"><?php echo ($m["name"]); ?></span>
                                </a>
                                <ul>
                                <?php if(is_array($m[items])): $i = 0; $__LIST__ = $m[items];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mm): $mod = ($i % 2 );++$i;?><li><a data-pjax href="<?php echo U($mm[controller].'/index');?>"><?php echo ($mm["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
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
                <div class="row">
                    <div class="page-container">
                        <div class="page animated bounceInDown">
<div class="col-md-12">
    <a class="btn btn-primary" href="">新增</a>
</div>
<div class="clearfix"></div>


<?php if(is_array($data[data])): $i = 0; $__LIST__ = $data[data];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-xs-12 col-md-6">
    <div class="c-widget c-widget-gallery">
        <div class="c-widget-gallery-desc">
            <blockquote>
                <p><?php echo ($vo["play_title"]); ?></p>
                <small>Someone famous in <cite title="Source Title">Source Title</cite></small>
            </blockquote>
        </div>
        <div class="c-widget-gallery-thumbnail">
            <img src="<?php echo ($vo["cover"]); ?>">
        </div>
    </div>
</div><?php endforeach; endif; else: echo "" ;endif; ?>



<div class="col-sm-6 col-md-6">
    <div class="c-widget c-widget-custom c-widget-facebook c-widget-size-normal">
        <div class="c-widget-icon">
            <span class="icon icon-social-facebook"></span>
        </div>
        <div data-ride="carousel" class="carousel slide" id="carousel-mail">
            <!-- Wrapper for slides -->
            <div role="listbox" class="carousel-inner">
                <div class="item">
                    <div class="c-wdiget-content-block mCustomScrollbar _mCS_5 mCS-autoHide mCS_no_scrollbar" style="overflow: visible;"><div class="mCustomScrollBox mCS-dark mCSB_vertical mCSB_outside" id="mCSB_5" style="max-height: 150px;" tabindex="0"><div dir="ltr" style="position:relative; top:0; left:0;" class="mCSB_container mCS_y_hidden mCS_no_scrollbar_y" id="mCSB_5_container">
                        <div class="c-widget-content-heading">
                            07 December <small>13:46</small>
                        </div>
                        <div class="c-widget-content-body">
                            Quickly maximize timely deliverables for real-time schemas. Dramatically maintain clicks-and-mortar solutions without functional solutions.
                        </div>
                        <div class="c-widget-content-sub">
                            <a href="#">
                                <span class="glyphicon glyphicon-thumbs-up"></span> 
                                Like This
                            </a>
                        </div>
                    </div></div><div class="mCSB_scrollTools mCSB_5_scrollbar mCS-dark mCSB_scrollTools_vertical" id="mCSB_5_scrollbar_vertical" style="display: none;"><div class="mCSB_draggerContainer"><div oncontextmenu="return false;" style="position: absolute; min-height: 30px; height: 0px; top: 0px;" class="mCSB_dragger" id="mCSB_5_dragger_vertical"><div class="mCSB_dragger_bar" style="line-height: 30px;"></div></div><div class="mCSB_draggerRail"></div></div></div></div>
                </div>
                <div class="item">
                    <div class="c-wdiget-content-block mCustomScrollbar _mCS_6 mCS-autoHide mCS_no_scrollbar" style="overflow: visible;"><div class="mCustomScrollBox mCS-dark mCSB_vertical mCSB_outside" id="mCSB_6" style="max-height: 150px;" tabindex="0"><div dir="ltr" style="position:relative; top:0; left:0;" class="mCSB_container mCS_y_hidden mCS_no_scrollbar_y" id="mCSB_6_container">
                        <div class="c-widget-content-heading">
                            05 December <small>09:39</small>
                        </div>
                        <div class="c-widget-content-body">
                            Efficiently unleash cross-media information without cross-media value. Quickly maximize timely deliverables for real-time schemas.
                        </div>
                        <div class="c-widget-content-sub">
                            <a href="#">
                                <span class="glyphicon glyphicon-thumbs-up"></span> 
                                Like This
                            </a>
                        </div>
                    </div></div><div class="mCSB_scrollTools mCSB_6_scrollbar mCS-dark mCSB_scrollTools_vertical" id="mCSB_6_scrollbar_vertical" style="display: none;"><div class="mCSB_draggerContainer"><div oncontextmenu="return false;" style="position: absolute; min-height: 30px; top: 0px; height: 0px;" class="mCSB_dragger" id="mCSB_6_dragger_vertical"><div class="mCSB_dragger_bar" style="line-height: 30px;"></div></div><div class="mCSB_draggerRail"></div></div></div></div>
                </div>
                <div class="item active">
                    <div class="c-wdiget-content-block mCustomScrollbar _mCS_7 mCS-autoHide mCS_no_scrollbar" style="overflow: visible;"><div class="mCustomScrollBox mCS-dark mCSB_vertical mCSB_outside" id="mCSB_7" style="max-height: 150px;" tabindex="0"><div dir="ltr" style="position:relative; top:0; left:0;" class="mCSB_container mCS_y_hidden mCS_no_scrollbar_y" id="mCSB_7_container">
                        <div class="c-widget-content-heading">
                            06 December <small>22:19</small>
                        </div>
                        <div class="c-widget-content-body">
                            Dramatically maintain clicks-and-mortar solutions without functional solutions. Quickly maximize timely deliverables for real-time schemas. 
                        </div>
                        <div class="c-widget-content-sub">
                            <a href="#">
                                <span class="glyphicon glyphicon-thumbs-up"></span> 
                                Like This
                            </a>
                        </div>
                    </div></div><div class="mCSB_scrollTools mCSB_7_scrollbar mCS-dark mCSB_scrollTools_vertical" id="mCSB_7_scrollbar_vertical" style="display: none;"><div class="mCSB_draggerContainer"><div oncontextmenu="return false;" style="position: absolute; min-height: 30px; top: 0px; height: 0px;" class="mCSB_dragger" id="mCSB_7_dragger_vertical"><div class="mCSB_dragger_bar" style="line-height: 30px;"></div></div><div class="mCSB_draggerRail"></div></div></div></div>
                </div>
            </div>
            <!-- Controls -->
            <a data-slide="prev" role="button" href="#carousel-mail" class="left carousel-control">
                <span aria-hidden="true" class="glyphicon glyphicon-chevron-left"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a data-slide="next" role="button" href="#carousel-mail" class="right carousel-control">
                <span aria-hidden="true" class="glyphicon glyphicon-chevron-right"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
</div>

</div>

                    </div>
                </div>
            </div>

            <div id="footer-container">
                <div class="footer-content">
                    &copy; <a href="#">浙江省文化厅信息中心</a> 技术支持 
                </div>
            </div>
        </div>
    </div>
</div>





<script type="text/javascript" src="assets/js/required/jquery.mCustomScrollbar.min.js"></script>
<script type="text/javascript" src="assets/js/required/misc/jquery.mousewheel-3.0.6.min.js"></script>
<script type="text/javascript" src="assets/js/required/misc/retina.min.js"></script>
<script type="text/javascript" src="assets/js/required/icheck.min.js"></script>
<script type="text/javascript" src="assets/js/required/misc/jquery.ui.touch-punch.min.js"></script>
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
<script type="text/javascript" src="assets/jstree/jstree.js"></script>
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
<script type="text/javascript">
$(function(){
    $('#area').jstree();
})    
</script>