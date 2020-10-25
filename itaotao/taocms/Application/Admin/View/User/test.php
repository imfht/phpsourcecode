
<html><!DOCTYPE html>

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
    <title>Metronic | Data Tables - Managed Datatables</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/font-awesome/css/font-awesome.min.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/uniform/css/uniform.default.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/select2/select2.css"/>
    <link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2-metronic.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/select2/select2-metronic.css"/>
    <link rel="stylesheet" href="assets/plugins/data-tables/DT_bootstrap.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/data-tables/DT_bootstrap.css"/>
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="assets/css/style-metronic.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/style.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/style-responsive.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/plugins.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/themes/default.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="assets/css/custom.css" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/css/custom.css" rel="stylesheet" type="text/css"/>
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
    <img src="assets/img/logo.png" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/img/logo.png" alt="logo" class="img-responsive"/>
</a>
<!-- END LOGO -->
<!-- BEGIN RESPONSIVE MENU TOGGLER -->
<a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
    <img src="assets/img/menu-toggler.png" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/img/menu-toggler.png" alt=""/>
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
                    <a href="inbox.html-a=view.htm" tppabs="http://www.keenthemes.com/preview/metronic_admin/inbox.html?a=view">
									<span class="photo">
										<img src="assets/img/avatar2.jpg" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/img/avatar2.jpg" alt=""/>
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
										<img src="assets/img/avatar3.jpg" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/img/avatar3.jpg" alt=""/>
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
										<img src="assets/img/avatar1.jpg" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/img/avatar1.jpg" alt=""/>
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
										<img src="assets/img/avatar2.jpg" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/img/avatar2.jpg" alt=""/>
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
										<img src="assets/img/avatar3.jpg" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/img/avatar3.jpg" alt=""/>
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
        <img alt="" src="assets/img/avatar1_small.jpg" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/img/avatar1_small.jpg"/>
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
    <a href="index.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/index.html">
        <i class="fa fa-home"></i>
						<span class="title">
							Dashboard
						</span>
    </a>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-shopping-cart"></i>
						<span class="title">
							E-Commerce
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="ecommerce_index.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ecommerce_index.html">
                <i class="fa fa-bullhorn"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="ecommerce_orders.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ecommerce_orders.html">
                <i class="fa fa-shopping-cart"></i>
                Orders
            </a>
        </li>
        <li>
            <a href="ecommerce_orders_view.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ecommerce_orders_view.html">
                <i class="fa fa-tags"></i>
                Order View
            </a>
        </li>
        <li>
            <a href="ecommerce_products.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ecommerce_products.html">
                <i class="fa fa-sitemap"></i>
                Products
            </a>
        </li>
        <li>
            <a href="ecommerce_products_edit.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ecommerce_products_edit.html">
                <i class="fa fa-file-o"></i>
                Product Edit
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-gift"></i>
						<span class="title">
							Frontend Themes
						</span>
						<span class="arrow">
						</span>
    </a>
    <ul class="sub-menu">
        <li class="tooltips" data-container="body" data-placement="right" data-html="true" data-original-title="Complete E-Commerce Frontend Theme For Metronic Admin">
            <a href="javascript:if(confirm('http://keenthemes.com/preview/index.php?theme=metronic_ecommerce  \n\n�ļ���δ�� Teleport Pro ȡ�أ���Ϊ �������·��������ʼ��ַ�����õķ�Χ��  \n\n��Ҫ�ӷ������ϴ�����'))window.location='http://keenthemes.com/preview/index.php?theme=metronic_ecommerce'" tppabs="http://keenthemes.com/preview/index.php?theme=metronic_ecommerce" target="_blank">
								<span class="title">
									E-Commerce Frontend
								</span>
            </a>
        </li>
        <li class="tooltips" data-container="body" data-placement="right" data-html="true" data-original-title="Complete Multipurpose Corporate Frontend Theme For Metronic Admin">
            <a href="javascript:if(confirm('http://keenthemes.com/preview/index.php?theme=metronic_frontend  \n\n�ļ���δ�� Teleport Pro ȡ�أ���Ϊ �������·��������ʼ��ַ�����õķ�Χ��  \n\n��Ҫ�ӷ������ϴ�����'))window.location='http://keenthemes.com/preview/index.php?theme=metronic_frontend'" tppabs="http://keenthemes.com/preview/index.php?theme=metronic_frontend" target="_blank">
								<span class="title">
									Corporate Frontend
								</span>
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-cogs"></i>
						<span class="title">
							Page Layouts
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="index_horizontal_menu.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/index_horizontal_menu.html">
                Dashboard & Mega Menu
            </a>
        </li>
        <li>
            <a href="layout_horizontal_sidebar_menu.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_horizontal_sidebar_menu.html">
                Horizontal & Sidebar Menu
            </a>
        </li>
        <li>
            <a href="layout_horizontal_menu1.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_horizontal_menu1.html">
                Horizontal Mega Menu 1
            </a>
        </li>
        <li>
            <a href="layout_horizontal_menu2.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_horizontal_menu2.html">
                Horizontal Mega Menu 2
            </a>
        </li>
        <li>
            <a href="layout_search_on_header.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_search_on_header.html">
                Searchbar On Header
            </a>
        </li>
        <li>
            <a href="layout_sidebar_toggler_on_header.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_sidebar_toggler_on_header.html">
                Sidebar Toggler On Header
            </a>
        </li>
        <li>
            <a href="layout_sidebar_reversed.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_sidebar_reversed.html">
								<span class="badge badge-roundless badge-success">
									new
								</span>
                Right Sidebar Page
            </a>
        </li>
        <li>
            <a href="layout_sidebar_fixed.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_sidebar_fixed.html">
                Sidebar Fixed Page
            </a>
        </li>
        <li>
            <a href="layout_sidebar_closed.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_sidebar_closed.html">
                Sidebar Closed Page
            </a>
        </li>
        <li>
            <a href="layout_ajax.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_ajax.html">
                Content Loading via Ajax
            </a>
        </li>
        <li>
            <a href="layout_disabled_menu.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_disabled_menu.html">
                Disabled Menu Links
            </a>
        </li>
        <li>
            <a href="layout_blank_page.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_blank_page.html">
                Blank Page
            </a>
        </li>
        <li>
            <a href="layout_boxed_page.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_boxed_page.html">
                Boxed Page
            </a>
        </li>
        <li>
            <a href="layout_language_bar.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_language_bar.html">
                Language Switch Bar
            </a>
        </li>
        <li>
            <a href="layout_promo.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/layout_promo.html">
                Promo Page
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-bookmark-o"></i>
						<span class="title">
							UI Features
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="ui_general.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_general.html">
                General
            </a>
        </li>
        <li>
            <a href="ui_buttons.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_buttons.html">
                Buttons & Icons
            </a>
        </li>
        <li>
            <a href="ui_typography.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_typography.html">
                Typography
            </a>
        </li>
        <li>
            <a href="ui_tabs_accordions_navs.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_tabs_accordions_navs.html">
                Tabs, Accordions & Navs
            </a>
        </li>
        <li>
            <a href="ui_tree.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_tree.html">
								<span class="badge badge-roundless badge-important">
									new
								</span>
                Tree View
            </a>
        </li>
        <li>
            <a href="ui_page_progress_style_1.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_page_progress_style_1.html">
								<span class="badge badge-roundless badge-warning">
									new
								</span>
                Page Progress Bar
            </a>
        </li>
        <li>
            <a href="ui_blockui.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_blockui.html">
                Block UI
            </a>
        </li>
        <li>
            <a href="ui_notific8.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_notific8.html">
                Notific8 Notifications
            </a>
        </li>
        <li>
            <a href="ui_toastr.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_toastr.html">
                Toastr Notifications
            </a>
        </li>
        <li>
            <a href="ui_alert_dialog_api.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_alert_dialog_api.html">
								<span class="badge badge-roundless badge-important">
									new
								</span>
                Alerts & Dialogs API
            </a>
        </li>
        <li>
            <a href="ui_session_timeout.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_session_timeout.html">
                Session Timeout
            </a>
        </li>
        <li>
            <a href="ui_idle_timeout.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_idle_timeout.html">
                User Idle Timeout
            </a>
        </li>
        <li>
            <a href="ui_modals.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_modals.html">
                Modals
            </a>
        </li>
        <li>
            <a href="ui_extended_modals.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_extended_modals.html">
                Extended Modals
            </a>
        </li>
        <li>
            <a href="ui_tiles.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_tiles.html">
                Tiles
            </a>
        </li>
        <li>
            <a href="ui_datepaginator.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_datepaginator.html">
								<span class="badge badge-roundless badge-success">
									new
								</span>
                Date Paginator
            </a>
        </li>
        <li>
            <a href="ui_nestable.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/ui_nestable.html">
                Nestable List
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-puzzle-piece"></i>
						<span class="title">
							UI Components
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="components_pickers.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/components_pickers.html">
                Pickers
            </a>
        </li>
        <li>
            <a href="components_dropdowns.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/components_dropdowns.html">
                Custom Dropdowns
            </a>
        </li>
        <li>
            <a href="components_form_tools.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/components_form_tools.html">
                Form Tools
            </a>
        </li>
        <li>
            <a href="components_editors.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/components_editors.html">
                Markdown & WYSIWYG Editors
            </a>
        </li>
        <li>
            <a href="components_ion_sliders.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/components_ion_sliders.html">
                Ion Range Sliders
            </a>
        </li>
        <li>
            <a href="components_noui_sliders.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/components_noui_sliders.html">
                NoUI Range Sliders
            </a>
        </li>
        <li>
            <a href="components_jqueryui_sliders.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/components_jqueryui_sliders.html">
                jQuery UI Sliders
            </a>
        </li>
        <li>
            <a href="components_knob_dials.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/components_knob_dials.html">
                Knob Circle Dials
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-table"></i>
						<span class="title">
							Form Stuff
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="form_controls.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/form_controls.html">
                Form Controls
            </a>
        </li>
        <li>
            <a href="form_layouts.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/form_layouts.html">
                Form Layouts
            </a>
        </li>
        <li>
            <a href="form_editable.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/form_editable.html">
								<span class="badge badge-roundless badge-warning">
									new
								</span>
                Form X-editable
            </a>
        </li>
        <li>
            <a href="form_wizard.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/form_wizard.html">
                Form Wizard
            </a>
        </li>
        <li>
            <a href="form_validation.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/form_validation.html">
                Form Validation
            </a>
        </li>
        <li>
            <a href="form_image_crop.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/form_image_crop.html">
								<span class="badge badge-roundless badge-important">
									new
								</span>
                Image Cropping
            </a>
        </li>
        <li>
            <a href="form_fileupload.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/form_fileupload.html">
                Multiple File Upload
            </a>
        </li>
        <li>
            <a href="form_dropzone.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/form_dropzone.html">
                Dropzone File Upload
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-envelope-o"></i>
						<span class="title">
							Email Templates
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="email_newsletter.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/email_newsletter.html">
                Responsive Newsletter<br>
                Email Template
            </a>
        </li>
        <li>
            <a href="email_system.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/email_system.html">
                Responsive System<br>
                Email Template
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-sitemap"></i>
						<span class="title">
							Pages
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="page_portfolio.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/page_portfolio.html">
                <i class="fa fa-briefcase"></i>
								<span class="badge badge-warning badge-roundless">
									new
								</span>
                Portfolio
            </a>
        </li>
        <li>
            <a href="page_timeline.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/page_timeline.html">
                <i class="fa fa-clock-o"></i>
								<span class="badge badge-info">
									4
								</span>
                Timeline
            </a>
        </li>
        <li>
            <a href="page_coming_soon.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/page_coming_soon.html">
                <i class="fa fa-cogs"></i>
                Coming Soon
            </a>
        </li>
        <li>
            <a href="page_blog.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/page_blog.html">
                <i class="fa fa-comments"></i>
                Blog
            </a>
        </li>
        <li>
            <a href="page_blog_item.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/page_blog_item.html">
                <i class="fa fa-font"></i>
                Blog Post
            </a>
        </li>
        <li>
            <a href="page_news.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/page_news.html">
                <i class="fa fa-coffee"></i>
								<span class="badge badge-success">
									9
								</span>
                News
            </a>
        </li>
        <li>
            <a href="page_news_item.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/page_news_item.html">
                <i class="fa fa-bell"></i>
                News View
            </a>
        </li>
        <li>
            <a href="page_about.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/page_about.html">
                <i class="fa fa-group"></i>
                About Us
            </a>
        </li>
        <li>
            <a href="page_contact.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/page_contact.html">
                <i class="fa fa-envelope-o"></i>
                Contact Us
            </a>
        </li>
        <li>
            <a href="page_calendar.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/page_calendar.html">
                <i class="fa fa-calendar"></i>
								<span class="badge badge-important">
									14
								</span>
                Calendar
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-gift"></i>
						<span class="title">
							Extra
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="extra_profile.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_profile.html">
                User Profile
            </a>
        </li>
        <li>
            <a href="extra_lock.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_lock.html">
                Lock Screen
            </a>
        </li>
        <li>
            <a href="extra_faq.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_faq.html">
                FAQ
            </a>
        </li>
        <li>
            <a href="inbox.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/inbox.html">
								<span class="badge badge-important">
									4
								</span>
                Inbox
            </a>
        </li>
        <li>
            <a href="extra_search.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_search.html">
                Search Results
            </a>
        </li>
        <li>
            <a href="extra_invoice.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_invoice.html">
                Invoice
            </a>
        </li>
        <li>
            <a href="extra_pricing_table.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_pricing_table.html">
                Pricing Tables
            </a>
        </li>
        <li>
            <a href="extra_404_option1.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_404_option1.html">
                404 Page Option 1
            </a>
        </li>
        <li>
            <a href="extra_404_option2.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_404_option2.html">
                404 Page Option 2
            </a>
        </li>
        <li>
            <a href="extra_404_option3.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_404_option3.html">
                404 Page Option 3
            </a>
        </li>
        <li>
            <a href="extra_500_option1.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_500_option1.html">
                500 Page Option 1
            </a>
        </li>
        <li>
            <a href="extra_500_option2.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/extra_500_option2.html">
                500 Page Option 2
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-folder-open"></i>
						<span class="title">
							Multi Level Menu
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="javascript:;">
                <i class="fa fa-cogs"></i> Item 1
								<span class="arrow">
								</span>
            </a>
            <ul class="sub-menu">
                <li>
                    <a href="javascript:;">
                        <i class="fa fa-user"></i>
                        Sample Link 1
										<span class="arrow">
										</span>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <a href="#">
                                <i class="fa fa-remove"></i> Sample Link 1
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa fa-pencil"></i> Sample Link 1
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa fa-edit"></i> Sample Link 1
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-user"></i> Sample Link 1
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-external-link"></i> Sample Link 2
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-bell"></i> Sample Link 3
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;">
                <i class="fa fa-globe"></i> Item 2
								<span class="arrow">
								</span>
            </a>
            <ul class="sub-menu">
                <li>
                    <a href="#">
                        <i class="fa fa-user"></i> Sample Link 1
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-external-link"></i> Sample Link 1
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-bell"></i> Sample Link 1
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#">
                <i class="fa fa-folder-open"></i>
                Item 3
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-user"></i>
						<span class="title">
							Login Options
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="login.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/login.html">
                Login Form 1
            </a>
        </li>
        <li>
            <a href="login_soft.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/login_soft.html">
                Login Form 2
            </a>
        </li>
    </ul>
</li>
<li class="active ">
    <a href="javascript:;">
        <i class="fa fa-th"></i>
						<span class="title">
							Data Tables
						</span>
						<span class="selected">
						</span>
						<span class="arrow open">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="table_basic.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/table_basic.html">
                Basic Datatables
            </a>
        </li>
        <li>
            <a href="table_responsive.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/table_responsive.html">
                Responsive Datatables
            </a>
        </li>
        <li class="active">
            <a href="table_managed.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/table_managed.html">
                Managed Datatables
            </a>
        </li>
        <li>
            <a href="table_editable.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/table_editable.html">
                Editable Datatables
            </a>
        </li>
        <li>
            <a href="table_advanced.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/table_advanced.html">
                Advanced Datatables
            </a>
        </li>
        <li>
            <a href="table_ajax.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/table_ajax.html">
                Ajax Datatables
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-file-text"></i>
						<span class="title">
							Portlets
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="portlet_general.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/portlet_general.html">
                General Portlets
            </a>
        </li>
        <li>
            <a href="portlet_ajax.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/portlet_ajax.html">
                Ajax Portlets
            </a>
        </li>
        <li>
            <a href="portlet_draggable.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/portlet_draggable.html">
                Draggable Portlets
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="javascript:;">
        <i class="fa fa-map-marker"></i>
						<span class="title">
							Maps
						</span>
						<span class="arrow ">
						</span>
    </a>
    <ul class="sub-menu">
        <li>
            <a href="maps_google.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/maps_google.html">
                Google Maps
            </a>
        </li>
        <li>
            <a href="maps_vector.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/maps_vector.html">
                Vector Maps
            </a>
        </li>
    </ul>
</li>
<li class="last ">
    <a href="charts.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/charts.html">
        <i class="fa fa-bar-chart-o"></i>
						<span class="title">
							Visual Charts
						</span>
    </a>
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
            Managed Datatables <small>managed datatable samples</small>
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
                <a href="index.html" tppabs="http://www.keenthemes.com/preview/metronic_admin/index.html">
                    Home
                </a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                <a href="#">
                    Data Tables
                </a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                <a href="#">
                    Managed Datatables
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
<!-- BEGIN EXAMPLE TABLE PORTLET-->
<div class="portlet box light-grey">
<div class="portlet-title">
    <div class="caption">
        <i class="fa fa-globe"></i>Managed Table
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
<div class="portlet-body">
<div class="table-toolbar">
    <div class="btn-group">
        <button id="sample_editable_1_new" class="btn green">
            Add New <i class="fa fa-plus"></i>
        </button>
    </div>
    <div class="btn-group pull-right">
        <button class="btn dropdown-toggle" data-toggle="dropdown">Tools <i class="fa fa-angle-down"></i>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="#">
                    Print
                </a>
            </li>
            <li>
                <a href="#">
                    Save as PDF
                </a>
            </li>
            <li>
                <a href="#">
                    Export to Excel
                </a>
            </li>
        </ul>
    </div>
</div>
<table class="table table-striped table-bordered table-hover" id="sample_1">
<thead>
<tr>
    <th class="table-checkbox">
        <input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes"/>
    </th>
    <th>
        Username
    </th>
    <th>
        Email
    </th>
    <th>
        Points
    </th>
    <th>
        Joined
    </th>
    <th>
        &nbsp;
    </th>
</tr>
</thead>
<tbody>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        shuxer
    </td>
    <td>
        <a href="mailto:shuxer@gmail.com">
            shuxer@gmail.com
        </a>
    </td>
    <td>
        120
    </td>
    <td class="center">
        12 Jan 2012
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        looper
    </td>
    <td>
        <a href="mailto:looper90@gmail.com">
            looper90@gmail.com
        </a>
    </td>
    <td>
        120
    </td>
    <td class="center">
        12.12.2011
    </td>
    <td>
									<span class="label label-sm label-warning">
										 Suspended
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        userwow
    </td>
    <td>
        <a href="mailto:userwow@yahoo.com">
            userwow@yahoo.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        12.12.2012
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        user1wow
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            userwow@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        12.12.2012
    </td>
    <td>
									<span class="label label-sm label-default">
										 Blocked
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        restest
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            test@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        12.12.2012
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        foopl
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        19.11.2010
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        weep
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        19.11.2010
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        coop
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        19.11.2010
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        pppol
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        19.11.2010
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        test
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        19.11.2010
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        userwow
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            userwow@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        12.12.2012
    </td>
    <td>
									<span class="label label-sm label-default">
										 Blocked
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        test
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            test@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        12.12.2012
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        goop
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        12.11.2010
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        weep
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        15.11.2011
    </td>
    <td>
									<span class="label label-sm label-default">
										 Blocked
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        toopl
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        16.11.2010
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        userwow
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            userwow@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        9.12.2012
    </td>
    <td>
									<span class="label label-sm label-default">
										 Blocked
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        tes21t
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            test@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        14.12.2012
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        fop
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        13.11.2010
    </td>
    <td>
									<span class="label label-sm label-warning">
										 Suspended
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        kop
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        17.11.2010
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        vopl
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        19.11.2010
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        userwow
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            userwow@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        12.12.2012
    </td>
    <td>
									<span class="label label-sm label-default">
										 Blocked
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        wap
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            test@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        12.12.2012
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        test
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        19.12.2010
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        toop
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        17.12.2010
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        weep
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
        20
    </td>
    <td class="center">
        15.11.2011
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- END EXAMPLE TABLE PORTLET-->
</div>
</div>
<div class="row">
<div class="col-md-6 col-sm-12">
<!-- BEGIN EXAMPLE TABLE PORTLET-->
<div class="portlet box grey">
<div class="portlet-title">
    <div class="caption">
        <i class="fa fa-user"></i>Table
    </div>
    <div class="actions">
        <a href="#" class="btn blue">
            <i class="fa fa-pencil"></i> Add
        </a>
        <div class="btn-group">
            <a class="btn green" href="#" data-toggle="dropdown">
                <i class="fa fa-cogs"></i> Tools <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="#">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-trash-o"></i> Delete
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-ban"></i> Ban
                    </a>
                </li>
                <li class="divider">
                </li>
                <li>
                    <a href="#">
                        <i class="i"></i> Make admin
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="portlet-body">
<table class="table table-striped table-bordered table-hover" id="sample_2">
<thead>
<tr>
    <th style="width1:8px;">
        <input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes"/>
    </th>
    <th>
        Username
    </th>
    <th>
        Email
    </th>
    <th>
        Status
    </th>
</tr>
</thead>
<tbody>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        shuxer
    </td>
    <td>
        <a href="mailto:shuxer@gmail.com">
            shuxer@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        looper
    </td>
    <td>
        <a href="mailto:looper90@gmail.com">
            looper90@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-warning">
										 Suspended
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        userwow
    </td>
    <td>
        <a href="mailto:userwow@yahoo.com">
            userwow@yahoo.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        user1wow
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            userwow@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-default">
										 Blocked
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        restest
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            test@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        foopl
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        weep
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        coop
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        pppol
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        test
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        userwow
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            userwow@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-default">
										 Blocked
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        test
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            test@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- END EXAMPLE TABLE PORTLET-->
</div>
<div class="col-md-6 col-sm-12">
<!-- BEGIN EXAMPLE TABLE PORTLET-->
<div class="portlet box purple">
<div class="portlet-title">
    <div class="caption">
        <i class="fa fa-cogs"></i>Table
    </div>
    <div class="actions">
        <a href="#" class="btn green">
            <i class="fa fa-plus"></i> Add
        </a>
        <a href="#" class="btn yellow">
            <i class="fa fa-print"></i> Print
        </a>
    </div>
</div>
<div class="portlet-body">
<table class="table table-striped table-bordered table-hover" id="sample_3">
<thead>
<tr>
    <th class="table-checkbox">
        <input type="checkbox" class="group-checkable" data-set="#sample_3 .checkboxes"/>
    </th>
    <th>
        Username
    </th>
    <th>
        Email
    </th>
    <th>
        Status
    </th>
</tr>
</thead>
<tbody>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        shuxer
    </td>
    <td>
        <a href="mailto:shuxer@gmail.com">
            shuxer@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        looper
    </td>
    <td>
        <a href="mailto:looper90@gmail.com">
            looper90@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-warning">
										 Suspended
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        userwow
    </td>
    <td>
        <a href="mailto:userwow@yahoo.com">
            userwow@yahoo.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        user1wow
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            userwow@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-default">
										 Blocked
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        restest
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            test@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        foopl
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        weep
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        coop
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        pppol
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        test
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            good@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        userwow
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            userwow@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-default">
										 Blocked
									</span>
    </td>
</tr>
<tr class="odd gradeX">
    <td>
        <input type="checkbox" class="checkboxes" value="1"/>
    </td>
    <td>
        test
    </td>
    <td>
        <a href="mailto:userwow@gmail.com">
            test@gmail.com
        </a>
    </td>
    <td>
									<span class="label label-sm label-success">
										 Approved
									</span>
    </td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- END EXAMPLE TABLE PORTLET-->
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
<script src="assets/plugins/respond.min.js"></script>
<script src="assets/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="assets/plugins/jquery-1.10.2.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-migrate-1.2.1.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery.blockui.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery.cokie.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="assets/plugins/uniform/jquery.uniform.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="assets/plugins/select2/select2.min.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="assets/plugins/data-tables/jquery.dataTables.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="assets/plugins/data-tables/DT_bootstrap.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="assets/scripts/core/app.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/scripts/core/app.js"></script>
<script src="assets/scripts/custom/table-managed.js" tppabs="http://www.keenthemes.com/preview/metronic_admin/assets/scripts/custom/table-managed.js"></script>
<script>
    jQuery(document).ready(function() {
        App.init();
        TableManaged.init();
    });
</script>
<script type="text/javascript">  var _gaq = _gaq || [];  _gaq.push(['_setAccount', 'UA-37564768-1']);  _gaq.push(['_setDomainName', 'keenthemes.com']);  _gaq.push(['_setAllowLinker', true]);  _gaq.push(['_trackPageview']);  (function() {    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;    ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);  })();</script></body>
<!-- END BODY -->
</html>