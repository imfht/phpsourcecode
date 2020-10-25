<?php /*a:2:{s:57:"E:\kyweixin\EasyAdmin\cqkyicms\admin\view\dept\index.html";i:1525928144;s:58:"E:\kyweixin\EasyAdmin\cqkyicms\admin\view\index\index.html";i:1525804571;}*/ ?>
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
        
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title"><?php echo htmlentities($title); ?></h4>
                    <ol class="breadcrumb pull-right">
                        <li><?php echo htmlentities($title); ?></li>
                        <li class="active"><?php echo htmlentities($name); ?>管理</li>
                    </ol>
                </div>
            </div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo htmlentities($name); ?>管理</h3>
    </div>
    <div class="panel-body">

        <a class="btn btn-success btn-icon btn-sm" href="<?php echo url('dept/add',array('id'=>0)); ?>" rel="add" >
            <i class="md  md-add"></i>
            <span>添加部门</span>
        </a>


        <div class="row">

            <div class="col-md-12 col-sm-12 col-xs-12">

                <table class="table  table-striped" id="menuTable">
                </table>


            </div>
        </div>
    </div>
</div>
            </div>
        </div>


<link href="/static/admins/js/jqTreeGrid/jquery.treegrid.css" rel="stylesheet" type="text/css">
<script src="/static/admins/js/jqTreeGrid/jquery.treegrid.js"></script>
<script src="/static/admins/js/jqTreeGrid/jquery.treegrid.extension.js"></script>
<script>
    load();
    function load() {
        $('#menuTable')
                .bootstrapTreeTable(
                        {
                            id: 'dept_id',
                            code: 'dept_id',
                            parentCode: 'parent_id',
                            type: "GET", // 请求数据的ajax类型
                            url: "<?php echo url('dept/deptlist'); ?>", // 请求数据的ajax的url
                            ajaxParams: {sort:'orderby'}, // 请求数据的ajax的data属性
                            expandColumn: '1',// 在哪一列上面显示展开按钮
                            striped: false, // 是否各行渐变色
                            bordered: true, // 是否显示边框
                            expandAll: false, // 是否全部展开
                            // toolbar : '#exampleToolbar',
                            columns: [
                                {
                                    title: '编号',
                                    field: 'dept_id',
                                    visible: false,
                                    align: 'center',
                                    valign: 'center',
                                    width: '5%'
                                },
                                {
                                    title: '部门名称',
                                    valign: 'center',
                                    field: 'dept_name',
                                    width: '20%'
                                },

                                {
                                    title: '部门备注',
                                    valign: 'center',
                                    field: 'dept_cont',
                                    width: '20%'
                                },



                                {
                                    title: '排序',
                                    valign: 'center',
                                    width : '20%',
                                    field: 'orderby'

                                },
                                {
                                    title: '操作',
                                    field: 'id',
                                    align: 'center',
                                    valign: 'center',
                                    formatter: function (item, index) {
                                        var pid = item.dept_id;
                                        var addother = "<?php echo url('dept/add',array('id'=>'p_id')); ?>";
                                        var editother = "<?php echo url('dept/edit',array('id'=>'p_id')); ?>";
                                        var delother = "<?php echo url('dept/del',array('id'=>'p_id')); ?>";
                                        var add =  addother.replace("p_id",pid);
                                        var edit =  editother.replace("p_id",pid);
                                        var del=delother.replace("p_id",pid);
                                        var e="<a class='btn btn-pink waves-effect waves-light  btn-sm' rel='add' href='"+edit+"'><i class='fa fa-paint-brush'></i> 修改</a>&nbsp;";

                                        var p='<a class="btn btn-success  waves-effect waves-light  btn-sm" rel="add" href="'+add+'"><i class="fa fa-plus"></i>添加下级</a>&nbsp;';
                                        var d="<a class='btn btn-inverse  waves-effect waves-light  btn-sm' rel='del' href='"+del+"'><i class='fa fa-remove'></i>删除</a>&nbsp;";

                                        return e + d + p;
                                    }
                                }]
                        });
    }

    function reLoad() {
        load();
    }

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