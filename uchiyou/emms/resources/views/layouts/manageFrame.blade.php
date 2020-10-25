<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="renderer" content="webkit">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>物资管家</title>

<meta name="keywords" content="">
<meta name="description" content="">

<!--[if lt IE 9]>
<meta http-equiv="refresh" content="0;ie.html" />
<![endif]-->

<link rel="shortcut icon" href="favicon.ico">
<link href="{{ asset('css/bootstrap.min.css?v=3.3.6') }}"
	rel="stylesheet">
<link href="{{ asset('css/font-awesome.min.css?v=4.4.0') }}"
	rel="stylesheet">
<link href="{{ asset('css/animate.css') }}" rel="stylesheet">
<link href="{{ asset('css/style.css?v=4.1.0') }}" rel="stylesheet">
@yield('importCss')

</head>

<!--<body class="fixed-sidebar full-height-layout gray-bg">-->

<body class="fixed-sidebar full-height-layout gray-bg"
	style="overflow: hidden">
	<div id="wrapper">
		<!--左侧导航开始-->
		<nav class="navbar-default navbar-static-side" role="navigation">
			<div class="nav-close">
				<i class="fa fa-times-circle"></i>
			</div>
			<div class="sidebar-collapse">
				<ul class="nav" id="side-menu">
					<li class="nav-header">
						<div class="dropdown profile-element">
							<a href="/"> <span
								class="clear"> <span class="block m-t-xs"
									style="font-size: 20px;"> <i class="fa fa-home"></i> <strong
										class="font-bold">主页</strong>
								</span>
							</span>
							</a>
						</div>
						<div class="logo-element">管理</div>
					</li>
					<li class="hidden-folded padder m-t m-b-sm text-muted text-xs"><span
						class="ng-scope">分类</span></li>
					<li><a class="J_menuItem" href="/admin/search/get"> <i
							class="fa fa-search"></i> <span class="nav-label">信息检索</span>
					</a></li>
					</li>
					<li><a class="J_menuItem" href="/admin/tree"> <i
							class="fa fa-sitemap"></i> <span class="nav-label">信息管理</span>
					</a></li>
					<li><a class="J_menuItem" href="/admin/material/rent/history/person/unreturn"> <i
							class="glyphicon glyphicon-bookmark"></i> <span class="nav-label">租借</span>
					</a></li>
					<li><a class="J_menuItem"
						href="/admin/material/appointment/history/person/appointed"> <i
							class="glyphicon glyphicon-paperclip"></i> <span class="nav-label">预约</span>
					</a></li>
					@if(Auth::user()->job_type != 1)
						<li><a class="J_menuItem"
						href="/admin/material/purchase/history/person/receive"> <i class="glyphicon glyphicon-plus-sign"></i>
							<span class="nav-label">申请购买</span>
						</a></li>
					@else
					<li class="line dk"></li>
					<li><a class="J_menuItem"
						href="/admin/material/purchase/history/manage/apply"> 
						<i class="glyphicon glyphicon-saved"></i>
							<span class="nav-label">购买审批</span>
							<span class="label label-primary"
								id="purchaseInfo"></span>
					</a></li>
					<li><a class="J_menuItem"
						href="/admin/material/repaire/history/applys"> <i class="glyphicon glyphicon-wrench"></i>
							<span class="nav-label">故障维修</span>
							<span class="label label-primary"
								id="repaireInfo"></span>
					</a></li>
					<li><a class="J_menuItem" href="/admin/organization"> <i
							class="fa fa-users"></i> <span class="nav-label">组织机构管理</span>
					</a></li>
					<li><a class="J_menuItem"
						href="/admin/material/deliver/history/orders"> <i class="fa  fa-truck"></i>
							<span class="nav-label">待送物资</span>
							<span class="label label-primary"
								id="deliverInfo"></span>
					</a></li>
					@if(Auth::user()->tree_trunk_id == 0)
					 <li>
                        <a href="#">
                            <i class="fa fa fa-bar-chart-o"></i>
                            <span class="nav-label">图表统计</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
						<li><a class="J_menuItem" href="/admin/statistics/material/department"><i
							class="glyphicon glyphicon-stats"></i> <span class="nav-label">部门物资数量 </span><span
							class="label label-warning pull-right"></span></a></li>
						<li><a class="J_menuItem" href="/admin/statistics/basic/rent"><i
							class="glyphicon glyphicon-stats"></i> <span class="nav-label">租借Top10 </span><span
							class="label label-warning pull-right"></span></a></li>
						<li><a class="J_menuItem" href="/admin/statistics/basic/appointment"><i
							class="glyphicon glyphicon-stats"></i> <span class="nav-label">预约Top10 </span><span
							class="label label-warning pull-right"></span></a></li>
						<!-- <li><a class="J_menuItem" href="/admin/statistics/node"><i
							class="fa fa-envelope"></i> <span class="nav-label">部门信息 </span><span
							class="label label-warning pull-right"></span></a></li> -->

                        </ul>
                    </li>
					 <li>
                        <a href="#">
                            <i class="fa fa fa-bar-chart-o"></i>
                            <span class="nav-label">企业管理</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
						<li><a class="J_menuItem" href="/admin/company"><i
							class="glyphicon glyphicon-stats"></i> <span class="nav-label">企业管理 </span><span
							class="label label-warning pull-right"></span></a></li>
						</ul>
                    </li>
                    @endif
					 <li>
                        <a href="#">
                            <i class="glyphicon glyphicon-list"></i>
                            <span class="nav-label">历史记录</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
						<li><a class="J_menuItem" href="/admin/material/rent/history/manage/unreturn"><i
							class="glyphicon glyphicon-align-justify"></i> <span class="nav-label">租借记录 </span><span
							class="label label-warning pull-right"></span></a></li>
						<li><a class="J_menuItem" href="/admin/material/appointment/history/manage/appointed"><i
							class="glyphicon glyphicon-align-justify"></i> <span class="nav-label">预约记录 </span><span
							class="label label-warning pull-right"></span></a></li>

                        </ul>
                    </li>
					@endif
					@if(Auth::user()->job_type == 4)
					<li><a class="J_menuItem" href="/admin/housekeep/home"><i
							class="glyphicon glyphicon-align-justify"></i> <span class="nav-label">系统管理 </span><span
							class="label label-warning pull-right"></span></a></li>
					@endif					
				</ul>
			</div>
		</nav>
		<!--左侧导航结束-->
		<!--右侧部分开始-->
		<div id="page-wrapper" class="gray-bg dashbard-1">
			<div class="row border-bottom">
				<nav class="navbar navbar-static-top" role="navigation"
					style="margin-bottom: 0">
					<div class="navbar-header navbar-collapse collapse">
						<a class="navbar-minimalize minimalize-styl-2 btn btn-info "
							href="#"><i class="fa fa-bars"></i> </a>
						<form role="search" class="navbar-form-custom" method="post"
							action="/admin/search" id="searchForm"
							style="display: inline-block">
							<input type="hidden" name="_token" value="{{csrf_token()}}">
							<table>
								<tr>
									<td><select class="form-control" name="type"
										style="vertical-align: middle;" id="searchType">
											<option value="name">名称</option>
											<option value="type">类型</option>
											<option value="tree_trunk_name">部门</option>
									</select></td>
									<td><input type="text" placeholder="请输要查找的内容 …"
										class="form-control" style="vertical-align: middle;"
										name="content"></td>
								</tr>
							</table>
						</form>
					</div>
					<ul class="nav navbar-top-links navbar-right">
						<li class="dropdown"><a class="dropdown-toggle count-info"
							data-toggle="dropdown" href="/admin/messages/show"> <i
								class="fa fa-bell"></i> <span class="label label-primary"
								id="messageInfo"></span>
						</a>
							<ul class="dropdown-menu dropdown-alerts">
								<li><a href="/admin/messages/show" class="J_menuItem">
										<div>
											<i class="fa fa-envelope fa-fw"></i> 查看未读消息 <span
												class="pull-right text-muted small"></span>
										</div>
								</a></li>
							</ul></li>
							
						<li class="dropdown">
						<a href="#" class="dropdown-toggle"
							data-toggle="dropdown" role="button" aria-expanded="false"> {{
								Auth::user()->name }} <span class="caret"></span>
						</a>

							<ul class="dropdown-menu" role="menu">
								<li><a href="/admin/user" class="J_menuItem">
										个人中心 </a>
								</li>
								<li><a href="{{ route('logout') }}"
									onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
										退出 </a>

									<form id="logout-form" action="{{ route('logout') }}"
										method="POST" style="display: none;">{{ csrf_field() }}</form>
								</li>
							</ul>
							</li>
					</ul>
				</nav>
			</div>
			<div class="row J_mainContent" id="content-main">
				<iframe id="J_iframe" width="100%" height="100%"
					src="/admin/search/get" frameborder="0" data-id="index_v1.html"
					seamless> </iframe>
			</div>
		</div>
		<!--右侧部分结束-->
	</div>

	<!-- 全局js -->
	<script src="{{ asset('js/jquery.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap.min.js?v=3.3.6') }}"></script>
	<script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
	<script
		src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
	<script src="{{ asset('js/plugins/layer/layer.min.js') }}"></script>
	<script src="{{ asset('js/jquery.form.js') }}"></script>

	<!-- 自定义js -->
	<script src="{{ asset('js/hAdmin.js?v=4.1.0') }}"></script>
	<script src="{{ asset('js/index.js') }}"></script>
	<script src="{{ asset('js/search.js') }}"></script>
	<div style="text-align: center;"></div>
</body>

</html>
