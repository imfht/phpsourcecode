<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-28 15:31:10
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-18 00:49:02
 */
use yii\helpers\Html;
use common\helpers\ImageHelper;

$settings = Yii::$app->settings;
$menucate = Yii::$app->service->backendNavService->getMenu('left');
$moduleAll =  Yii::$app->params['moduleAll'];
/* @var $this \yii\web\View */
/* @var $content string */
?>
<header class="main-header">
    <!-- Logo -->
    <a href="<?= Yii::$app->homeUrl; ?>" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><?= Yii::$app->params['Website']['title']; ?></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><?= Yii::$app->params['Website']['title']; ?></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
        <ul class="nav navbar-nav" id="top-nav">

        <?php if (Yii::$app->params['is_addons']): ?>

                    <li class="dropdown pull-right" >
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">切换模块 <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php foreach ($moduleAll as $key => $value): ?>
                                <li><a href="module?addons=<?=  $value['module_name'] ?>"><?=  $value['addons']['title'] ?></a></li>
                                <li class="divider"></li>
                                
                            <?php endforeach; ?>
                        </ul>
                    </li>
        <?php endif; ?>
        </ul>
        
           
        </div>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-success"><?= Yii::$app->params['message']['total']; ?></span>
                    </a>
                    <?php if (Yii::$app->params['message']['total'] > 0): ?>
                        <ul class="dropdown-menu">
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; max-height: 200px;">
                                <ul class="menu" style="overflow: hidden; width: 100%; height: 200px;padding-left:10px;padding-right:10px;">
                                <?php foreach (Yii::$app->params['message']['list'] as $key => $value): ?>
                                    
                                    <dl style="padding-top: 10px">
                                        <dt>来自： <?= $value['type']; ?> <?= date('Y-m-d H:i', $value['create_time']); ?></dt>
                                        <dd style="line-height: 30px"> 
                                            <a href="<?= $value['url']; ?>" target="_block">
                                                <?= $value['message']; ?>
                                            </a>
                                        </dd>
                                     
                                    </dl>   
                                  
                                  
                                    <?php endforeach; ?>
                               
                              
                                </ul>
                            </li>
                            <li class="text-center"><a href="#">查看更多</a></li>
                            </ul>
                    <?php endif; ?>
                    
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= ImageHelper::tomedia(Yii::$app->user->identity->avatar, 'avatar.jpg'); ?>" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?= Yii::$app->user->identity->username; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= ImageHelper::tomedia(Yii::$app->user->identity->avatar, 'avatar.jpg'); ?>" class="img-circle" alt="User Image">

                            <p>
                                <?= Yii::$app->user->identity->username; ?>
                                <small><?= Yii::$app->user->identity->username; ?></small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="row">
                                <div class="col-xs-4 text-center">
                                    <a class="nav-link" onclick="addTabs({title: '个人资料',close: true,url: '/admin/user/update?id=<?= Yii::$app->user->identity->id; ?>',urlType: 'relative'});">个人资料</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a class="nav-link" onclick="addTabs({title: '修改密码',close: true,url: '/site/reset-password?token=<?= Yii::$app->user->identity->password_reset_token; ?>',urlType: 'relative'});">修改密码</a>
                                </div>
                                
                                <div class="col-xs-4 text-center">
                                    <a class="nav-link" onclick="addTabs({title: '清理缓存',close: true,url: '/system/settings/clear-cache',urlType: 'relative'});">清理缓存</a>
                                </div>

                            </div>
                            <!-- /.row -->
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="#" class="btn btn-default btn-flat nav-link"
                                onclick="addTabs({title: '我的公司',close: true,url: '/admin/bloc/index',urlType: 'relative'});"
                                >我的公司</a>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    '退出登录',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ); ?>
                                <!-- <a href="#" class="btn btn-default btn-flat">Sign out</a> -->
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>