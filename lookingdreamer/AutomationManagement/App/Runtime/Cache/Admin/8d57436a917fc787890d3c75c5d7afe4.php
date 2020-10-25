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




				 


<div class="col-md-12">


    <div class="panel panel-archon">
        <div class="panel-heading"
             style="background-color: #59d1a3; color: #ffffff; text-transform: uppercase;">
            <h3 class="panel-title tb_title">
                <?php echo (($transtring["main_table_title"])?($transtring["main_table_title"]):"表格标题未定义/未获取成功"); ?>
				<span class="pull-right">
					<a style="color: white;" href="#" class="panel-settings">
                        <i class="icon-cog"></i>
                    </a>
					<a style="color: white;" href="#" class="panel-close">
                        <i class="icon-remove"></i>
                    </a>
				</span>
            </h3>
        </div>


        <div class="buttons-demo tb_title" style="height: 40px; margin-top: 1px;">
            <a href="<?php echo ($add_link); ?>" class="btn btn-hg btn-info">
                <i class="icon-plus"></i>
                添加记录
            </a>
            <a href="<?php echo ($edit_link); ?>" checkbox="true"
               class="dialog-action btn btn-hg btn-info ">
                <i class="icon-edit"></i>
                编辑记录
            </a>
            <a href="<?php echo ($delete_link); ?>" message="确定要删除选中的记录么？"
               dialog="true" checkbox="true"
               class="dialog-action btn btn-hg btn-info ">
                <i class="icon-remove-sign"></i>
                批量删除
            </a>

            <a href="<?php echo ($cache_link); ?>" dialog="after"
               checkbox="false" class="dialog-action btn btn-hg btn-info ">
                <i class=" icon-refresh"></i>
                更新缓存
            </a>
            <a href="<?php echo ($export_link); ?>"
               class="btn btn-hg btn-info ">
                <i class="icon-download"></i>
                导出列表
            </a>
            <a href="<?php echo ($import_link); ?>"
               class="btn btn-hg btn-info ">
                <i class="icon-upload"></i>
                导入列表
            </a>
            <a href="<?php echo U('Admin/Addstr/flushaddstr');?>"
               class="btn btn-hg btn-info ">
                <i class="icon-upload"></i>
                刷新数据
            </a>

            <div class="col-lg-3" style="float: right; margin-top: 1px">
                <form method="GET" class="navbar-form pull-right"
                      action="<?php echo ($index_link); ?>" style="margin-top: 0px;">

                    <div class="input-group">
                        <input type="text" class="form-control" name="search"
                               placeholder="可选择任何关键字搜索!">
						<span class="input-group-btn">
							<button type="submit" class="btn btn-small btn-primary">
                                <i class="icon-search"></i>
                                搜索
                            </button>
						</span>
                    </div>

                </form>
            </div>


        </div>


        <div class="panel-body" style="padding-top: 1px">
            <table class="table table-hover">
                <thead>
                <tr>

                    <th>
                        <input type="checkbox" class="style checkboxall"/>
                    </th>
                    <?php if(is_array($table_th)): $i = 0; $__LIST__ = $table_th;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$th_name): $mod = ($i % 2 );++$i;?><th class="th_center"><?php echo ($th_name); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>

                </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $vo){ if($vo['id']){ echo "<tr>"; echo "<td class='notallow td_center'>"; echo "<input type='checkbox' value=".$vo['id']." class='style checkboxrow'/>"; echo "</td>"; echo "<td class='visible-lg td_center'>".$vo['id']."</td>"; foreach ($table_tr as $trname){ if($trname != "id"){ echo "<td class='visible-lg td_center'>".$vo["$trname"]."</td>"; } } $edit_url=$edit_link."?"."id=".$vo['id']; echo "<td style=' margin:0 auto;padding:0;clear:both;'>"; echo "<div style='margin:0; padding:0;  display:inline-block; _display:inline; *display:inline;zoom:1;  ''>"; echo "<a href=".$edit_url." class='btn btn-small btn-primary'>"; echo "<i class='icon-edit'></i>"; echo "</a>"; echo "</div>"; echo "<div style='margin:0; padding:0;  display:inline-block; _display:inline; *display:inline;zoom:1; '>"; echo "<a href='".$delete_link."'"; echo " message='确定要删除 序号为 ( ".$vo['id']. " ) 的这条记录么？'"; echo " params='"."id=".$vo['id']."' dialog='true' checkbox='false'"; echo " id='dialogtest' class='dialog-action btn btn-small btn-primary'>"; echo " <i class='icon-remove-sign'></i>"; echo "</a>"; $runurl=U('Admin/Addstr/addstring',array('id'=>$vo['id'])); echo "<a href=".$runurl." class='btn btn-small btn-primary'>"; echo "<i class='icon-arrow-right' title='添加子段到数据表中,使其生效!'></i>"; echo "</a>"; echo "</div>"; echo "</td>"; echo "</tr>"; } }?>
                <!-- 		  <?php if(is_array($listsecond)): $i = 0; $__LIST__ = $listsecond;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                         <td class="notallow"><input type="checkbox" value="<?php echo ($vo["id"]); ?>" class="style checkboxrow" /></td>
                                                 <?php if(is_array($vo)): $i = 0; $__LIST__ = $vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><td class="visible-lg"><?php echo ($sub); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>


                                        <td>
                                            <button type="button" class="btn btn-info"><i class="icon-remove-sign"></i></button>
                                            <button type="button" class="btn btn-info"><i class="icon-edit"></i></button>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?> -->

                </tbody>
            </table>
        </div>
        <div style="text-align: center;">
            <div class="pagination" style="margin-top: 2px"><?php echo ($page); ?></div>
        </div>
    </div>
</div>
</div>



<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="width: 602px;height: 285px;
       padding: 0;
      "

        >
    <div class="modal-dialog" style=" padding: 0;"  >
        <div class="modal-content" style=" padding: 0;" >
            <div class="modal-header" >
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h5 class="modal-title tb_title">设置列表显示的字段</h5>

            </div>
            <div class="modal-body" >
                <form class="form-horizontal" role="form" method="get" action="<?php echo ($index_link); ?>">

                    <div class="form-group">

                        <?php if(is_array($xlsCell)): $i = 0; $__LIST__ = $xlsCell;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-lg-9">
                            <label class="checkbox-inline">
                                <input type="checkbox"  name="checkbox[]" value="<?php echo ($vo["column_name"]); ?>" >
                            </label>
                            <label  class="col-lg-3 control-label td_center"><?php echo ($vo["column_comment"]); ?></label>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>

                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-small btn-primary">

                    确定
                </button>
            </div>
            </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->






















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