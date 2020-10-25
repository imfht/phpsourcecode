<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <TITLE>『运维管理系统V1.0』</TITLE>

    <!--[if IE]>
    <link rel="stylesheet" type="text/css" href="../Public/css/ie.css" /><![endif]-->
    <script type="text/javascript" src="../Public/js/common.js"></script>
    <script type="text/javascript" src="../Public/js/jquery.min.js"></script>
    <script type="text/javascript" src="../Public/js/jquery_ui_custom.js"></script>
    <script type="text/javascript" src="../Public/js/jquery.form.js"></script>
    <script type="text/javascript" src="__PUBLIC__/Js/shCore.js"></script>
    <SCRIPT LANGUAGE="JavaScript">

        //var $J=jQuery.noConflict();
        //指定当前组模块URL地址
        var URL = '__URL__';
        var ACTION_NAME = 'index';
        var APP = '__APP__';
        var PUBLIC = '__PUBLIC__';

        jQuery(document).ready(function () {
            jQuery(document).bind("contextmenu", function (e) {
                //return false;
            });
        });
        //-->
    </SCRIPT>


    <!--添加bootstrap css-->
    <!-- Loading Bootstrap -->
    <link href="../Public/bootstrap/bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Loading Stylesheets -->
    <link href="../Public/bootstrap/css/archon.css" rel="stylesheet">
    <link href="../Public/bootstrap/css/responsive.css" rel="stylesheet">
    <link href="../Public/bootstrap/css/prettify.css" rel="stylesheet">

    <!-- Loading Custom Stylesheets -->
    <link href="../Public/bootstrap/css/custom.css" rel="stylesheet">

    <!-- Loading Custom Stylesheets -->
    <link href="../Public/bootstrap/css/custom.css" rel="stylesheet">
    <!-- 	自定义添加 -->
    <link href="../Public/bootstrap/css/page.css" rel="stylesheet">
    <link rel="shortcut icon" href="../Public/bootstrap/images/favicon.ico">


    <!--添加原有的 js-->
    <script type="text/javascript" src="../Public/js/jquery.sparkline.min.js"></script>
    <script type="text/javascript" src="../Public/js/bootstrap-modal.min.js"></script>
    <script type="text/javascript" src="../Public/js/bootstrap-bootbox.min.js"></script>
    <script type="text/javascript" src="../Public/js/bootstrap-progressbar.js"></script>
    <script type="text/javascript" src="../Public/js/jquery.collapsible.min.js"></script>
    <script type="text/javascript" src="../Public/js/jquery.uniform.min.js"></script>
    <script type="text/javascript" src="../Public/js/index.js"></script>

    <!-- 自定义添加的css -->
    <style type="text/css">


        /* # Modal
        ================================================== */

        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            z-index: 1050;
            overflow: auto;
            width: 560px;
            margin: -250px 0 0 -280px;
            border: 1px solid #c5c5c5;

            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;

            -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            -moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);

            -webkit-background-clip: padding-box;
            -moz-background-clip: padding-box;
            background-clip: padding-box;
            height: 125px;

        }

        .modal-body {
            overflow-y: auto;
            padding: 15px;
            background-color: #45aeda;
            color: #ffffff;
        }

        .bootbox {

            background-color: #ffffff;

        }

        .modal-footer {
            border: 0px solid #c5c5c5;
        }

        .td_center {
            border-radius: 2px;
            font-size: 13px;
            font-weight: 500;
            line-height: 20px;
            margin: 0;
            align: center;

        }

        .th_center {
            border-radius: 2px;
            font-size: 13px;
            font-weight: bold;
            line-height: 20px;
            font-family: 黑 体;
            margin: 0;
            padding: 8px 5px 8px 0;
            text-shadow: inheri;
            align: center;
        }

        .tb_title {
            color: #ffffff;
            cursor: pointer;
            font-size: 18px !important;
            line-height: 18px;
        }

        .add_title {
            background-color: #59d1a3;
            color: #ffffff;
            cursor: pointer;
            font-size: 18px !important;
            line-height: 18px;
        }

        .left_title {
            font-family: 微 软 雅 黑;
            cursor: pointer;
            font-size: 16px !important;
            line-height: 18px;
        }


    </style>




</head>



<body>


	<div class="frame">
		<div class="sidebar">
			<div class="wrapper">



				<!-- Replace the src of the image with your logo -->
				<a href="index.html" class="logo"><img  src="../Public/bootstrap/images/logo.png" alt="运维管理系统" /></a>
                <ul class="nav  nav-list">




                    <?php if(is_array($firstmenu)): $i = 0; $__LIST__ = $firstmenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
                        <a  class="dropdown" href="#"><i class="icon-tint"></i> <?php echo ($menu['title']); ?><span class="label"><i class="icon-double-angle-down"></i></span></a>
                        <ul>
                            <?php if(is_array($menu['second'])): $i = 0; $__LIST__ = $menu['second'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$submenu): $mod = ($i % 2 );++$i;?><li class="<?php echo ($submenu['display']); ?>"  ><a  href="<?php echo ($submenu['url']); ?>"  target="<?php echo ($submenu['target']); ?>"><i class="icon-bullhorn"></i> <?php echo ($submenu['title']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>


                </ul>
			</div><!-- /Wrapper -->
		</div><!-- /Sidebar -->

		<!-- Main content starts here-->
		<div class="content">
            <div id="main-content">

			<div class="navbar">
				<a href="#" onclick="return false;" class="btn pull-left toggle-sidebar"><i class="icon-list"></i></a>
				<a class="navbar-brand" href="index.html">首页</a>

				<!-- Top right user menu -->
				<ul class="nav navbar-nav user-menu pull-right">
					<!-- First nav user item -->
					<li class="dropdown hidden-xs">
						<a class="dropdown-toggle" data-toggle="dropdown"><i class="icon-envelope-alt"></i></a>
						<ul class="dropdown-menu right inbox">
							<li class="dropdown-menu-title">
								INBOX <span>(2)</span>
							</li>
							<li>
								<img src="../Public/bootstrap/images/theme/1_120628140539_11.jpg" alt="" class="avatar">
								<div class="message">
									<span class="username">黄总</span>
									<span class="mini-details">(6) <i class="icon-paper-clip"></i></span>
									<span class="time pull-right"> 06:58 PM</span>
									<p>请记得清理日志空间！</p>
								</div>
							</li>
							<li>
								<img src="../Public/bootstrap/images/theme/1_101126212222_5.jpg" alt="" class="avatar">
								<div class="message">
									<span class="username">小芳</span>
									<span class="mini-details">(2) <i class="icon-paper-clip"></i></span>
									<span class="time pull-right"> 09:58 AM</span>
									<p>明天是不是聚餐呢?</p>
								</div>
							</li>

							<li class="dropdown-menu-footer">
								<a href="#">查看所有邮件!</a>
							</li>
						</ul>
					</li><!-- /dropdown -->

					<!-- Second nav user item -->
					<li class="dropdown hidden-xs">
						<a class="dropdown-toggle" data-toggle="dropdown"><i class="icon-bell"></i></a>
						<ul class="dropdown-menu right notifications">
							<li class="dropdown-menu-title">
								消息通知
							</li>
							<li>
								<i class="icon-cog avatar text-success"></i>
								<div class="message">
									<span class="username text-success">周三亚马逊见面!</span>
									<span class="time pull-right"> 06:58 PM</span>
								</div>
							</li>
							<li>
								<i class="icon-shopping-cart avatar text-danger"></i>
								<div class="message">
									<span class="username text-danger">周五大版本发布！</span>
									<span class="time pull-right"> 04:29 PM</span>
								</div>
							</li>


							<li class="dropdown-menu-footer">
								<a href="#">查看所有消息</a>
							</li>
						</ul>
					</li><!-- / dropdown -->

					<li class="dropdown user-name">
						<a class="dropdown-toggle" data-toggle="dropdown"><img src="../Public/bootstrap/images/theme/1_120628140539_2.jpg" class="user-avatar" alt="" />管理员</a>
							<ul class="dropdown-menu right inbox user">
								<li class="user-avatar">
									<img src="../Public/bootstrap/images/theme/1_101126212222_7.jpg" class="user-avatar" alt="" />
									管理员
								</li>
							<li>
								<i class="icon-user avatar"></i>
								<div class="message">
									<span class="username"><a style=" color: #ffffff;" href="<?php echo U('Admin/User/profile');?>">个人信息 </a></span>
								</div>
							</li>
							<li>
								<i class="icon-cogs avatar"></i>
								<div class="message">
									<span class="username"><a style=" color: #ffffff;" href="<?php echo U('Admin/Config/setting?group='.$cg['name']);?>">系统设置 </a></span>
								</div>
							</li>
							<li>
								<i class="icon-book avatar"></i>
								<div class="message">
									<span class="username">帮助文档 </span>
								</div>
							</li>
							<li><a href="<?php echo U('Admin/Public/logout');?>">注销</a></li>
						</ul>
					</li><!-- / dropdown -->
				</ul><!-- / Top right user menu -->

			</div><!-- / Navbar-->

			<div id="main-content">

			    <div class="row">
					<div class="col-mod-12">
						<ul class="breadcrumb">
							<li><a href="<?php echo U('Admin/Index/index');?>" >返回主页</a></li>
							<li><a href="<?php echo U('Admin/Index/index');?>"><?php echo (($transtring["main_title"])?($transtring["main_title"]):"任务管理"); ?></a></li>
							<li class="active"><?php echo (($transtring["next_title"])?($transtring["next_title"]):"本周任务管理"); ?></li>
						</ul>
					</div>
				</div>

				<div class="row">
					<div class="col-mod-12">
						<div class="alert alert-dismissable alert-success alert-dashboard fade in">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<strong>Hi!</strong> 亲们,天气冷了注意保暖哦!
						</div>
					</div>
				</div>




				 
<div class="row" style="text-align: center; margin:0 auto;">
    <div class="col-md-10">
        <div class="panel panel-archon">
            <div class="panel-heading" style="background-color: #59d1a3; color: #ffffff; text-transform: uppercase;">
                <h3 class="panel-title add_title">
                    编辑菜单		<span class="pull-right">
										<a style="color: white;" href="#" class="panel-minimize"><i
                                                class="icon-chevron-up"></i></a>
										<a style="color: white;" href="#" class="panel-close"><i
                                                class="icon-remove"></i></a>
									</span>
                </h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" action="<?php echo ($update_link); ?>" method="post">

                    <?php foreach ($vo as $key=>$val) { if(in_array($key, $select_index )){ echo "
                    <div class='form-group'>"; echo "<label class='col-lg-3 control-label left_title'>"; foreach ($addarr as $cn=>$en){ if($key == "$en" ){ echo $cn; } } echo "</label>"; echo "
                        <div class='col-lg-9'>"; echo "<select class='form-control left_title' name='".$key."'>"; foreach($selectarr["$key"] as $name=>$value ){ echo "
                                <option value='$name'"; if( $val == $name){ echo "selected"; } echo ">$value</option>
                                "; } echo "</select>"; echo "
                        </div>
                        "; echo "
                    </div>
                    "; }else{ switch($key){ case "id": echo "
                        <div class='form-group'>"; echo "<label class='col-lg-3 control-label left_title'>"; foreach ($addarr as $cn=>$en){ if($key == "$en" ){ echo $cn; } } echo "</label>"; echo "
                            <div class='col-lg-9'>"; echo "<input type='text' name='$key'  value='$val' class='form-control left_title' disabled>"; echo "<input type='hidden' name='$key'  value='$val' class='form-control left_title' >"; echo "
                            </div>
                            "; echo "
                        </div>
                        "; break; default: echo "
                        <div class='form-group'>"; echo "<label class='col-lg-3 control-label left_title'>"; foreach ($addarr as $cn=>$en){ if($key == "$en" ){ echo $cn; } } echo "</label>"; echo "
                            <div class='col-lg-9'>"; echo "<input type='text' name='$key' value='$val' class='form-control left_title'>"; echo "
                            </div>
                            "; echo "
                        </div>
                        "; } } }?>


                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">保存</button>
                        <button type="reset" class="btn btn-default"
                                onclick="window.location.href='<?php echo ($index_link); ?>'">取消
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>















			</div><!-- /Main Content  @7 -->

		</div><!-- / Content @5 -->

		<div class="row footer">
			<div class="col-md-12 text-center">
				© 2014 <a href="http://bootstrapguru.com/">系统运维组</a>
			</div>
		</div>
	</div> <!-- Frame -->



<!--添加bootstrap js-->
	<!-- Load JS here for greater good =============================-->
	<script src="../Public/bootstrap/js/jquery-ui-1.10.3.custom.min.js"></script>
	<script src="../Public/bootstrap/js/jquery.ui.touch-punch.min.js"></script>
	<script src="../Public/bootstrap/js/bootstrap.min.js"></script>
	<script src="../Public/bootstrap/js/bootstrap-select.js"></script>
	<script src="../Public/bootstrap/js/bootstrap-switch.js"></script>
	<script src="../Public/bootstrap/js/jquery.tagsinput.js"></script>
	<script src="../Public/bootstrap/js/jquery.placeholder.js"></script>
	<script src="../Public/bootstrap/js/bootstrap-typeahead.js"></script>
	<script src="../Public/bootstrap/js/application.js"></script>
	<script src="../Public/bootstrap/js/moment.min.js"></script>
	<script src="../Public/bootstrap/js/jquery.dataTables.min.js"></script>
	<script src="../Public/bootstrap/js/jquery.sortable.js"></script>
	<script type="text/javascript" src="../Public/bootstrap/js/jquery.gritter.js"></script>

	<!-- Charts  =============================-->
	<script src="../Public/bootstrap/js/charts/jquery.flot.js"></script>
	<script src="../Public/bootstrap/js/charts/jquery.flot.resize.js"></script>
	<script src="../Public/bootstrap/js/charts/jquery.flot.stack.js"></script>
	<script src="../Public/bootstrap/js/charts/jquery.flot.pie.min.js"></script>
	<script src="../Public/bootstrap/js/charts/jquery.sparkline.min.js"></script>
	<script src="../Public/bootstrap/js/jquery.knob.js"></script>


	<!-- NVD3 graphs  =============================-->
	<script src="../Public/bootstrap/js/nvd3/lib/d3.v3.js"></script>
	<script src="../Public/bootstrap/js/nvd3/nv.d3.js"></script>
	<script src="../Public/bootstrap/js/nvd3/src/models/legend.js"></script>
	<script src="../Public/bootstrap/js/nvd3/src/models/pie.js"></script>
	<script src="../Public/bootstrap/js/nvd3/src/models/pieChart.js"></script>
	<script src="../Public/bootstrap/js/nvd3/src/utils.js"></script>
	<script src="../Public/bootstrap/js/nvd3/sample.nvd3.js"></script>

	<!-- Map and icons on map-->
	<!--
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false"></script>
	<script src="../Public/bootstrap/js/map-icons.js"></script>
	-->

	<!-- Archon JS =============================-->
	<script src="../Public/bootstrap/js/archon.js"></script>
	<script src="../Public/bootstrap/js/knobs-custom.js"></script>
	<script src="../Public/bootstrap/js/sparkline-custom.js"></script>
	<script src="../Public/bootstrap/js/dashboard-custom.js"></script>



</body>


</html>