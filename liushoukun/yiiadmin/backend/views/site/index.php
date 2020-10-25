<?php

/* @var $this yii\web\View */
use yii\helpers\Url;
$this->title = 'My Yii Application';
?>

<div class="navbar navbar-default" id="navbar">
    <script type="text/javascript">
        try {
            ace.settings.check('navbar', 'fixed')
        } catch(e) {}
    </script>
    <!----------------------------------------------
        顶部栏 start --- logo + 个人信息
    ------------------------------------------------>
    <div class="navbar-container" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="#" class="navbar-brand">
                <small>
                    <i class="icon-leaf"></i>
                    ACE后台管理系统
                </small>
            </a>
            <!-- /.brand -->
        </div>
        <!-- /.navbar-header -->

        <div class="navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">
                <li class="grey">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon-tasks"></i>
                        <span class="badge badge-grey">4</span>
                    </a>

                    <ul class="pull-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
                        <li class="dropdown-header">
                            <i class="icon-ok"></i> 还有4个任务完成
                        </li>

                        <li>
                            <a href="#">
                                <div class="clearfix">
                                    <span class="pull-left">软件更新</span>
                                    <span class="pull-right">65%</span>
                                </div>

                                <div class="progress progress-mini ">
                                    <div style="width:65%" class="progress-bar "></div>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <div class="clearfix">
                                    <span class="pull-left">硬件更新</span>
                                    <span class="pull-right">35%</span>
                                </div>

                                <div class="progress progress-mini ">
                                    <div style="width:35%" class="progress-bar progress-bar-danger"></div>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <div class="clearfix">
                                    <span class="pull-left">单元测试</span>
                                    <span class="pull-right">15%</span>
                                </div>

                                <div class="progress progress-mini ">
                                    <div style="width:15%" class="progress-bar progress-bar-warning"></div>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <div class="clearfix">
                                    <span class="pull-left">错误修复</span>
                                    <span class="pull-right">90%</span>
                                </div>

                                <div class="progress progress-mini progress-striped active">
                                    <div style="width:90%" class="progress-bar progress-bar-success"></div>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                查看任务详情
                                <i class="icon-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="purple">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon-bell-alt icon-animated-bell"></i>
                        <span class="badge badge-important">8</span>
                    </a>

                    <ul class="pull-right dropdown-navbar navbar-pink dropdown-menu dropdown-caret dropdown-close">
                        <li class="dropdown-header">
                            <i class="icon-warning-sign"></i> 8条通知
                        </li>

                        <li>
                            <a href="#">
                                <div class="clearfix">
											<span class="pull-left">
												<i class="btn btn-xs no-hover btn-pink icon-comment"></i>
												新闻评论
											</span>
                                    <span class="pull-right badge badge-info">+12</span>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <i class="btn btn-xs btn-primary icon-user"></i> 切换为编辑登录..
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <div class="clearfix">
											<span class="pull-left">
												<i class="btn btn-xs no-hover btn-success icon-shopping-cart"></i>
												新订单
											</span>
                                    <span class="pull-right badge badge-success">+8</span>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <div class="clearfix">
											<span class="pull-left">
												<i class="btn btn-xs no-hover btn-info icon-twitter"></i>
												粉丝
											</span>
                                    <span class="pull-right badge badge-info">+11</span>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                查看所有通知
                                <i class="icon-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="green">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon-envelope icon-animated-vertical"></i>
                        <span class="badge badge-success">5</span>
                    </a>

                    <ul class="pull-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
                        <li class="dropdown-header">
                            <i class="icon-envelope-alt"></i> 5条消息
                        </li>

                        <li>
                            <a href="#">
                                <img src="<?=Url::base()?>/aceAdmin/assets/avatars/avatar.png" class="msg-photo" alt="Alex's Avatar" />
                                <span class="msg-body">
											<span class="msg-title">
												<span class="blue">Alex:</span> 不知道写啥 ...
										</span>

										<span class="msg-time">
												<i class="icon-time"></i>
												<span>1分钟以前</span>
										</span>
										</span>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <img src="<?=Url::base()?>/aceAdmin/assets/avatars/avatar3.png" class="msg-photo" alt="Susan's Avatar" />
                                <span class="msg-body">
											<span class="msg-title">
												<span class="blue">Susan:</span> 不知道翻译...
										</span>

										<span class="msg-time">
												<i class="icon-time"></i>
												<span>20分钟以前</span>
										</span>
										</span>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <img src="<?=Url::base()?>/aceAdmin/assets/avatars/avatar4.png" class="msg-photo" alt="Bob's Avatar" />
                                <span class="msg-body">
											<span class="msg-title">
												<span class="blue">Bob:</span> 到底是不是英文 ...
										</span>

										<span class="msg-time">
												<i class="icon-time"></i>
												<span>下午3:15</span>
										</span>
										</span>
                            </a>
                        </li>

                        <li>
                            <a href="inbox.html">
                                查看所有消息
                                <i class="icon-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="light-blue">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <img class="nav-user-photo" src="<?=Url::base()?>/aceAdmin/assets/avatars/user.jpg" alt="Jason's Photo" />
                        <span class="user-info">
									<small>欢迎光临,</small>
									Jason
								</span>

                        <i class="icon-caret-down"></i>
                    </a>

                    <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li>
                            <a href="#">
                                <i class="icon-cog"></i> 设置
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <i class="icon-user"></i> 个人资料
                            </a>
                        </li>

                        <li class="divider"></li>

                        <li>
                            <a href="#">
                                <i class="icon-off"></i> 退出
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- /.ace-nav -->
        </div>
        <!-- /.navbar-header -->
    </div>
    <!-- /.container -->
    <!----------------------------------------------
        顶部栏 end --- logo + 个人信息
    ------------------------------------------------>
</div>

<!---------------------------------------------------------

        主内容区 start --- 左边导航栏 + 显示区

----------------------------------------------------------->
<div class="main-container" id="main-container">

    <script type="text/javascript">
        try {
            ace.settings.check('main-container', 'fixed')
        } catch(e) {}
    </script>

    <!-- main-container-inner start -->
    <div class="main-container-inner">
        <a class="menu-toggler" id="menu-toggler" href="#">
            <span class="menu-text"></span>
        </a>

        <!----------------------------------------------
            左边导航栏  start
        ------------------------------------------------>
        <div class="sidebar" id="sidebar">
            <script type="text/javascript">
                try {
                    ace.settings.check('sidebar', 'fixed')
                } catch(e) {}
            </script>

            <div class="sidebar-shortcuts" id="sidebar-shortcuts">
                <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                    <button class="btn btn-success">
                        <i class="icon-signal"></i>
                    </button>

                    <button class="btn btn-info">
                        <i class="icon-pencil"></i>
                    </button>

                    <button class="btn btn-warning">
                        <i class="icon-group"></i>
                    </button>

                    <button class="btn btn-danger">
                        <i class="icon-cogs"></i>
                    </button>
                </div>

                <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
                    <span class="btn btn-success"></span>

                    <span class="btn btn-info"></span>

                    <span class="btn btn-warning"></span>

                    <span class="btn btn-danger"></span>
                </div>
            </div>
            <!-- #sidebar-shortcuts -->

            <div id="nav_wraper">
                <ul class="nav nav-list">
                    <li class="active">
                        <!--<a href="index.html">
                        <i class="icon-dashboard"></i>
                        <span class="menu-text"> 控制台 </span>
                    </a>-->
                        <a href="javascript:openapp('index.php','asdas',&#39;控制台&#39;,true);">
                            &nbsp;
                            <i class="fa fa-angle-double-right"></i>
                            <span class="menu-text">
									控制台
								</span>
                        </a>
                    </li>
                    <li>
                        <!--<a href="login.html">
                        <i class="icon-dashboard"></i>
                        <span class="menu-text"> 控制台 </span>
                    </a>-->
                        <a href="javascript:openapp(&#39;login.html&#39;,&#39;87Admin&#39;,&#39;登录吧&#39;,true);">
                            &nbsp;
                            <i class="fa fa-angle-double-right"></i>
                            <span class="menu-text">
									登录吧
								</span>
                        </a>
                    </li>

                    <li>
                        <a href="typography.html">
                            <i class="icon-text-width"></i>
                            <span class="menu-text"> 文字排版 </span>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-desktop"></i>
                            <span class="menu-text"> UI 组件 </span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <li>
                                <a href="elements.html">
                                    <i class="icon-double-angle-right"></i> 组件
                                </a>
                            </li>

                            <li>
                                <a href="buttons.html">
                                    <i class="icon-double-angle-right"></i> 按钮 &amp; 图表
                                </a>
                            </li>

                            <li>
                                <a href="treeview.html">
                                    <i class="icon-double-angle-right"></i> 树菜单
                                </a>
                            </li>

                            <li>
                                <a href="jquery-ui.html">
                                    <i class="icon-double-angle-right"></i> jQuery UI
                                </a>
                            </li>

                            <li>
                                <a href="nestable-list.html">
                                    <i class="icon-double-angle-right"></i> 可拖拽列表
                                </a>
                            </li>

                            <li>
                                <a href="#" class="dropdown-toggle">
                                    <i class="icon-double-angle-right"></i> 三级菜单
                                    <b class="arrow icon-angle-down"></b>
                                </a>

                                <ul class="submenu">
                                    <li>
                                        <a href="#">
                                            <i class="icon-leaf"></i> 第一级
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#" class="dropdown-toggle">
                                            <i class="icon-pencil"></i> 第四级
                                            <b class="arrow icon-angle-down"></b>
                                        </a>

                                        <ul class="submenu">
                                            <li>
                                                <a href="#">
                                                    <i class="icon-plus"></i> 添加产品
                                                </a>
                                            </li>

                                            <li>
                                                <a href="#">
                                                    <i class="icon-eye-open"></i> 查看商品
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-list"></i>
                            <span class="menu-text"> 表格 </span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <li>
                                <a href="tables.html">
                                    <i class="icon-double-angle-right"></i> 简单 &amp; 动态
                                </a>
                            </li>

                            <li>
                                <a href="jqgrid.html">
                                    <i class="icon-double-angle-right"></i> jqGrid plugin
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-edit"></i>
                            <span class="menu-text"> 表单 </span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <li>
                                <a href="form-elements.html">
                                    <i class="icon-double-angle-right"></i> 表单组件
                                </a>
                            </li>

                            <li>
                                <a href="form-wizard.html">
                                    <i class="icon-double-angle-right"></i> 向导提示 &amp; 验证
                                </a>
                            </li>

                            <li>
                                <a href="wysiwyg.html">
                                    <i class="icon-double-angle-right"></i> 编辑器
                                </a>
                            </li>

                            <li>
                                <a href="dropzone.html">
                                    <i class="icon-double-angle-right"></i> 文件上传
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="widgets.html">
                            <i class="icon-list-alt"></i>
                            <span class="menu-text"> 插件 </span>
                        </a>
                    </li>

                    <li>
                        <a href="calendar.html">
                            <i class="icon-calendar"></i>

                            <span class="menu-text">
									日历
									<span class="badge badge-transparent tooltip-error" title="2&nbsp;Important&nbsp;Events">
										<i class="icon-warning-sign red bigger-130"></i>
									</span>
									</span>
                        </a>
                    </li>

                    <li>
                        <a href="gallery.html">
                            <i class="icon-picture"></i>
                            <span class="menu-text"> 相册 </span>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-tag"></i>
                            <span class="menu-text"> 更多页面 </span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <li>
                                <a href="profile.html">
                                    <i class="icon-double-angle-right"></i> 用户信息
                                </a>
                            </li>

                            <li>
                                <a href="inbox.html">
                                    <i class="icon-double-angle-right"></i> 收件箱
                                </a>
                            </li>

                            <li>
                                <a href="pricing.html">
                                    <i class="icon-double-angle-right"></i> 售价单
                                </a>
                            </li>

                            <li>
                                <a href="invoice.html">
                                    <i class="icon-double-angle-right"></i> 购物车
                                </a>
                            </li>

                            <li>
                                <a href="timeline.html">
                                    <i class="icon-double-angle-right"></i> 时间轴
                                </a>
                            </li>

                            <li>
                                <a href="login.html">
                                    <i class="icon-double-angle-right"></i> 登录 &amp; 注册
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-file-alt"></i>

                            <span class="menu-text">
									其他页面
									<span class="badge badge-primary ">5</span>
									</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <li>
                                <a href="faq.html">
                                    <i class="icon-double-angle-right"></i> 帮助
                                </a>
                            </li>

                            <li>
                                <a href="error-404.html">
                                    <i class="icon-double-angle-right"></i> 404错误页面
                                </a>
                            </li>

                            <li>
                                <a href="error-500.html">
                                    <i class="icon-double-angle-right"></i> 500错误页面
                                </a>
                            </li>

                            <li>
                                <a href="grid.html">
                                    <i class="icon-double-angle-right"></i> 网格
                                </a>
                            </li>

                            <li>
                                <a href="blank.html">
                                    <i class="icon-double-angle-right"></i> 空白页面
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <!-- /.nav-list -->
            </div>
            <div class="sidebar-collapse" id="sidebar-collapse">
                <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
            </div>

            <script type="text/javascript">
                try {
                    ace.settings.check('sidebar', 'collapsed')
                } catch(e) {}
            </script>
        </div>
        <!----------------------------------------------
            左边导航栏  end
        ------------------------------------------------>

        <!----------------------------------------------
            显示区  start
        ------------------------------------------------>
        <div class="main-content">
            <!-------------------------
                选项卡：任务栏 start
            --------------------------->
            <div class="breadcrumbs" id="breadcrumbs">
                <a id="task-pre" class="task-changebt" style="display: none;">←</a>

                <!-- 选项卡：任务栏 start -->
                <div id="task-content">
                    <ul class="macro-component-tab" id="task-content-inner">
                        <li class="macro-component-tabitem noclose btn btn-info current" app-id="0" app-url="{:U('test')}" app-name="首页">
                            <span class="macro-tabs-item-text">首页</span>
                        </li>
                        <!--<li class="macro-component-tabitem" app-id="87Admin" app-url="login.html" app-name="菜单管理">
                            <span class="macro-tabs-item-text" title="登录吧">登录吧</span>
                            <a class="macro-component-tabclose" href="javascript:void(0)" title="点击关闭标签">
                                <span></span><b class="macro-component-tabclose-icon">×</b>
                            </a>
                        </li>
                        <li class="macro-component-tabitem" app-id="94Admin" app-url="invoice.html" app-name="菜单分类">
                            <span class="macro-tabs-item-text" title="菜单分类">菜单分类</span>
                            <a class="macro-component-tabclose" href="javascript:void(0)" title="点击关闭标签">
                                <span></span><b class="macro-component-tabclose-icon">×</b>
                            </a>
                        </li>
                        <li class="macro-component-tabitem" app-id="100Admin" app-url="tables.html" app-name="后台菜单">
                            <span class="macro-tabs-item-text" title="后台菜单">后台菜单</span>
                            <a class="macro-component-tabclose" href="javascript:void(0)" title="点击关闭标签">
                                <span></span><b class="macro-component-tabclose-icon">×</b>
                            </a>
                        </li>-->
                    </ul>
                    <div style="clear:both;"></div>
                </div>
                <!-- 选项卡：任务栏 end -->

                <a id="task-next" class="task-changebt" style="display: none;">→</a>
            </div>
            <!-------------------------
                选项卡：任务栏 end
            --------------------------->

            <!-------------------------
                选项卡：内容显示区 start
            --------------------------->
            <div class="page-content" id="content">
                <iframe src="buttons.html" style="width: 100%; height: 100%; display: inline;" frameborder="0" id="appiframe-0" class="appiframe"></iframe>
                <!--<iframe style="width: 100%; height: 100%; display: none;" frameborder="0" class="appiframe" src="login.html" id="appiframe-87Admin "></iframe>
                <iframe style="width: 100%; height: 100%; display: none; " frameborder="0 " class="appiframe " src="invoice.html " id="appiframe-86Admin "></iframe>
                <iframe style="width: 100%; height: 100%; display: none; " frameborder="0 " class="appiframe " src="tables.html " id="appiframe-100Admin "></iframe>-->
            </div>
            <!-------------------------
                选项卡：内容显示区 end
            --------------------------->

        </div>
        <!-- /.main-content -->
        <!----------------------------------------------
            显示区  end
        ------------------------------------------------>

        <!----------------------------------------------
            设置图标-选择皮肤操作  start
        ------------------------------------------------>

        <div class="ace-settings-container " id="ace-settings-container ">
            <div class="btn btn-app btn-xs btn-warning ace-settings-btn " id="ace-settings-btn ">
                <i class="icon-cog bigger-150 "></i>
            </div>

            <div class="ace-settings-box " id="ace-settings-box ">
                <div>
                    <div class="pull-left ">
                        <select id="skin-colorpicker " class="hide ">
                            <option data-skin="default " value="#438EB9 ">#438EB9</option>
                            <option data-skin="skin-1 " value="#222A2D ">#222A2D</option>
                            <option data-skin="skin-2 " value="#C6487E ">#C6487E</option>
                            <option data-skin="skin-3 " value="#D0D0D0 ">#D0D0D0</option>
                        </select>
                    </div>
                    <span>&nbsp; 选择皮肤</span>
                </div>

                <div>
                    <input type="checkbox " class="ace ace-checkbox-2 " id="ace-settings-navbar " />
                    <label class="lbl " for="ace-settings-navbar "> 固定导航条</label>
                </div>

                <div>
                    <input type="checkbox " class="ace ace-checkbox-2 " id="ace-settings-sidebar " />
                    <label class="lbl " for="ace-settings-sidebar "> 固定滑动条</label>
                </div>

                <div>
                    <input type="checkbox " class="ace ace-checkbox-2 " id="ace-settings-breadcrumbs " />
                    <label class="lbl " for="ace-settings-breadcrumbs ">固定面包屑</label>
                </div>

                <div>
                    <input type="checkbox " class="ace ace-checkbox-2 " id="ace-settings-rtl " />
                    <label class="lbl " for="ace-settings-rtl ">切换到左边</label>
                </div>

                <div>
                    <input type="checkbox " class="ace ace-checkbox-2 " id="ace-settings-add-container " />
                    <label class="lbl " for="ace-settings-add-container ">
                        切换窄屏
                        <b></b>
                    </label>
                </div>
            </div>
        </div>
        <!-- /#ace-settings-container -->
        <!----------------------------------------------
            设置图标-选择皮肤操作  end
        ------------------------------------------------>

    </div>
    <!-- main-container-inner end -->

    <a href="# " id="btn-scroll-up " class="btn-scroll-up btn btn-sm btn-inverse ">
        <i class="icon-double-angle-up icon-only bigger-110 "></i>
    </a>
</div>
<!---------------------------------------------------------

        主内容区 end --- 左边导航栏 + 显示区

----------------------------------------------------------->

<?php $this->beginBlock('footer'); ?>

<!-- 增加的js -->
<script src="<?=Url::base()?>/aceAdmin/assets/js/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=Url::base()?>/aceAdmin/assets/js/index.js"></script>

<?php $this->endBlock(); ?>
