<?php
use yii\helpers\Url;

?>
<!-- #section:basics/navbar.layout -->
<div class="navbar navbar-default" id="navbar">
    <script type="text/javascript">
        try {
            ace.settings.check('navbar', 'fixed')
        } catch (e) {
        }
    </script>
    <!----------------------------------------------
        顶部栏 start --- logo + 个人信息
    ------------------------------------------------>
    <div class="navbar-container" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="#" class="navbar-brand">
                <small>
                    <i class="icon-leaf"></i>
                    YII-ADMIN
                </small>
            </a>
            <!-- /.brand -->
        </div>
        <!-- /.navbar-header -->
        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">
                <!-- #section:basics/navbar.user_menu -->
                <li class="light-blue">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle" aria-expanded="false">
                           <span class="user-info">
									<small>Welcome</small>
									<?= Yii::$app->user->identity->username?>
								</span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li>
                            <a href="javascript:openapp('<?php echo Url::toRoute('personal/index') ?>','center-1','个人设置',true);">
                                <i class="ace-icon fa fa-cog"></i>
                                个人设置
                            </a>
                        </li>

                        <li class="divider"></li>

                        <li>
                            <?php \yii\bootstrap\ActiveForm::begin(['id'=>'logout','action'=>Url::toRoute('site/logout')])?>
                            <?php \yii\bootstrap\ActiveForm::end()?>
                            <a href="javascript:;logout()">
                                <i class="ace-icon fa fa-power-off"></i> 退出
                            </a>
                            <script>
                                function  logout() {

                                    $('#logout').submit();
                                }
                            </script>

                        </li>
                    </ul>
                </li>

                <!-- /section:basics/navbar.user_menu -->
            </ul>
        </div>
        <!-- /.navbar-header -->
    </div>
    <!-- /.container -->
    <!----------------------------------------------
        顶部栏 end --- logo + 个人信息
    ------------------------------------------------>
</div>


<!-- /section:basics/navbar.layout -->
<div class="main-container" id="main-container">
    <script type="text/javascript">
        try {
            ace.settings.check('main-container', 'fixed')
        } catch (e) {
        }
    </script>


    <!----------------------------------------------
        左边导航栏  start
    ------------------------------------------------>
    <div class="sidebar responsive" id="sidebar">
        <script type="text/javascript">
            try {
                ace.settings.check('sidebar', 'fixed')
            } catch (e) {
            }
        </script>

        <ul class="nav nav-list">
            <li class="active">
                <a href="javascript:openapp('<?php echo Url::toRoute('main') ?>','0','控制台',true);">
                    <i class="menu-icon fa fa-tachometer"></i>
                    <span class="menu-text"> 控制台</span>
                </a>
                <b class="arrow"></b>
            </li>
            <!-- 菜单-->
            <?= $menu ?>
            <!-- 菜单-->

        </ul>
        <!-- /.nav-list -->
        <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
            <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left"
               data-icon2="ace-icon fa fa-angle-double-right"></i>
        </div>

        <script type="text/javascript">
            try {
                ace.settings.check('sidebar', 'collapsed')
            } catch (e) {
            }
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
                    <li class="macro-component-tabitem noclose btn btn-info current" app-id="0" app-url="<?php echo Url::toRoute('main') ?>"
                        app-name="控制台">
                        <span class="macro-tabs-item-text">控制台</span>
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
            <iframe src="<?php echo Url::toRoute('main') ?>" style="width: 100%; height: 100%; display: inline;"
                    frameborder="0" id="appiframe-0" class="appiframe"></iframe>
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


</div><!-- /.main-container -->


<?php $this->beginBlock('footer'); ?>

<!-- 增加的js -->
<script src="<?= Url::base() ?>/aceAdmin/assets/js/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="<?= Url::base() ?>/aceAdmin/assets/js/index.js"></script>

<?php $this->endBlock(); ?>


