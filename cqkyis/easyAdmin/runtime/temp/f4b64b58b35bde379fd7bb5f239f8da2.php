<?php /*a:2:{s:60:"D:\php-work-2018\EasyAdmin\cqkyicms\admin\view\good\add.html";i:1527233350;s:63:"D:\php-work-2018\EasyAdmin\cqkyicms\admin\view\index\index.html";i:1525804571;}*/ ?>
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
    #filePicker div:nth-child(2){width:100%!important;height:100%!important;}
    #icon div:nth-child(2){width:100%!important;height:100%!important;}
    .file-item{

        text-align: center;
        margin: 8px;
        width: 130px;
        height: 130px;

        position: relative;
        float: left;
    }

    .file-item img{
        width: 130px;
        height: 130px;
        position:absolute;
        left:0;
        top:0;
        z-index:1;
    }
    .make{
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 5;
        background-color: rgba(0, 0, 0, .5);
        text-align: center;
        display: none;
    }
    .make i{
        color: #fff;
        font-size: 32px;
        position: absolute;
        top: 0;
        right: 0;
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

                            <form action="<?php echo url('good/add'); ?>"  target="frame" method="post" id="Add" style="margin-top: 20px">

                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" for="good_name">商品名称 *</label>
                                        <div class="col-lg-7">
                                            <input class="form-control required" id="good_name" name="good_name" type="text">
                                        </div>
                                        <div class="col-lg-4">
                                           *（必填）商品的名称
                                        </div>
                                    </div>
                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" for="good_s_name">商品短标题 *</label>
                                        <div class="col-lg-7">
                                            <textarea class="form-control" name="good_s_name" id="good_s_name"></textarea>
                                        </div>
                                        <div class="col-lg-4">
                                            *（必填）商品的名称
                                        </div>
                                    </div>
                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" for="keys">商品关键字 </label>
                                        <div class="col-lg-7">
                                            <input class="form-control " id="keys" name="keys" type="text">
                                        </div>
                                        <div class="col-lg-4">
                                            多个关键字用逗号隔开，如海尔，空调
                                        </div>
                                    </div>
                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" for="dept_ids">商品分类 </label>
                                        <div class="col-lg-7">
                                            <input type="hidden" class="form-control" id="dept_id" name="cate_id"    placeholder="商品分类">
                                            <input type="text" class="form-control required" name="dept_ids" id="dept_ids"    placeholder="商品分类">

                                        </div>
                                        <div class="col-lg-4">
                                            商品分类
                                        </div>
                                    </div>
                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" for="good_img">商品图片 *</label>
                                        <div class="col-lg-7">
                                        <div class="input-group ">
                                            <input type="text" id="good_img" name="good_img" class="form-control required" placeholder="商品图片">
                                                        <span class="input-group-btn">
                                                         <div id="uploaders" class="wu-example">
                                                             <div id="icon" class="btn btn-info waves-effect waves-light"  >上传图片</div>

                                                         </div>
                                                        </span>
                                        </div>
                                            </div>
                                        <div class="col-lg-4">
                                            多个关键字用逗号隔开，如海尔，空调
                                        </div>
                                    </div>
                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" for="userName1">商品价格 *</label>
                                        <div class="col-lg-7">
                                            <div class="input-group ">
                                                <span class="input-group-addon">市场价</span>
                                                <input type="text"  name="price" class="form-control" placeholder="市场价">
                                                <span class="input-group-addon">商城价</span>
                                                <input type="text"  name="mall_price" class="form-control required" placeholder="商城价">

                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            多个关键字用逗号隔开，如海尔，空调
                                        </div>
                                    </div>

                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" for="userName1">属性</label>
                                        <div class="col-lg-7" style="margin-top: 5px;">

                                                <div class="checkbox checkbox-inline">
                                                    <input type="checkbox" name="is_home" id="inlineCheckbox1" value="1">
                                                    <label for="inlineCheckbox1"> 首页推荐 </label>
                                                </div>
                                            <div class="checkbox checkbox-success checkbox-inline">
                                                <input type="checkbox" id="inlineCheckbox2" name="is_new" value="1" checked="checked">
                                                <label for="inlineCheckbox2"> 新品上市 </label>
                                            </div>

                                        </div>
                                        <div class="col-lg-4">
                                            多个关键字用逗号隔开，如海尔，空调
                                        </div>
                                    </div>

                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" for="userName1">商品图片相册 </label>
                                        <div class="col-lg-7">
                                            <div id="uploader">
                                                <div id="filePicker" class=" btn btn-info waves-effect waves-light">上传图片</div>
                                                <div id="fileList" class="uploader-list">

                                                </div>


                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            可以多选上传,最大支持五张图片
                                        </div>
                                    </div>

                                    <!--<div class="form-group clearfix" >-->
                                        <!--<label class="col-lg-1 control-label" >规格设置 </label>-->
                                        <!--<div class="col-lg-3">-->
                                            <!--<input class="form-control " name="name[]" type="text" placeholder="规格名称">-->
                                        <!--</div>-->
                                        <!--<div class="col-lg-3">-->
                                            <!--<input class="form-control " name="pricespec[]" type="text" placeholder="规格价格">-->
                                        <!--</div>-->
                                        <!--<div class="col-lg-1">-->
                                            <!--<div class="btn btn-icon waves-effect waves-light btn-info m-b-5 ggadd"> <i class="md md-add"></i> </div>-->
                                        <!--</div>-->
                                        <!--<div class="col-lg-4">-->
                                            <!--规格设置-->
                                        <!--</div>-->

                                    <!--</div>-->
                                <!--<div class="item">-->


                                <!--</div>-->

                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" >分享标题 </label>
                                        <div class="col-lg-7">
                                            <input class="form-control " name="wx_title" type="text">
                                        </div>
                                        <div class="col-lg-4">
                                            分享标题
                                        </div>
                                    </div>
                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" for="userName1">分享说明 </label>
                                        <div class="col-lg-7">
                                            <textarea class="form-control" name="wx_cont"></textarea>
                                        </div>
                                        <div class="col-lg-4">
                                            分享标题
                                        </div>
                                    </div>


                                    <div class="form-group clearfix">
                                        <label class="col-lg-1 control-label" for="userName1">商品详情 </label>
                                            <div class="col-lg-7">
                                                <script src="/static/admins/js/ueditor/ueditor.config.js"></script>
                                                <script src="/static/admins/js/ueditor/ueditor.all.js"></script>
                                                <textarea id="content" name="context" style="width: 100%"></textarea>
                                                <script type="text/javascript">
                                                    var ue = UE.getEditor('content',{
                                                        serverUrl :'<?php echo url("ueditor/index"); ?>',
                                                        initialFrameHeight:400
//
                                                    });
                                                    editor.render("content");
                                                </script>
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
<link href="/static/admins/js/layui/css/layui.css" rel="stylesheet" type="text/css"/>
<script src="/static/admins/js/layer/layer.js"></script>
<link href="/static/admins/js/webuploader/webuploader.css" rel="stylesheet" type="text/css">
<script src="/static/admins/js/webuploader/webuploader.js"></script>
<script src="/static/admins/js/jquery-validation/dist/jquery.validate.min.js"></script>

<script>
    $().ready(function() {

        $("#Add").validate({

            messages: {
                good_name: "商品名称不能为空",
                good_img:"商品图片不能为空",
                mall_price:"商城价格不能为空",
                dept_ids:"商品分类不能为空"
            },
            showErrors: function(errorMap, errorList) {

                $.each(errorList, function (i, v) {

                    layer.tips(v.message, v.element, {tips: [1, '#3595CC'], time: 2000 });
                    return false;
                });
                onfocusout: false
            }
        });

    });

    $.validator.setDefaults({
        submitHandler: function () {


            $('#Add').submit();

        }
    });


    $('#dept_ids').on('click',function () {
        layer.open({
            type: 2,
            area: ['300px', '450px'],
            fixed: false, //不固定
            maxmin: true,
            content: "cate.html",
            title:"选择分类"
        });

    });

    var uploaders = WebUploader.create({
        auto: true,
        swf: '/static/admin/js/uploader/Uploader.swf',
        server: "<?php echo url('upload/upload'); ?>",
        pick: '#icon',
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        },
        formData: {
            face: 'good',
            wight: 300,
            height: 200
        },
        fileNumLimit:1
    });

    uploaders.on('uploadAccept', function (fieldata, ret) {

        $('#good_img').val(ret.urls);
        //$('.input-group-addon').html("<img src='/uploads/"+ret.urls+"''/>");
    });

    var uploader =WebUploader.create({
        auto: true,
        swf: '/static/admin/js/uploader/Uploader.swf',
        server: "<?php echo url('upload/upload'); ?>",
        pick: '#filePicker',
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        },
        formData: {
            face: 'good',
            wight: 300,
            height: 200
        },
        fileNumLimit:5
    });
    uploader.on('uploadAccept', function (fieldata, ret) {

        //if (ret.code == 'SUCCESS') {
            console.log(ret);
            addFile(ret);
        //}else{

      //  }
    });
    uploader.on('error',function () {
       layer.msg('上传数量超过5张！');
    });
    $list = $('#fileList');
    function addFile(ret) {

        var $li = $(
                '<div  class="file-item thumbnail  dd_'+ret.data+'" >' +
                '<img src="/uploads//'+ret.urls+'">' +
                '<input name="allimg[]" type="hidden" value="'+ret.urls+'">' +
                ' <div class="make ln" data-url="'+ret.urls+'">'+

                '<i class="md  md-clear" ></i>'+
                ' </div>' +
                '</div>'
        );
        $list.prepend( $li );
        $('.file-item').hover(function () {
           $(this).children('.make').show();

        },function () {
            $(this).children('.make').hide();
        });

        $('.ln').click(function () {
         var urls=$(this).attr('data-url');
            $.ajax({
                type: 'POST',
                url: "<?php echo url('upload/del'); ?>",
                data: {
                    "path" : urls
                },
                success:  function(ret) {

                    if(ret.code==1){

                        layer.msg('删除成功！');


                    }else{
                        layer.msg('删除失败！');
                    }
                }

            });

            $(this).parent('.file-item').remove();
             uploader.reset();
        });
}
//
//     var i=0;
// $('.ggadd').click(function () {
//     //点击添加向item里添加无素
//     i++;
//     var htmls=' <div class="form-group clearfix form-group'+i+'" >'+
//             '<label class="col-lg-1 control-label" >规格'+i+'设置 </label>'+
//             '<div class="col-lg-3">'+
//             '<input class="form-control " name="wx_title" type="text" placeholder="规格名称">'+
//            ' </div>'+
//             '<div class="col-lg-3">'+
//             '<input class="form-control " name="wx_title" type="text" placeholder="规格价格">'+
//            '</div>'+
//            ' <div class="col-lg-1">'+
//             '<div class="btn btn-icon waves-effect waves-light btn-info m-b-5" onclick="del('+i+')"> <i class="md md-remove" ></i> </div>'+
//             '</div>'+
//             '<div class="col-lg-4">规格'+i+'设置'+
//             '</div></div>';
//  $('.item').append(htmls);
//
// });
//     function del(i) {
//         $('.form-group'+i).remove();
//     }




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