<?php if (!defined('THINK_PATH')) exit();?><html><!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>Metronic | Form Stuff - Form Validation</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="/tp3.2/Public/static/font-awesome/css/font-awesome.min.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/bootstrap/css/bootstrap.min.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/uniform/css/uniform.default.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME STYLES -->
    <link href="/tp3.2/Public/static/css/style-metronic.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/css/style-metronic.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/style.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/style-responsive.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/plugins.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="/tp3.2/Public/static/css/themes/default.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="/tp3.2/Public/static/css/custom.css" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="favicon.ico" tppabs="http://www.keenthemes.com/preview/metronic_admin/favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed">
<!-- BEGIN HEADER -->
<div class="header navbar navbar-fixed-top">
<!-- BEGIN TOP NAVIGATION BAR -->
<div class="header-inner">
<!-- BEGIN LOGO -->
<a class="navbar-brand" href="index.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/index.html">
    <img src="/tp3.2/Public/static/img/logo.png" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/img/logo.png" alt="logo" class="img-responsive"/>
</a>
<!-- END LOGO -->
<!-- BEGIN RESPONSIVE MENU TOGGLER -->
<a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
    <img src="/tp3.2/Public/static/img/menu-toggler.png" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/img/menu-toggler.png" alt=""/>
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
									<span class="label label-icon label-success">
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
									<span class="label label-icon label-danger">
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
									<span class="label label-icon label-warning">
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
									<span class="label label-icon label-info">
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
									<span class="label label-icon label-danger">
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
									<span class="label label-icon label-danger">
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
									<span class="label label-icon label-warning">
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
									<span class="label label-icon label-info">
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
									<span class="label label-icon label-danger">
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
                    <a href="inbox.html-a=view.htm" tppabs="http://www.keenthemes.com/preview/metronic_admin/inbox.html?a=view">
									<span class="photo">
										<img src="/tp3.2/Public/static/img/avatar2.jpg" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/img/avatar2.jpg" alt=""/>
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
                    <a href="inbox.html-a=view.htm" tppabs="http://www.keenthemes.com/preview/metronic_admin/inbox.html?a=view">
									<span class="photo">
										<img src="/tp3.2/Public/static/img/avatar3.jpg" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/img/avatar3.jpg" alt=""/>
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
                    <a href="inbox.html-a=view.htm" tppabs="http://www.keenthemes.com/preview/metronic_admin/inbox.html?a=view">
									<span class="photo">
										<img src="/tp3.2/Public/static/img/avatar1.jpg" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/img/avatar1.jpg" alt=""/>
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
                    <a href="inbox.html-a=view.htm" tppabs="http://www.keenthemes.com/preview/metronic_admin/inbox.html?a=view">
									<span class="photo">
										<img src="/tp3.2/Public/static/img/avatar2.jpg" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/img/avatar2.jpg" alt=""/>
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
                    <a href="inbox.html-a=view.htm" tppabs="http://www.keenthemes.com/preview/metronic_admin/inbox.html?a=view">
									<span class="photo">
										<img src="/tp3.2/Public/static/img/avatar3.jpg" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/img/avatar3.jpg" alt=""/>
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
            <a href="inbox.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/inbox.html">
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
        <img alt="" src="/tp3.2/Public/static/img/avatar1_small.jpg" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/img/avatar1_small.jpg"/>
					<span class="username">
						 Bob Nilson
					</span>
        <i class="fa fa-angle-down"></i>
    </a>
    <ul class="dropdown-menu">
        <li>
            <a href="extra_profile.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_profile.html">
                <i class="fa fa-user"></i> My Profile
            </a>
        </li>
        <li>
            <a href="page_calendar.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/page_calendar.html">
                <i class="fa fa-calendar"></i> My Calendar
            </a>
        </li>
        <li>
            <a href="inbox.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/inbox.html">
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
            <a href="extra_lock.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_lock.html">
                <i class="fa fa-lock"></i> Lock Screen
            </a>
        </li>
        <li>
            <a href="login.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/login.html">
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
            Form Validation <small>form validation</small>
        </h3>
        <ul class="page-breadcrumb breadcrumb">
            <li class="btn-group">
                <button type="button" class="btn blue dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
							<span>
								Actions
							</span>
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li>
                        <a href="#">
                            Action
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            Another action
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            Something else here
                        </a>
                    </li>
                    <li class="divider">
                    </li>
                    <li>
                        <a href="#">
                            Separated link
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <i class="fa fa-home"></i>
                <a href="index.html" >
                    Home
                </a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                <a href="#">
                    Form Stuff
                </a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                <a href="#">
                    Form Validation
                </a>
            </li>
        </ul>
        <!-- END PAGE TITLE & BREADCRUMB-->
    </div>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN VALIDATION STATES-->
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder"></i>新增用户
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                    <a href="#portlet-config" data-toggle="modal" class="config">
                    </a>
                    <a href="javascript:;" class="reload">
                    </a>
                    <a href="javascript:;" class="remove">
                    </a>
                </div>
            </div>
            <div class="portlet-body form">
                <!-- BEGIN FORM-->
                <form  id="add_user" method="post" class="form-horizontal" action="#">
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button>
                            请检查出现的错误,修改后再提交！
                        </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button>
                            验证通过!
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">用户名
										<span class="required">
											 *
										</span>
                            </label>
                            <div class="col-md-4">
                                <input type="text" name="username" data-required="1" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">密码
										<span class="required">
											 *
										</span>
                            </label>
                            <div class="col-md-4">
                                <input type="password" name="password" class="form-control" id="password"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">确认密码
										<span class="required">
											 *
										</span>
                            </label>
                            <div class="col-md-4">
                                <input type="password" name="confirm_password" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Email
										<span class="required">
											 *
										</span>
                            </label>
                            <div class="col-md-4">
                                <input name="email" type="text" class="form-control"/>
                                		<span class="help-block">
												 e.g:123456@qq.com
										</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">手机号
                            </label>
                            <div class="col-md-4">
                                <input name="mobile" type="text" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">状态
										<span class="required">
											 *
										</span>
                            </label>
                            <div class="col-md-4">
                                <div data-error-container="#form_2_membership_error" class="radio-list">
                                    <label>
                                        <input type="radio" value="1" name="status" checked>
                                        开启 </label>
                                    <label>
                                        <input type="radio" value="2" name="status">
                                        封禁 </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions fluid">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green">提交</button>
                            <button type="reset" class="btn default">取消</button>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
        <!-- END VALIDATION STATES-->
    </div>
</div>
<!-- END PAGE CONTENT-->
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
<script src="/tp3.2/Public/static/jquery-1.10.2.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="/tp3.2/Public/static/jquery-migrate-1.2.1.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="/tp3.2/Public/static/bootstrap/js/bootstrap.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/tp3.2/Public/static/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="/tp3.2/Public/static/jquery-slimscroll/jquery.slimscroll.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin//tp3.2/Public/static/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="/tp3.2/Public/static/jquery.blockui.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="/tp3.2/Public/static/jquery-validation/dist/jquery.validate.min.js" ></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL STYLES -->
<script src="/tp3.2/Public/Admin/js/jQuery.md5.js"></script>
<script src="/tp3.2/Public/static/scripts/core/app.js" ></script>
<script src="/tp3.2/Public/static/bootbox/bootbox.min.js"  type="text/javascript"></script>
<script src="/tp3.2/Public/Admin/js/user-validation.js" ></script>
<!-- END PAGE LEVEL STYLES -->
<script>
    jQuery(document).ready(function() {
        // initiate layout and plugins
        App.init();
        FormValidation.init();
    });
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>