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
    background-image: url('assets/man_bg.jpg');
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
                    <div class="logo">
                        <a href="index.html">
                            <img class="default-logo" src="assets/images/required/logo-default.png" height="44" alt="Logo" />
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
                                        <span class="main-text">搜索</span>
                                    </a>
                                    <a href="#" class="search-opened">
                                        <span aria-hidden="true" class="icon icon-cross"></span>
                                        <span class="main-text">搜索</span>
                                    </a>
                                    <ul>
                                        <li class="simple-search">
                                            <div class="simple-search-inner">
                                                <div class="simple-search-block">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="input-search" placeholder="请输入搜索关键字">
                                                        <span class="input-group-btn">
                                                            <button type="submit" class="btn btn-default">
                                                                <span class="main-text">确定</span>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" id="input-advanced-search">其他选项
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="advanced-search">
                                            <div class="advanced-search-block">
                                                <div class="form-group">
                                                    <select id="input-advanced-select" class="form-control">
                                                        <option>文化设施</option>
                                                        <option>文化活动</option>
                                                    </select>
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
                                            <img class="list-thumbnail" src="<?php echo (cutPic($user["head"],39,39)); ?>" width="39" height="39" alt="profile-pic-4" />
                                        </span>
                                        <span class="main-menu-text">
                                            <?php echo ($user["name"]); ?>
                                            <i class="icon icon-arrow-down-bold-round icon-size-small"></i>
                                        </span>
                                    </a>
                                    <ul>
                                        <li>
                                            <a data-pjax href="<?php echo U('User/profile');?>">
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
        <div id="left-column" >
        <div class="col col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block left_area">
                        
                        <div class="block-content-outer">
                        <div class="block-content-inner">
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
                <div class="row page-body">
                    <div class="page-container">
                        <div class="page animated bounceInDown">
<div class="col-md-12">

<div data-image-url="<?php echo (cutPic($user["cover"],1021,190)); ?>" class="profile-header-feature-image" style="background: transparent url('<?php echo (cutPic($user["cover"],1021,190)); ?>') repeat scroll center center;">
    <div class="row">
        <div class="col-md-3">
            <div class="profile-info">
                <div class="main-profile-pic">
                    <a href="#">
                        <img width="190" height="190" alt="" src="<?php echo (cutPic($user["head"],190,190)); ?>" class="img-responsive list-thumbnail-normal">
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<div class="clearfix"></div>

<div class="col col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block">
                        <div  class="block-heading">
                        <div class="main-text h2">
                        <div class="caption">基本资料</div></div>
                    </div>
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            <form method="post" action="<?php echo U('User/profile');?>">
<div class="row">
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_name">姓名</label>
                <input  value="<?php echo ($user["name"]); ?>"  name="name" type="text" placeholder="" id="field_name" class="form-control ">
                </div>
            </div>
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_sex">性别</label>
                <select data-value="<?php echo ($user["sex"]); ?>" class="form-control" name="sex">
            <option value="1">男</option>
            <option value="2">女</option>
        </select>
                </div>
            </div>
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_email">邮箱</label>
                <input  value="<?php echo ($user["email"]); ?>"  name="email" type="text" placeholder="" id="field_email" class="form-control ">
                </div>
            </div>
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_mobile">联系电话</label>
                <input  value="<?php echo ($user["mobile"]); ?>"  name="mobile" type="text" placeholder="" id="field_mobile" class="form-control ">
                </div>
            </div>
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_addr">联系地址</label>
                <input  value="<?php echo ($user["addr"]); ?>"  name="addr" type="text" placeholder="" id="field_addr" class="form-control ">
                </div>
            </div>
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_intro">个人说明</label>
                <input  value="<?php echo ($user["intro"]); ?>"  name="intro" type="text" placeholder="" id="field_intro" class="form-control ">
                </div>
            </div>
</div>
                        </div>
                        </div>
                    </div>
                </div>

<div class="col col-md-6 col-sm-6 col-xs-6 col-lg-6">
                    <div class="block">
                        <div  class="block-heading">
                        <div class="main-text h2">
                        <div class="caption">头像</div></div>
                    </div>
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            <input class="file-loading" id="upHead" type="file" multiple="false" name="head_pic"  >
                        </div>
                        </div>
                    </div>
                </div>

<div class="col col-md-6 col-sm-6 col-xs-6 col-lg-6">
                    <div class="block">
                        <div  class="block-heading">
                        <div class="main-text h2">
                        <div class="caption">背景图片</div></div>
                    </div>
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            <input class="file-loading" id="uploadAttr" type="file" multiple="false" name="cover_pic"  >
                        </div>
                        </div>
                    </div>
                </div>

<input class="btn btn-primary" type="submit" value="保存" />
<a data-pjax class="btn btn-primary " href="<?php echo ($backUrl); ?>">取消</a>

</form>
</Widget:show>

</div>


<script type="text/javascript">
$(function(){
    
    $("#upHead").fileinput({
        'showUpload':true, 
        'multiple':false, 
        'showRemove': false,
        'uploadAsync': false,
        'overwriteInitial': false,
        'uploadUrl': "<?php echo U('Admin/User/upHead');?>",
        'previewFileType':'any',
    });
    $("#uploadAttr").fileinput({
        'showUpload':true, 
        'multiple':false, 
        'showRemove': false,
        'uploadAsync': false,
        'overwriteInitial': false,
        'uploadUrl': "<?php echo U('Admin/User/upCover');?>",
        'previewFileType':'any',
    });
})    
</script>

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