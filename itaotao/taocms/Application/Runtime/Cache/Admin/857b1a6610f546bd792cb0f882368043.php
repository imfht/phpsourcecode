<?php if (!defined('THINK_PATH')) exit();?><html><!DOCTYPE html>

<!--
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.1.1
Version: 2.0.2
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>Metronic | Admin Dashboard Template</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="/tp3.2/Public/static/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/bootstrap/css/bootstrap.min.css"  rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="/tp3.2/Public/static/gritter/css/jquery.gritter.css"  rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/fullcalendar/fullcalendar/fullcalendar.css"  rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/jqvmap/jqvmap/jqvmap.css"  rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/jquery-easy-pie-chart/jquery.easy-pie-chart.css"  rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGIN STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="/tp3.2/Public/static/css/style-metronic.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/style.css"  rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/style-responsive.css"  rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/plugins.css"  rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/pages/tasks.css"  rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="/tp3.2/Public/static/css/print.css"  rel="stylesheet" type="text/css" media="print"/>
    <link href="/tp3.2/Public/static/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="favicon.ico" />
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed">
<!-- BEGIN HEADER -->
<div class="header navbar navbar-fixed-top">
<!-- BEGIN TOP NAVIGATION BAR -->
<div class="header-inner">
<!-- BEGIN LOGO -->
<a class="navbar-brand" href="index.html" >
    <img src="/tp3.2/Public/static/img/logo.png"  alt="logo" class="img-responsive"/>
</a>
<!-- END LOGO -->
<!-- BEGIN RESPONSIVE MENU TOGGLER -->
<a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
    <img src="/tp3.2/Public/static/img/menu-toggler.png"  alt=""/>
</a>
<!-- END RESPONSIVE MENU TOGGLER -->
<!-- BEGIN TOP NAVIGATION MENU -->
<ul class="nav navbar-nav pull-right">
<!-- BEGIN NOTIFICATION DROPDOWN -->
<li class="dropdown" id="header_notification_bar">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <i class="fa fa-warning"></i>
					<span class="badge">
						 6
					</span>
    </a>
    <ul class="dropdown-menu extended notification">
        <li>
            <p>
                You have 14 new notifications
            </p>
        </li>
        <li>
            <ul class="dropdown-menu-list scroller" style="height: 250px;">
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-success">
										<i class="fa fa-plus"></i>
									</span>
                        New user registered.
									<span class="time">
										 Just now
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-danger">
										<i class="fa fa-bolt"></i>
									</span>
                        Server #12 overloaded.
									<span class="time">
										 15 mins
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-warning">
										<i class="fa fa-bell-o"></i>
									</span>
                        Server #2 not responding.
									<span class="time">
										 22 mins
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-info">
										<i class="fa fa-bullhorn"></i>
									</span>
                        Application error.
									<span class="time">
										 40 mins
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-danger">
										<i class="fa fa-bolt"></i>
									</span>
                        Database overloaded 68%.
									<span class="time">
										 2 hrs
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-danger">
										<i class="fa fa-bolt"></i>
									</span>
                        2 user IP blocked.
									<span class="time">
										 5 hrs
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-warning">
										<i class="fa fa-bell-o"></i>
									</span>
                        Storage Server #4 not responding.
									<span class="time">
										 45 mins
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-info">
										<i class="fa fa-bullhorn"></i>
									</span>
                        System Error.
									<span class="time">
										 55 mins
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-danger">
										<i class="fa fa-bolt"></i>
									</span>
                        Database overloaded 68%.
									<span class="time">
										 2 hrs
									</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="external">
            <a href="#">
                See all notifications <i class="m-icon-swapright"></i>
            </a>
        </li>
    </ul>
</li>
<!-- END NOTIFICATION DROPDOWN -->
<!-- BEGIN INBOX DROPDOWN -->
<li class="dropdown" id="header_inbox_bar">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <i class="fa fa-envelope"></i>
					<span class="badge">
						 5
					</span>
    </a>
    <ul class="dropdown-menu extended inbox">
        <li>
            <p>
                You have 12 new messages
            </p>
        </li>
        <li>
            <ul class="dropdown-menu-list scroller" style="height: 250px;">
                <li>
                    <a href="inbox.html-a=view.htm" >
									<span class="photo">
										<img src="/tp3.2/Public/static/img/avatar2.jpg"  alt=""/>
									</span>
									<span class="subject">
										<span class="from">
											 Lisa Wong
										</span>
										<span class="time">
											 Just Now
										</span>
									</span>
									<span class="message">
										 Vivamus sed auctor nibh congue nibh. auctor nibh auctor nibh...
									</span>
                    </a>
                </li>
                <li>
                    <a href="inbox.html-a=view.htm" >
									<span class="photo">
										<img src="/tp3.2/Public/static/img/avatar3.jpg" alt=""/>
									</span>
									<span class="subject">
										<span class="from">
											 Richard Doe
										</span>
										<span class="time">
											 16 mins
										</span>
									</span>
									<span class="message">
										 Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh...
									</span>
                    </a>
                </li>
                <li>
                    <a href="inbox.html-a=view.htm" >
									<span class="photo">
										<img src="/tp3.2/Public/static/img/avatar1.jpg"  alt=""/>
									</span>
									<span class="subject">
										<span class="from">
											 Bob Nilson
										</span>
										<span class="time">
											 2 hrs
										</span>
									</span>
									<span class="message">
										 Vivamus sed nibh auctor nibh congue nibh. auctor nibh auctor nibh...
									</span>
                    </a>
                </li>
                <li>
                    <a href="inbox.html-a=view.htm">
									<span class="photo">
										<img src="/tp3.2/Public/static/img/avatar2.jpg" alt=""/>
									</span>
									<span class="subject">
										<span class="from">
											 Lisa Wong
										</span>
										<span class="time">
											 40 mins
										</span>
									</span>
									<span class="message">
										 Vivamus sed auctor 40% nibh congue nibh...
									</span>
                    </a>
                </li>
                <li>
                    <a href="inbox.html-a=view.htm" >
									<span class="photo">
										<img src="/tp3.2/Public/static/img/avatar3.jpg"  alt=""/>
									</span>
									<span class="subject">
										<span class="from">
											 Richard Doe
										</span>
										<span class="time">
											 46 mins
										</span>
									</span>
									<span class="message">
										 Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh...
									</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="external">
            <a href="inbox.html" >
                See all messages <i class="m-icon-swapright"></i>
            </a>
        </li>
    </ul>
</li>
<!-- END INBOX DROPDOWN -->
<!-- BEGIN TODO DROPDOWN -->
<li class="dropdown" id="header_task_bar">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <i class="fa fa-tasks"></i>
					<span class="badge">
						 5
					</span>
    </a>
    <ul class="dropdown-menu extended tasks">
        <li>
            <p>
                You have 12 pending tasks
            </p>
        </li>
        <li>
            <ul class="dropdown-menu-list scroller" style="height: 250px;">
                <li>
                    <a href="#">
									<span class="task">
										<span class="desc">
											 New release v1.2
										</span>
										<span class="percent">
											 30%
										</span>
									</span>
									<span class="progress">
										<span style="width: 40%;" class="progress-bar progress-bar-success" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
											<span class="sr-only">
												 40% Complete
											</span>
										</span>
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="task">
										<span class="desc">
											 Application deployment
										</span>
										<span class="percent">
											 65%
										</span>
									</span>
									<span class="progress progress-striped">
										<span style="width: 65%;" class="progress-bar progress-bar-danger" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">
											<span class="sr-only">
												 65% Complete
											</span>
										</span>
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="task">
										<span class="desc">
											 Mobile app release
										</span>
										<span class="percent">
											 98%
										</span>
									</span>
									<span class="progress">
										<span style="width: 98%;" class="progress-bar progress-bar-success" aria-valuenow="98" aria-valuemin="0" aria-valuemax="100">
											<span class="sr-only">
												 98% Complete
											</span>
										</span>
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="task">
										<span class="desc">
											 Database migration
										</span>
										<span class="percent">
											 10%
										</span>
									</span>
									<span class="progress progress-striped">
										<span style="width: 10%;" class="progress-bar progress-bar-warning" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
											<span class="sr-only">
												 10% Complete
											</span>
										</span>
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="task">
										<span class="desc">
											 Web server upgrade
										</span>
										<span class="percent">
											 58%
										</span>
									</span>
									<span class="progress progress-striped">
										<span style="width: 58%;" class="progress-bar progress-bar-info" aria-valuenow="58" aria-valuemin="0" aria-valuemax="100">
											<span class="sr-only">
												 58% Complete
											</span>
										</span>
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="task">
										<span class="desc">
											 Mobile development
										</span>
										<span class="percent">
											 85%
										</span>
									</span>
									<span class="progress progress-striped">
										<span style="width: 85%;" class="progress-bar progress-bar-success" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">
											<span class="sr-only">
												 85% Complete
											</span>
										</span>
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="task">
										<span class="desc">
											 New UI release
										</span>
										<span class="percent">
											 18%
										</span>
									</span>
									<span class="progress progress-striped">
										<span style="width: 18%;" class="progress-bar progress-bar-important" aria-valuenow="18" aria-valuemin="0" aria-valuemax="100">
											<span class="sr-only">
												 18% Complete
											</span>
										</span>
									</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="external">
            <a href="#">
                See all tasks <i class="m-icon-swapright"></i>
            </a>
        </li>
    </ul>
</li>
<!-- END TODO DROPDOWN -->
<!-- BEGIN USER LOGIN DROPDOWN -->
<li class="dropdown user">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <img alt="" src="/tp3.2/Public/static/img/avatar1_small.jpg" />
					<span class="username">
						 <?php echo (session('nickname')); ?>
					</span>
        <i class="fa fa-angle-down"></i>
    </a>
    <ul class="dropdown-menu">
        <li>
            <a href="extra_profile.html" >
                <i class="fa fa-user"></i> My Profile
            </a>
        </li>
        <li>
            <a href="page_calendar.html"
            <i class="fa fa-calendar"></i> My Calendar
            </a>
        </li>
        <li>
            <a href="inbox.html" >
                <i class="fa fa-envelope"></i> My Inbox
							<span class="badge badge-danger">
								 3
							</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa fa-tasks"></i> My Tasks
							<span class="badge badge-success">
								 7
							</span>
            </a>
        </li>
        <li class="divider">
        </li>
        <li>
            <a href="javascript:;" id="trigger_fullscreen">
                <i class="fa fa-arrows"></i> Full Screen
            </a>
        </li>
        <li>
            <a href="extra_lock.html" >
                <i class="fa fa-lock"></i> Lock Screen
            </a>
        </li>
        <li>
            <a href="<?php echo U('Public/logout');?>" >
                <i class="fa fa-key"></i> Log Out
            </a>
        </li>
    </ul>
</li>
<!-- END USER LOGIN DROPDOWN -->
</ul>
<!-- END TOP NAVIGATION MENU -->
</div>
<!-- END TOP NAVIGATION BAR -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
<!-- BEGIN SIDEBAR -->
<div class="page-sidebar-wrapper">
<div class="page-sidebar navbar-collapse collapse">
<!-- add "navbar-no-scroll" class to disable the scrolling of the sidebar menu -->
<!-- BEGIN SIDEBAR MENU -->
    <ul class="page-sidebar-menu" data-auto-scroll="true" data-slide-speed="200">
<li class="sidebar-toggler-wrapper">
    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
    <div class="sidebar-toggler hidden-phone">
    </div>
    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
</li>
<li class="sidebar-search-wrapper">
    <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
    <form class="sidebar-search" action="http://www.keenthemes.com/preview/metronic_admin/extra_search.html" method="POST">
        <div class="form-container">
            <div class="input-box">
                <a href="javascript:;" class="remove">
                </a>
                <input type="text" placeholder="Search..."/>
                <input type="button" class="submit" value=" "/>
            </div>
        </div>
    </form>
    <!-- END RESPONSIVE QUICK SEARCH FORM -->
</li>
<li class="start ">
    <a href="index.html" >
        <i class="fa fa-home"></i>
						<span class="title">
							后台首页
						</span>
    </a>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-users"></i>
						<span class="title">
							用户管理
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="<?php echo U('User/index');?>" >
                <i class="fa fa-tag"></i>
                用户信息
            </a>
        </li>
        <li>
            <a href="<?php echo U('Auth/index');?>" >
                <i class="fa fa-gear"></i>
                权限管理
            </a>
        </li>
    </ul>
</li>
    <li>
        <a href="javascript:;">
            <i class="fa fa-cogs"></i>
						<span class="title">
							系统管理
						</span>
						<span class="arrow ">
						</span>
        </a>
        <ul class="sub-menu">
            <li>
                <a href="<?php echo U('Menu/index');?>" >
                    <i class="fa fa-tag"></i>
                    菜单管理
                </a>
            </li>
        </ul>
    </li>
</ul>
<!-- END SIDEBAR MENU -->
</div>
</div>
<!-- END SIDEBAR -->
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
<div class="page-content">
<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
<div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                Widget settings form goes here
            </div>
            <div class="modal-footer">
                <button type="button" class="btn blue">Save changes</button>
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
<!-- BEGIN STYLE CUSTOMIZER -->
<div class="theme-panel hidden-xs hidden-sm">
    <div class="toggler">
    </div>
    <div class="toggler-close">
    </div>
    <div class="theme-options">
        <div class="theme-option theme-colors clearfix">
						<span>
							 THEME COLOR
						</span>
            <ul>
                <li class="color-black current color-default" data-style="default">
                </li>
                <li class="color-blue" data-style="blue">
                </li>
                <li class="color-brown" data-style="brown">
                </li>
                <li class="color-purple" data-style="purple">
                </li>
                <li class="color-grey" data-style="grey">
                </li>
                <li class="color-white color-light" data-style="light">
                </li>
            </ul>
        </div>
        <div class="theme-option">
						<span>
							 Layout
						</span>
            <select class="layout-option form-control input-small">
                <option value="fluid" selected="selected">Fluid</option>
                <option value="boxed">Boxed</option>
            </select>
        </div>
        <div class="theme-option">
						<span>
							 Header
						</span>
            <select class="header-option form-control input-small">
                <option value="fixed" selected="selected">Fixed</option>
                <option value="default">Default</option>
            </select>
        </div>
        <div class="theme-option">
						<span>
							 Sidebar
						</span>
            <select class="sidebar-option form-control input-small">
                <option value="fixed">Fixed</option>
                <option value="default" selected="selected">Default</option>
            </select>
        </div>
        <div class="theme-option">
						<span>
							 Sidebar Position
						</span>
            <select class="sidebar-pos-option form-control input-small">
                <option value="left" selected="selected">Left</option>
                <option value="right">Right</option>
            </select>
        </div>
        <div class="theme-option">
						<span>
							 Footer
						</span>
            <select class="footer-option form-control input-small">
                <option value="fixed">Fixed</option>
                <option value="default" selected="selected">Default</option>
            </select>
        </div>
    </div>
</div>
<!-- END STYLE CUSTOMIZER -->
<!-- BEGIN PAGE HEADER-->
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PAGE TITLE & BREADCRUMB-->
        <h3 class="page-title">
            Dashboard <small>statistics and more</small>
        </h3>
        <ul class="page-breadcrumb breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="index.html" >
                    Home
                </a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                <a href="#">
                    Dashboard
                </a>
            </li>
            <li class="pull-right">
                <div id="dashboard-report-range" class="dashboard-date-range tooltips" data-placement="top" data-original-title="Change dashboard date range">
                    <i class="fa fa-calendar"></i>
								<span>
								</span>
                    <i class="fa fa-angle-down"></i>
                </div>
            </li>
        </ul>
        <!-- END PAGE TITLE & BREADCRUMB-->
    </div>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN DASHBOARD STATS -->
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat blue">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    1349
                </div>
                <div class="desc">
                    New Feedbacks
                </div>
            </div>
            <a class="more" href="#">
                View more <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat green">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    549
                </div>
                <div class="desc">
                    New Orders
                </div>
            </div>
            <a class="more" href="#">
                View more <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat purple">
            <div class="visual">
                <i class="fa fa-globe"></i>
            </div>
            <div class="details">
                <div class="number">
                    +89%
                </div>
                <div class="desc">
                    Brand Popularity
                </div>
            </div>
            <a class="more" href="#">
                View more <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat yellow">
            <div class="visual">
                <i class="fa fa-bar-chart-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    12,5M$
                </div>
                <div class="desc">
                    Total Profit
                </div>
            </div>
            <a class="more" href="#">
                View more <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
</div>
<!-- END DASHBOARD STATS -->
<div class="clearfix">
</div>
<div class="row">
    <div class="col-md-6 col-sm-6">
        <!-- BEGIN PORTLET-->
        <div class="portlet solid bordered light-grey">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bar-chart-o"></i>Site Visits
                </div>
                <div class="tools">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn default btn-sm active">
                            <input type="radio" name="options" class="toggle" id="option1">Users </label>
                        <label class="btn default btn-sm">
                            <input type="radio" name="options" class="toggle" id="option2">Feedbacks </label>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div id="site_statistics_loading">
                    <img src="/tp3.2/Public/static/img/loading.gif" />
                </div>
                <div id="site_statistics_content" class="display-none">
                    <div id="site_statistics" class="chart">
                    </div>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
    <div class="col-md-6 col-sm-6">
        <!-- BEGIN PORTLET-->
        <div class="portlet solid light-grey bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bullhorn"></i>Activities
                </div>
                <div class="tools">
                    <div class="btn-group pull-right" data-toggle="buttons">
                        <a href="" class="btn blue btn-sm active">
                            Users
                        </a>
                        <a href="" class="btn blue btn-sm">
                            Orders
                        </a>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div id="site_activities_loading">
                    <img src="/tp3.2/Public/static/img/loading.gif" />
                </div>
                <div id="site_activities_content" class="display-none">
                    <div id="site_activities" style="height: 100px;">
                    </div>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
        <!-- BEGIN PORTLET-->
        <div class="portlet solid bordered light-grey">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-signal"></i>Server Load
                </div>
                <div class="tools">
                    <div class="btn-group pull-right" data-toggle="buttons">
                        <a href="" class="btn red btn-sm active">
                            Database
                        </a>
                        <a href="" class="btn red btn-sm">
                            Web
                        </a>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div id="load_statistics_loading">
                    <img src="/tp3.2/Public/static/img/loading.gif" />
                </div>
                <div id="load_statistics_content" class="display-none">
                    <div id="load_statistics" style="height: 108px;">
                    </div>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
</div>
<div class="clearfix">
</div>
<div class="row ">
<div class="col-md-6 col-sm-6">
<div class="portlet box blue">
<div class="portlet-title">
    <div class="caption">
        <i class="fa fa-bell-o"></i>Recent Activities
    </div>
    <div class="actions">
        <div class="btn-group">
            <a class="btn btn-sm default" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                Filter By <i class="fa fa-angle-down"></i>
            </a>
            <div class="dropdown-menu hold-on-click dropdown-checkboxes pull-right">
                <label><input type="checkbox"/> Finance</label>
                <label><input type="checkbox" checked=""/> Membership</label>
                <label><input type="checkbox"/> Customer Support</label>
                <label><input type="checkbox" checked=""/> HR</label>
                <label><input type="checkbox"/> System</label>
            </div>
        </div>
    </div>
</div>
<div class="portlet-body">
<div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible="0">
<ul class="feeds">
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-info">
                    <i class="fa fa-check"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    You have 4 pending tasks.
														<span class="label label-sm label-warning ">
															 Take action <i class="fa fa-share"></i>
														</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            Just now
        </div>
    </div>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-success">
                        <i class="fa fa-bar-chart-o"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        Finance Report for year 2013 has been released.
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                20 mins
            </div>
        </div>
    </a>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-danger">
                    <i class="fa fa-user"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    You have 5 pending membership that requires a quick review.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            24 mins
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-info">
                    <i class="fa fa-shopping-cart"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received with
														<span class="label label-sm label-success">
															 Reference Number: DR23923
														</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            30 mins
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-success">
                    <i class="fa fa-user"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    You have 5 pending membership that requires a quick review.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            24 mins
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-default">
                    <i class="fa fa-bell-o"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    Web server hardware needs to be upgraded.
														<span class="label label-sm label-default ">
															 Overdue
														</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            2 hours
        </div>
    </div>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-default">
                        <i class="fa fa-briefcase"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        IPO Report for year 2013 has been released.
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                20 mins
            </div>
        </div>
    </a>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-info">
                    <i class="fa fa-check"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    You have 4 pending tasks.
														<span class="label label-sm label-warning ">
															 Take action <i class="fa fa-share"></i>
														</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            Just now
        </div>
    </div>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-danger">
                        <i class="fa fa-bar-chart-o"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        Finance Report for year 2013 has been released.
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                20 mins
            </div>
        </div>
    </a>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-default">
                    <i class="fa fa-user"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    You have 5 pending membership that requires a quick review.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            24 mins
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-info">
                    <i class="fa fa-shopping-cart"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received with
														<span class="label label-sm label-success">
															 Reference Number: DR23923
														</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            30 mins
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-success">
                    <i class="fa fa-user"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    You have 5 pending membership that requires a quick review.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            24 mins
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-warning">
                    <i class="fa fa-bell-o"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    Web server hardware needs to be upgraded.
														<span class="label label-sm label-default ">
															 Overdue
														</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            2 hours
        </div>
    </div>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-info">
                        <i class="fa fa-briefcase"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        IPO Report for year 2013 has been released.
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                20 mins
            </div>
        </div>
    </a>
</li>
</ul>
</div>
<div class="scroller-footer">
    <div class="pull-right">
        <a href="#">
            See All Records <i class="m-icon-swapright m-icon-gray"></i>
        </a>
        &nbsp;
    </div>
</div>
</div>
</div>
</div>
<div class="col-md-6 col-sm-6">
<div class="portlet box green tasks-widget">
<div class="portlet-title">
    <div class="caption">
        <i class="fa fa-check"></i>Tasks
    </div>
    <div class="tools">
        <a href="#portlet-config" data-toggle="modal" class="config">
        </a>
        <a href="" class="reload">
        </a>
    </div>
    <div class="actions">
        <div class="btn-group">
            <a class="btn default btn-xs" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                More <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#">
                        <i class="i"></i> All Project
                    </a>
                </li>
                <li class="divider">
                </li>
                <li>
                    <a href="#">
                        AirAsia
                    </a>
                </li>
                <li>
                    <a href="#">
                        Cruise
                    </a>
                </li>
                <li>
                    <a href="#">
                        HSBC
                    </a>
                </li>
                <li class="divider">
                </li>
                <li>
                    <a href="#">
                        Pending
												<span class="badge badge-important">
													 4
												</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        Completed
												<span class="badge badge-success">
													 12
												</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        Overdue
												<span class="badge badge-warning">
													 9
												</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="portlet-body">
<div class="task-content">
<div class="scroller" style="height: 305px;" data-always-visible="1" data-rail-visible1="1">
<!-- START TASK LIST -->
<ul class="task-list">
<li>
    <div class="task-checkbox">
        <input type="checkbox" class="liChild" value=""/>
    </div>
    <div class="task-title">
												<span class="task-title-sp">
													 Present 2013 Year IPO Statistics at Board Meeting
												</span>
												<span class="label label-sm label-success">
													 Company
												</span>
												<span class="task-bell">
													<i class="fa fa-bell-o"></i>
												</span>
    </div>
    <div class="task-config">
        <div class="task-config-btn btn-group">
            <a class="btn btn-xs default" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#">
                        <i class="fa fa-check"></i> Complete
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-trash-o"></i> Cancel
                    </a>
                </li>
            </ul>
        </div>
    </div>
</li>
<li>
    <div class="task-checkbox">
        <input type="checkbox" class="liChild" value=""/>
    </div>
    <div class="task-title">
												<span class="task-title-sp">
													 Hold An Interview for Marketing Manager Position
												</span>
												<span class="label label-sm label-danger">
													 Marketing
												</span>
    </div>
    <div class="task-config">
        <div class="task-config-btn btn-group">
            <a class="btn btn-xs default" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#">
                        <i class="fa fa-check"></i> Complete
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-trash-o"></i> Cancel
                    </a>
                </li>
            </ul>
        </div>
    </div>
</li>
<li>
    <div class="task-checkbox">
        <input type="checkbox" class="liChild" value=""/>
    </div>
    <div class="task-title">
												<span class="task-title-sp">
													 AirAsia Intranet System Project Internal Meeting
												</span>
												<span class="label label-sm label-success">
													 AirAsia
												</span>
												<span class="task-bell">
													<i class="fa fa-bell-o"></i>
												</span>
    </div>
    <div class="task-config">
        <div class="task-config-btn btn-group">
            <a class="btn btn-xs default" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#">
                        <i class="fa fa-check"></i> Complete
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-trash-o"></i> Cancel
                    </a>
                </li>
            </ul>
        </div>
    </div>
</li>
<li>
    <div class="task-checkbox">
        <input type="checkbox" class="liChild" value=""/>
    </div>
    <div class="task-title">
												<span class="task-title-sp">
													 Technical Management Meeting
												</span>
												<span class="label label-sm label-warning">
													 Company
												</span>
    </div>
    <div class="task-config">
        <div class="task-config-btn btn-group">
            <a class="btn btn-xs default" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#">
                        <i class="fa fa-check"></i> Complete
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-trash-o"></i> Cancel
                    </a>
                </li>
            </ul>
        </div>
    </div>
</li>
<li>
    <div class="task-checkbox">
        <input type="checkbox" class="liChild" value=""/>
    </div>
    <div class="task-title">
												<span class="task-title-sp">
													 Kick-off Company CRM Mobile App Development
												</span>
												<span class="label label-sm label-info">
													 Internal Products
												</span>
    </div>
    <div class="task-config">
        <div class="task-config-btn btn-group">
            <a class="btn btn-xs default" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#">
                        <i class="fa fa-check"></i> Complete
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-trash-o"></i> Cancel
                    </a>
                </li>
            </ul>
        </div>
    </div>
</li>
<li>
    <div class="task-checkbox">
        <input type="checkbox" class="liChild" value=""/>
    </div>
    <div class="task-title">
												<span class="task-title-sp">
													 Prepare Commercial Offer For SmartVision Website Rewamp
												</span>
												<span class="label label-sm label-danger">
													 SmartVision
												</span>
    </div>
    <div class="task-config">
        <div class="task-config-btn btn-group">
            <a class="btn btn-xs default" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#">
                        <i class="fa fa-check"></i> Complete
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-trash-o"></i> Cancel
                    </a>
                </li>
            </ul>
        </div>
    </div>
</li>
<li>
    <div class="task-checkbox">
        <input type="checkbox" class="liChild" value=""/>
    </div>
    <div class="task-title">
												<span class="task-title-sp">
													 Sign-Off The Comercial Agreement With AutoSmart
												</span>
												<span class="label label-sm label-default">
													 AutoSmart
												</span>
												<span class="task-bell">
													<i class="fa fa-bell-o"></i>
												</span>
    </div>
    <div class="task-config">
        <div class="task-config-btn btn-group">
            <a class="btn btn-xs default" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#">
                        <i class="fa fa-check"></i> Complete
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-trash-o"></i> Cancel
                    </a>
                </li>
            </ul>
        </div>
    </div>
</li>
<li>
    <div class="task-checkbox">
        <input type="checkbox" class="liChild" value=""/>
    </div>
    <div class="task-title">
												<span class="task-title-sp">
													 Company Staff Meeting
												</span>
												<span class="label label-sm label-success">
													 Cruise
												</span>
												<span class="task-bell">
													<i class="fa fa-bell-o"></i>
												</span>
    </div>
    <div class="task-config">
        <div class="task-config-btn btn-group">
            <a class="btn btn-xs default" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#">
                        <i class="fa fa-check"></i> Complete
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-trash-o"></i> Cancel
                    </a>
                </li>
            </ul>
        </div>
    </div>
</li>
<li class="last-line">
    <div class="task-checkbox">
        <input type="checkbox" class="liChild" value=""/>
    </div>
    <div class="task-title">
												<span class="task-title-sp">
													 KeenThemes Investment Discussion
												</span>
												<span class="label label-sm label-warning">
													 KeenThemes
												</span>
    </div>
    <div class="task-config">
        <div class="task-config-btn btn-group">
            <a class="btn btn-xs default" href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#">
                        <i class="fa fa-check"></i> Complete
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-trash-o"></i> Cancel
                    </a>
                </li>
            </ul>
        </div>
    </div>
</li>
</ul>
<!-- END START TASK LIST -->
</div>
</div>
<div class="task-footer">
								<span class="pull-right">
									<a href="#">
                                        See All Tasks <i class="m-icon-swapright m-icon-gray"></i>
                                    </a>
									 &nbsp;
								</span>
</div>
</div>
</div>
</div>
</div>
<div class="clearfix">
</div>
<div class="row ">
    <div class="col-md-6 col-sm-6">
        <div class="portlet box purple">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-calendar"></i>General Stats
                </div>
                <div class="actions">
                    <a href="javascript:;" class="btn btn-sm yellow easy-pie-chart-reload">
                        <i class="fa fa-repeat"></i> Reload
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="easy-pie-chart">
                            <div class="number transactions" data-percent="55">
											<span>
												 +55
											</span>
                                %
                            </div>
                            <a class="title" href="#">
                                Transactions <i class="m-icon-swapright"></i>
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm">
                    </div>
                    <div class="col-md-4">
                        <div class="easy-pie-chart">
                            <div class="number visits" data-percent="85">
											<span>
												 +85
											</span>
                                %
                            </div>
                            <a class="title" href="#">
                                New Visits <i class="m-icon-swapright"></i>
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm">
                    </div>
                    <div class="col-md-4">
                        <div class="easy-pie-chart">
                            <div class="number bounce" data-percent="46">
											<span>
												 -46
											</span>
                                %
                            </div>
                            <a class="title" href="#">
                                Bounce <i class="m-icon-swapright"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-calendar"></i>Server Stats
                </div>
                <div class="tools">
                    <a href="" class="collapse">
                    </a>
                    <a href="#portlet-config" data-toggle="modal" class="config">
                    </a>
                    <a href="" class="reload">
                    </a>
                    <a href="" class="remove">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="sparkline-chart">
                            <div class="number" id="sparkline_bar">
                            </div>
                            <a class="title" href="#">
                                Network <i class="m-icon-swapright"></i>
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm">
                    </div>
                    <div class="col-md-4">
                        <div class="sparkline-chart">
                            <div class="number" id="sparkline_bar2">
                            </div>
                            <a class="title" href="#">
                                CPU Load <i class="m-icon-swapright"></i>
                            </a>
                        </div>
                    </div>
                    <div class="margin-bottom-10 visible-sm">
                    </div>
                    <div class="col-md-4">
                        <div class="sparkline-chart">
                            <div class="number" id="sparkline_line">
                            </div>
                            <a class="title" href="#">
                                Load Rate <i class="m-icon-swapright"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix">
</div>
<div class="row ">
<div class="col-md-6 col-sm-6">
    <!-- BEGIN REGIONAL STATS PORTLET-->
    <div class="portlet">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-globe"></i>Regional Stats
            </div>
            <div class="tools">
                <a href="" class="collapse">
                </a>
                <a href="#portlet-config" data-toggle="modal" class="config">
                </a>
                <a href="" class="reload">
                </a>
                <a href="" class="remove">
                </a>
            </div>
        </div>
        <div class="portlet-body">
            <div id="region_statistics_loading">
                <img src="/tp3.2/Public/static/img/loading.gif" />
            </div>
            <div id="region_statistics_content" class="display-none">
                <div class="btn-toolbar margin-bottom-10">
                    <div class="btn-group" data-toggle="buttons">
                        <a href="" class="btn default btn-sm active">
                            Users
                        </a>
                        <a href="" class="btn default btn-sm">
                            Orders
                        </a>
                    </div>
                    <div class="btn-group pull-right">
                        <a href="" class="btn default btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            Select Region
											<span class="fa fa-angle-down">
											</span>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="javascript:;" id="regional_stat_world">
                                    World
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" id="regional_stat_usa">
                                    USA
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" id="regional_stat_europe">
                                    Europe
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" id="regional_stat_russia">
                                    Russia
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" id="regional_stat_germany">
                                    Germany
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="vmap_world" class="vmaps display-none">
                </div>
                <div id="vmap_usa" class="vmaps display-none">
                </div>
                <div id="vmap_europe" class="vmaps display-none">
                </div>
                <div id="vmap_russia" class="vmaps display-none">
                </div>
                <div id="vmap_germany" class="vmaps display-none">
                </div>
            </div>
        </div>
    </div>
    <!-- END REGIONAL STATS PORTLET-->
</div>
<div class="col-md-6 col-sm-6">
<!-- BEGIN PORTLET-->
<div class="portlet paddingless">
<div class="portlet-title line">
    <div class="caption">
        <i class="fa fa-bell-o"></i>Feeds
    </div>
    <div class="tools">
        <a href="" class="collapse">
        </a>
        <a href="#portlet-config" data-toggle="modal" class="config">
        </a>
        <a href="" class="reload">
        </a>
        <a href="" class="remove">
        </a>
    </div>
</div>
<div class="portlet-body">
<!--BEGIN TABS-->
<div class="tabbable tabbable-custom">
<ul class="nav nav-tabs">
    <li class="active">
        <a href="#tab_1_1" data-toggle="tab">
            System
        </a>
    </li>
    <li>
        <a href="#tab_1_2" data-toggle="tab">
            Activities
        </a>
    </li>
    <li>
        <a href="#tab_1_3" data-toggle="tab">
            Recent Users
        </a>
    </li>
</ul>
<div class="tab-content">
<div class="tab-pane active" id="tab_1_1">
<div class="scroller" style="height: 290px;" data-always-visible="1" data-rail-visible="0">
<ul class="feeds">
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-success">
                    <i class="fa fa-bell-o"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    You have 4 pending tasks.
																	<span class="label label-sm label-danger ">
																		 Take action <i class="fa fa-share"></i>
																	</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            Just now
        </div>
    </div>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-success">
                        <i class="fa fa-bell-o"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        New version v1.4 just lunched!
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                20 mins
            </div>
        </div>
    </a>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-danger">
                    <i class="fa fa-bolt"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    Database server #12 overloaded. Please fix the issue.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            24 mins
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-info">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            30 mins
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-success">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            40 mins
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-warning">
                    <i class="fa fa-plus"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New user registered.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            1.5 hours
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-success">
                    <i class="fa fa-bell-o"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    Web server hardware needs to be upgraded.
																	<span class="label label-sm label-default ">
																		 Overdue
																	</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            2 hours
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-default">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            3 hours
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-warning">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            5 hours
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-info">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            18 hours
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-default">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            21 hours
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-info">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            22 hours
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-default">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            21 hours
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-info">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            22 hours
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-default">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            21 hours
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-info">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            22 hours
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-default">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            21 hours
        </div>
    </div>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-info">
                    <i class="fa fa-bullhorn"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    New order received. Please take care of it.
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            22 hours
        </div>
    </div>
</li>
</ul>
</div>
</div>
<div class="tab-pane" id="tab_1_2">
<div class="scroller" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
<ul class="feeds">
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-success">
                        <i class="fa fa-bell-o"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        New user registered
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                Just now
            </div>
        </div>
    </a>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-success">
                        <i class="fa fa-bell-o"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        New order received
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                10 mins
            </div>
        </div>
    </a>
</li>
<li>
    <div class="col1">
        <div class="cont">
            <div class="cont-col1">
                <div class="label label-sm label-danger">
                    <i class="fa fa-bolt"></i>
                </div>
            </div>
            <div class="cont-col2">
                <div class="desc">
                    Order #24DOP4 has been rejected.
																	<span class="label label-sm label-danger ">
																		 Take action <i class="fa fa-share"></i>
																	</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col2">
        <div class="date">
            24 mins
        </div>
    </div>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-success">
                        <i class="fa fa-bell-o"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        New user registered
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                Just now
            </div>
        </div>
    </a>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-success">
                        <i class="fa fa-bell-o"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        New user registered
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                Just now
            </div>
        </div>
    </a>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-success">
                        <i class="fa fa-bell-o"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        New user registered
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                Just now
            </div>
        </div>
    </a>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-success">
                        <i class="fa fa-bell-o"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        New user registered
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                Just now
            </div>
        </div>
    </a>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-success">
                        <i class="fa fa-bell-o"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        New user registered
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                Just now
            </div>
        </div>
    </a>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-success">
                        <i class="fa fa-bell-o"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        New user registered
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                Just now
            </div>
        </div>
    </a>
</li>
<li>
    <a href="#">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm label-success">
                        <i class="fa fa-bell-o"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc">
                        New user registered
                    </div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date">
                Just now
            </div>
        </div>
    </a>
</li>
</ul>
</div>
</div>
<div class="tab-pane" id="tab_1_3">
<div class="scroller" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
<div class="row">
    <div class="col-md-6 user-info">
        <img alt="" src="/tp3.2/Public/static/img/avatar.png" />
        <div class="details">
            <div>
                <a href="#">
                    Robert Nilson
                </a>
															<span class="label label-sm label-success label-mini">
																 Approved
															</span>
            </div>
            <div>
                29 Jan 2013 10:45AM
            </div>
        </div>
    </div>
    <div class="col-md-6 user-info">
        <img alt="" src="/tp3.2/Public/static/img/avatar.png" />
        <div class="details">
            <div>
                <a href="#">
                    Lisa Miller
                </a>
															<span class="label label-sm label-info">
																 Pending
															</span>
            </div>
            <div>
                19 Jan 2013 10:45AM
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 user-info">
        <img alt="" src="/tp3.2/Public/static/img/avatar.png" />
        <div class="details">
            <div>
                <a href="#">
                    Eric Kim
                </a>
															<span class="label label-sm label-info">
																 Pending
															</span>
            </div>
            <div>
                19 Jan 2013 12:45PM
            </div>
        </div>
    </div>
    <div class="col-md-6 user-info">
        <img alt="" src="/tp3.2/Public/static/img/avatar.png" />
        <div class="details">
            <div>
                <a href="#">
                    Lisa Miller
                </a>
															<span class="label label-sm label-danger">
																 In progress
															</span>
            </div>
            <div>
                19 Jan 2013 11:55PM
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 user-info">
        <img alt="" src="/tp3.2/Public/static/img/avatar.png" />
        <div class="details">
            <div>
                <a href="#">
                    Eric Kim
                </a>
															<span class="label label-sm label-info">
																 Pending
															</span>
            </div>
            <div>
                19 Jan 2013 12:45PM
            </div>
        </div>
    </div>
    <div class="col-md-6 user-info">
        <img alt="" src="/tp3.2/Public/static/img/avatar.png" />
        <div class="details">
            <div>
                <a href="#">
                    Lisa Miller
                </a>
															<span class="label label-sm label-danger">
																 In progress
															</span>
            </div>
            <div>
                19 Jan 2013 11:55PM
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 user-info">
        <img alt="" src="/tp3.2/Public/static/img/avatar.png" />
        <div class="details">
            <div>
                <a href="#">
                    Eric Kim
                </a>
															<span class="label label-sm label-info">
																 Pending
															</span>
            </div>
            <div>
                19 Jan 2013 12:45PM
            </div>
        </div>
    </div>
    <div class="col-md-6 user-info">
        <img alt="" src="/tp3.2/Public/static/img/avatar.png" />
        <div class="details">
            <div>
                <a href="#">
                    Lisa Miller
                </a>
															<span class="label label-sm label-danger">
																 In progress
															</span>
            </div>
            <div>
                19 Jan 2013 11:55PM
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 user-info">
        <img alt="" src="/tp3.2/Public/static/img/avatar.png" />
        <div class="details">
            <div>
                <a href="#">
                    Eric Kim
                </a>
															<span class="label label-sm label-info">
																 Pending
															</span>
            </div>
            <div>
                19 Jan 2013 12:45PM
            </div>
        </div>
    </div>
    <div class="col-md-6 user-info">
        <img alt="" src="/tp3.2/Public/static/img/avatar.png" />
        <div class="details">
            <div>
                <a href="#">
                    Lisa Miller
                </a>
															<span class="label label-sm label-danger">
																 In progress
															</span>
            </div>
            <div>
                19 Jan 2013 11:55PM
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 user-info">
        <img alt="" src="/tp3.2/Public/static/img/avatar.png" />
        <div class="details">
            <div>
                <a href="#">
                    Eric Kim
                </a>
															<span class="label label-sm label-info">
																 Pending
															</span>
            </div>
            <div>
                19 Jan 2013 12:45PM
            </div>
        </div>
    </div>
    <div class="col-md-6 user-info">
        <img alt="" src="/tp3.2/Public/static/img/avatar.png" />
        <div class="details">
            <div>
                <a href="#">
                    Lisa Miller
                </a>
															<span class="label label-sm label-danger">
																 In progress
															</span>
            </div>
            <div>
                19 Jan 2013 11:55PM
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</div>
<!--END TABS-->
</div>
</div>
<!-- END PORTLET-->
</div>
</div>
<div class="clearfix">
</div>
<div class="row ">
    <div class="col-md-6 col-sm-6">
        <!-- BEGIN PORTLET-->
        <div class="portlet box blue calendar">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-calendar"></i>Calendar
                </div>
            </div>
            <div class="portlet-body light-grey">
                <div id="calendar">
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
    <div class="col-md-6 col-sm-6">
        <!-- BEGIN PORTLET-->
        <div class="portlet">
            <div class="portlet-title line">
                <div class="caption">
                    <i class="fa fa-comments"></i>Chats
                </div>
                <div class="tools">
                    <a href="" class="collapse">
                    </a>
                    <a href="#portlet-config" data-toggle="modal" class="config">
                    </a>
                    <a href="" class="reload">
                    </a>
                    <a href="" class="remove">
                    </a>
                </div>
            </div>
            <div class="portlet-body" id="chats">
                <div class="scroller" style="height: 435px;" data-always-visible="1" data-rail-visible1="1">
                    <ul class="chats">
                        <li class="in">
                            <img class="avatar img-responsive" alt="" src="/tp3.2/Public/static/img/avatar1.jpg" />
                            <div class="message">
											<span class="arrow">
											</span>
                                <a href="#" class="name">
                                    Bob Nilson
                                </a>
											<span class="datetime">
												 at Jul 25, 2012 11:09
											</span>
											<span class="body">
												 Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
											</span>
                            </div>
                        </li>
                        <li class="out">
                            <img class="avatar img-responsive" alt="" src="/tp3.2/Public/static/img/avatar2.jpg" />
                            <div class="message">
											<span class="arrow">
											</span>
                                <a href="#" class="name">
                                    Lisa Wong
                                </a>
											<span class="datetime">
												 at Jul 25, 2012 11:09
											</span>
											<span class="body">
												 Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
											</span>
                            </div>
                        </li>
                        <li class="in">
                            <img class="avatar img-responsive" alt="" src="/tp3.2/Public/static/img/avatar1.jpg" />
                            <div class="message">
											<span class="arrow">
											</span>
                                <a href="#" class="name">
                                    Bob Nilson
                                </a>
											<span class="datetime">
												 at Jul 25, 2012 11:09
											</span>
											<span class="body">
												 Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
											</span>
                            </div>
                        </li>
                        <li class="out">
                            <img class="avatar img-responsive" alt="" src="/tp3.2/Public/static/img/avatar3.jpg" />
                            <div class="message">
											<span class="arrow">
											</span>
                                <a href="#" class="name">
                                    Richard Doe
                                </a>
											<span class="datetime">
												 at Jul 25, 2012 11:09
											</span>
											<span class="body">
												 Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
											</span>
                            </div>
                        </li>
                        <li class="in">
                            <img class="avatar img-responsive" alt="" src="/tp3.2/Public/static/img/avatar3.jpg" />
                            <div class="message">
											<span class="arrow">
											</span>
                                <a href="#" class="name">
                                    Richard Doe
                                </a>
											<span class="datetime">
												 at Jul 25, 2012 11:09
											</span>
											<span class="body">
												 Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
											</span>
                            </div>
                        </li>
                        <li class="out">
                            <img class="avatar img-responsive" alt="" src="/tp3.2/Public/static/img/avatar1.jpg" />
                            <div class="message">
											<span class="arrow">
											</span>
                                <a href="#" class="name">
                                    Bob Nilson
                                </a>
											<span class="datetime">
												 at Jul 25, 2012 11:09
											</span>
											<span class="body">
												 Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
											</span>
                            </div>
                        </li>
                        <li class="in">
                            <img class="avatar img-responsive" alt="" src="/tp3.2/Public/static/img/avatar3.jpg" />
                            <div class="message">
											<span class="arrow">
											</span>
                                <a href="#" class="name">
                                    Richard Doe
                                </a>
											<span class="datetime">
												 at Jul 25, 2012 11:09
											</span>
											<span class="body">
												 Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
											</span>
                            </div>
                        </li>
                        <li class="out">
                            <img class="avatar img-responsive" alt="" src="/tp3.2/Public/static/img/avatar1.jpg" />
                            <div class="message">
											<span class="arrow">
											</span>
                                <a href="#" class="name">
                                    Bob Nilson
                                </a>
											<span class="datetime">
												 at Jul 25, 2012 11:09
											</span>
											<span class="body">
												 Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. sed diam nonummy nibh euismod tincidunt ut laoreet.
											</span>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="chat-form">
                    <div class="input-cont">
                        <input class="form-control" type="text" placeholder="Type a message here..."/>
                    </div>
                    <div class="btn-cont">
									<span class="arrow">
									</span>
                        <a href="" class="btn blue icn-only">
                            <i class="fa fa-check icon-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
</div>
</div>
</div>
<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="footer">
    <div class="footer-inner">
        2014 &copy; Metronic by keenthemes.
    </div>
    <div class="footer-tools">
		<span class="go-top">
			<i class="fa fa-angle-up"></i>
		</span>
    </div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="/tp3.2/Public/static/respond.min.js"></script>
<script src="/tp3.2/Public/static/excanvas.min.js"></script>
<![endif]-->
<script src="/tp3.2/Public/static/jquery-1.10.2.min.js" ></script>
<script src="/tp3.2/Public/static/jquery-migrate-1.2.1.min.js" ></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="/tp3.2/Public/static/jquery-ui/jquery-ui-1.10.3.custom.min.js" ></script>
<script src="/tp3.2/Public/static/bootstrap/js/bootstrap.min.js" ></script>
<script src="/tp3.2/Public/static/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" ></script>
<script src="/tp3.2/Public/static/jquery-slimscroll/jquery.slimscroll.min.js" ></script>
<script src="/tp3.2/Public/static/jquery.blockui.min.js" ></script>
<script src="/tp3.2/Public/static/jquery.cokie.min.js" ></script>
<script src="/tp3.2/Public/static/uniform/jquery.uniform.min.js" ></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="/tp3.2/Public/static/jqvmap/jqvmap/jquery.vmap.js" ></script>
<script src="/tp3.2/Public/static/jqvmap/jqvmap/maps/jquery.vmap.russia.js" ></script>
<script src="/tp3.2/Public/static/jqvmap/jqvmap/maps/jquery.vmap.world.js" ></script>
<script src="/tp3.2/Public/static/jqvmap/jqvmap/maps/jquery.vmap.europe.js" ></script>
<script src="/tp3.2/Public/static/jqvmap/jqvmap/maps/jquery.vmap.germany.js" ></script>
<script src="/tp3.2/Public/static/jqvmap/jqvmap/maps/jquery.vmap.usa.js" ></script>
<script src="/tp3.2/Public/static/jqvmap/jqvmap/data/jquery.vmap.sampledata.js" ></script>
<script src="/tp3.2/Public/static/flot/jquery.flot.min.js" ></script>
<script src="/tp3.2/Public/static/flot/jquery.flot.resize.min.js" ></script>
<script src="/tp3.2/Public/static/flot/jquery.flot.categories.min.js" ></script>
<script src="/tp3.2/Public/static/jquery.pulsate.min.js" ></script>
<script src="/tp3.2/Public/static/bootstrap-daterangepicker/moment.min.js" ></script>
<script src="/tp3.2/Public/static/bootstrap-daterangepicker/daterangepicker.js" ></script>
<script src="/tp3.2/Public/static/gritter/js/jquery.gritter.js" ></script>
<!-- IMPORTANT! fullcalendar depends on jquery-ui-1.10.3.custom.min.js for drag & drop support -->
<script src="/tp3.2/Public/static/fullcalendar/fullcalendar/fullcalendar.min.js" ></script>
<script src="/tp3.2/Public/static/jquery-easy-pie-chart/jquery.easy-pie-chart.js" ></script>
<script src="/tp3.2/Public/static/jquery.sparkline.min.js" ></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="/tp3.2/Public/static/scripts/core/app.js" ></script>
<script src="/tp3.2/Public/static/scripts/custom/index.js" ></script>
<script src="/tp3.2/Public/static/scripts/custom/tasks.js" ></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
    jQuery(document).ready(function() {
        App.init(); // initlayout and core plugins
        Index.init();
        Index.initJQVMAP(); // init index page's custom scripts
        Index.initCalendar(); // init index page's custom scripts
        Index.initCharts(); // init index page's custom scripts
        Index.initChat();
        Index.initMiniCharts();
        Index.initDashboardDaterange();
        Index.initIntro();
        Tasks.initDashboardWidget();
    });
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>