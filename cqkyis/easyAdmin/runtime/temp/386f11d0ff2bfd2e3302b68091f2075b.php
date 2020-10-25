<?php /*a:2:{s:68:"D:\php-work-2018\EasyAdmin\cqkyicms\admin\view\goodconfig\index.html";i:1527849598;s:63:"D:\php-work-2018\EasyAdmin\cqkyicms\admin\view\index\index.html";i:1525804571;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <link rel="shortcut icon" href="assets/images/favicon_1.ico">

    <title>柯一网络通用后台管理系统-PHP版</title>

    <link href="/static/admins/plugins/sweetalert/dist/sweetalert.css" rel="stylesheet" type="text/css">

    <link href="/static/admins/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/core.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/icons.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/components.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/pages.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/menu.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/responsive.css" rel="stylesheet" type="text/css">

    <script src="/static/admins/js/modernizr.min.js"></script>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <script src="/static/admins/js/jquery.min.js"></script>
    <script src="/static/admins/js/bootstrap.min.js"></script>
    <script src="/static/admins/js/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="/static/admins/js/layer/layer.js"></script>
    <script src="/static/admins/js/admin.js"></script>
    <iframe id="frame" name="frame" style="display:none;"></iframe>
</head>


<body class="fixed-left">
<div id="wrapper">
    <!-- Top Bar Start -->
    <div class="topbar">
        <!-- LOGO -->
        <div class="topbar-left">
            <div class="text-center">
                <a href="index.html" class="logo"><img src="/static/admins/img/logos.png"/></a>
            </div>
        </div>
        <!-- Button mobile view to collapse sidebar menu -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container">
                <div class="">
                    <div class="pull-left">
                        <button class="button-menu-mobile open-left">
                            <i class="fa fa-bars"></i>
                        </button>
                        <span class="clearfix"></span>
                    </div>
                    <form class="navbar-form pull-left" role="search">
                        <div class="form-group">
                            <input type="text" class="form-control search-bar" placeholder="Type here for search...">
                        </div>
                        <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
                    </form>

                    <ul class="nav navbar-nav navbar-right pull-right">
                        <li class="dropdown hidden-xs">
                            <a href="#" data-target="#" class="dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="true">
                                <i class="md md-notifications"></i> <span class="badge badge-xs badge-danger">3</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-lg">
                                <li class="text-center notifi-title">Notification</li>
                                <li class="list-group">
                                    <!-- list item-->
                                    <a href="javascript:void(0);" class="list-group-item">
                                        <div class="media">
                                            <div class="pull-left">
                                                <em class="fa fa-user-plus fa-2x text-info"></em>
                                            </div>
                                            <div class="media-body clearfix">
                                                <div class="media-heading">New user registered</div>
                                                <p class="m-0">
                                                    <small>You have 10 unread messages</small>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- list item-->
                                    <a href="javascript:void(0);" class="list-group-item">
                                        <div class="media">
                                            <div class="pull-left">
                                                <em class="fa fa-diamond fa-2x text-primary"></em>
                                            </div>
                                            <div class="media-body clearfix">
                                                <div class="media-heading">New settings</div>
                                                <p class="m-0">
                                                    <small>There are new settings available</small>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- list item-->
                                    <a href="javascript:void(0);" class="list-group-item">
                                        <div class="media">
                                            <div class="pull-left">
                                                <em class="fa fa-bell-o fa-2x text-danger"></em>
                                            </div>
                                            <div class="media-body clearfix">
                                                <div class="media-heading">Updates</div>
                                                <p class="m-0">
                                                    <small>There are
                                                        <span class="text-primary">2</span> new updates available</small>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- last list item -->
                                    <a href="javascript:void(0);" class="list-group-item">
                                        <small>See all notifications</small>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="hidden-xs">
                            <a href="#" id="btn-fullscreen" class="waves-effect"><i class="md md-crop-free"></i></a>
                        </li>
                        <li class="hidden-xs">
                            <a href="#" class="right-bar-toggle waves-effect"><i class="md md-chat"></i></a>
                        </li>
                        <li class="dropdown">
                            <a href="" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="true"><img src="/static/admins/images/users/avatar-1.jpg" alt="user-img" class="img-circle"> </a>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:void(0)"><i class="md md-face-unlock"></i> 修改资料</a></li>

                                <li><a href="javascript:void(0)"><i class="md md-lock"></i> 修改密码</a></li>
                                <li><a href="<?php echo url('login/out'); ?>"><i class="md md-settings-power"></i> 退出</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!--/.nav-collapse -->
            </div>
        </div>
    </div>
    <!-- Top Bar End -->


    <div class="left side-menu">
        <div class="sidebar-inner slimscrollleft">
            <div class="user-details">
                <div class="pull-left">
                    <img src="/static/admins/images/users/avatar-1.jpg" alt="" class="thumb-md img-circle">
                </div>
                <div class="user-info">
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php echo htmlentities(app('session')->get('user.nickname')); ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:void(0)"><i class="md md-face-unlock"></i> 修改资料<div class="ripple-wrapper"></div></a></li>

                            <li><a href="javascript:void(0)"><i class="md md-lock"></i> 修改密码</a></li>
                            <li><a href="<?php echo url('login/out'); ?>"><i class="md md-settings-power"></i> 退出</a></li>
                        </ul>
                    </div>

                    <p class="text-white m-0"><?php echo htmlentities(app('session')->get('user.role_name')); ?></p>
                </div>
            </div>
            <!--- Divider -->
            <div id="sidebar-menu">
                <ul>
                    <li>
                        <a href="<?php echo url('index/index'); ?>" class="waves-effect waves-light  <?php if(($mp['parent_id'] == 0)): ?> active " <?php endif; ?> "><i class="md md-home"></i><span> 欢迎页面 </span></a>
                    </li>
                    <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): if( count($menu)==0 ) : echo "" ;else: foreach($menu as $key=>$vo): ?>
                    <li class="has_sub">
                        <a  class="waves-effect waves-light  <?php if(($mp['parent_id'] == $vo['menu_id'])): ?> active " <?php endif; ?>  "><i class="<?php echo htmlentities($vo['menu_icon']); ?>"></i><span> <?php echo htmlentities($vo['menu_name']); ?> </span><span class="pull-right"><i class="md  md-keyboard-arrow-right"></i></span></a>
                        <ul class="list-unstyled"    >
                            <?php if(is_array($vo['sub']) || $vo['sub'] instanceof \think\Collection || $vo['sub'] instanceof \think\Paginator): if( count($vo['sub'])==0 ) : echo "" ;else: foreach($vo['sub'] as $key=>$sub): ?>

                            <li  <?php if(($mp['menu_id'] == $sub['menu_id'])): ?> class="active"  <?php endif; ?>><a  href="<?php echo url($sub['menu_role']); ?>"  ><i class="<?php echo htmlentities($sub['menu_icon']); ?>"></i><?php echo htmlentities($sub['menu_name']); ?></a></li>
                            <?php endforeach; endif; else: echo "" ;endif; ?>

                        </ul>
                    </li>

                    <?php endforeach; endif; else: echo "" ;endif; ?>


                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="content-page">
        <!-- Start content -->
        
<style>
    .tab-content{
        box-shadow: 0 0 0;
    }

</style>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title"><?php echo htmlentities($title); ?></h4>
                <ol class="breadcrumb pull-right">
                    <li><?php echo htmlentities($title); ?></li>
                    <li class="active"><?php echo htmlentities($name); ?></li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo htmlentities($name); ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <form action="<?php echo url('goodconfig/index'); ?>"  target="frame" method="post" id="Add" style="margin-top: 20px">


                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" for="appid">是否开启包送 *</label>
                                        <div class="col-lg-7">
                                            <div class="radio radio-info radio-inline">
                                                <input type="radio" id="inlineRadio1" value="0" name="status" >
                                                <label for="inlineRadio1">禁用 </label>
                                            </div>
                                            <div class="radio radio-info radio-inline">
                                                <input type="radio" id="inlineRadio2" value="1" name="status" >
                                                <label for="inlineRadio2"> 启用 </label>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" for="postprice">配送费 </label>
                                        <div class="col-lg-7">
                                            <input class="form-control " id="postprice" name="postprice" value="<?php echo htmlentities($config['postprice']); ?>" type="text" placeholder="配送费">
                                        </div>

                                    </div>

                                <div class="form-group clearfix">
                                    <label class="col-lg-1 control-label" for="mprice">满包送 </label>
                                    <div class="col-lg-7">
                                        <div class="input-group">
                                            <span class="input-group-addon">满</span>
                                            <input type="text" id="mprice" name="mprice" value="<?php echo htmlentities($config['mprice']); ?>" class="form-control" placeholder="Username">
                                            <span class="input-group-addon">元包送</span>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group clearfix">
                                    <label class="col-lg-1 control-label" for="sendtime">送达时间 </label>
                                    <div class="col-lg-7">
                                        <input class="form-control " id="sendtime" name="sendtime" value="<?php echo htmlentities($config['sendtime']); ?>" type="text" placeholder="送达时间精确到分钟">
                                    </div>

                                </div>
                                <div class="form-group clearfix">
                                    <label class="col-lg-1 control-label" for="yetime">营业时间 </label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                        <span class="input-group-addon">早</span>
                                        <input class="form-control " id="yetime" name="yetime" value="<?php echo htmlentities($config['yetime']); ?>" type="text" placeholder="营业时间">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                        <span class="input-group-addon">晚</span>
                                        <input class="form-control " id="yetimes" name="yetimes" value="<?php echo htmlentities($config['yetimes']); ?>" type="text" placeholder="营业时间">
                                        </div>
                                    </div>

                                </div>














                            <div class="form-group ">
                                <div class=" col-sm-9">
                                    <button type="submit" class="btn btn-info waves-effect waves-light">保存</button>
                                </div>
                            </div>
                            </form>

                        </div>


                    </div>  <!-- End panel-body -->
                </div> <!-- End panel -->

            </div> <!-- end col -->

        </div>

    </div>
</div>
<script src="/static/admins/js/jquery.min.js"></script>
<script src="/static/admins/js/bootstrap.min.js"></script>
<link href="/static/admins/js/layui/css/layui.css" rel="stylesheet" type="text/css"/>
<script src="/static/admins/js/layer/layer.js"></script>
<link href="/static/admins/js/webuploader/webuploader.css" rel="stylesheet" type="text/css">
<script src="/static/admins/js/webuploader/webuploader.js"></script>
<script src="/static/admins/js/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="/static/admins/js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="/static/admins/js/bootstrap-datepicker/js/bootstrap-datepicker.zh-CN.min.js"></script>
<script src="/static/admins/js/timepicker/bootstrap-timepicker.min.js"></script>
<link href="/static/admins/js/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css"/>
<link href="/static/admins/js/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<script>
    $('#yetime').timepicker({showMeridian: false});
    $('#yetimes').timepicker({showMeridian: false})
//    $().ready(function() {
//
//        $("#Add").validate({
//
//            messages: {
//                good_name: "商品名称不能为空",
//                good_img:"商品图片不能为空",
//                mall_price:"商城价格不能为空",
//                dept_ids:"商品分类不能为空"
//            },
//            showErrors: function(errorMap, errorList) {
//
//                $.each(errorList, function (i, v) {
//
//                    layer.tips(v.message, v.element, {tips: [1, '#3595CC'], time: 2000 });
//                    return false;
//                });
//                onfocusout: false
//            }
//        });
//
//    });
//
//    $.validator.setDefaults({
//        submitHandler: function () {
//
//
//            $('#Add').submit();
//
//        }
//    });
//






</script>



</div>
    <footer class="footer text-right">
        2015 © 重庆柯一网络有限公司.
    </footer>





    <script>
        var resizefunc = [];
    </script>

    <!-- jQuery  -->

    <script src="/static/admins/js/detect.js"></script>
    <script src="/static/admins/js/fastclick.js"></script>
    <script src="/static/admins/js/jquery.slimscroll.js"></script>
    <script src="/static/admins/js/jquery.blockUI.js"></script>
    <script src="/static/admins/js/waves.js"></script>
    <script src="/static/admins/js/wow.min.js"></script>
    <script src="/static/admins/js/jquery.nicescroll.js"></script>
    <script src="/static/admins/js/jquery.scrollTo.min.js"></script>

    <script src="/static/admins/js/jquery.app.js"></script>

    <!-- moment js  -->
    <script src="/static/admins/plugins/moment/moment.js"></script>

    <!-- counters  -->
    <script src="/static/admins/plugins/waypoints/lib/jquery.waypoints.js"></script>
    <script src="/static/admins/plugins/counterup/jquery.counterup.min.js"></script>

    <!-- sweet alert  -->
    <script src="/static/admins/plugins/sweetalert/dist/sweetalert.min.js"></script>


    <!-- flot Chart -->
    <script src="/static/admins/plugins/flot-chart/jquery.flot.js"></script>
    <script src="/static/admins/plugins/flot-chart/jquery.flot.time.js"></script>
    <script src="/static/admins/plugins/flot-chart/jquery.flot.tooltip.min.js"></script>
    <script src="/static/admins/plugins/flot-chart/jquery.flot.resize.js"></script>
    <script src="/static/admins/plugins/flot-chart/jquery.flot.pie.js"></script>
    <script src="/static/admins/plugins/flot-chart/jquery.flot.selection.js"></script>
    <script src="/static/admins/plugins/flot-chart/jquery.flot.stack.js"></script>
    <script src="/static/admins/plugins/flot-chart/jquery.flot.crosshair.js"></script>

    <!-- todos app  -->
    <script src="/static/admins/pages/jquery.todo.js"></script>

    <!-- chat app  -->
    <script src="/static/admins/pages/jquery.chat.js"></script>

    <!-- dashboard  -->
    <script src="/static/admins/pages/jquery.dashboard.js"></script>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.counter').counterUp({
                delay: 100,
                time: 1200
            });
        });
    </script>







</body>
</html>