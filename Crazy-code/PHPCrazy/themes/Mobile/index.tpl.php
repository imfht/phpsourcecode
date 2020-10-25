<?php
/*
*   Package:        PHPCrazy
*   Link:           http://53109774.qzone.qq.com/
*   Author:         Crazy <mailzhangyun@qq.com>
*   Copyright:      2014-2015 Crazy
*   License:        Please read the LICENSE file.
*/ include T('header'); ?>

        <style type="text/css">
            .am-gallery li{
               text-align: center;
            }
            .box-shadow{
               -moz-box-shadow:0px 0px 6px #999;
               -webkit-box-shadow:0px 0px 6px #999;
               box-shadow:0px 0px 6px #999;
            }
            .am-titlebar-default {
                border-bottom: 0px solid #DEDEDE;
            }
            .am-navbar-nav a [class*="am-icon"] {
                display: block !important;
            }
        </style>
        <header data-am-widget="header" class="am-header am-header-default">
            <div class="am-header-left am-header-nav">
                <a href="<?php echo HomeUrl(); ?>" class="">
                    <i class="am-header-icon am-icon-home"></i>
                </a>
            </div>
            <h1 class="am-header-title">PHPCrazy</h1>
            <div class="am-header-right am-header-nav">
                <a href="#user-link" class="" data-am-modal="{target: '#my-actions'}">
                    <i class="am-header-icon am-icon-user"></i>
                </a>
            </div>
        </header>
        <div class="am-modal-actions" id="my-actions">
            <div class="am-modal-actions-group">
                <ul class="am-list">
                    <?php if ($GLOBALS['U']['login']): ?>
                    <li class="am-modal-actions-header"><?php echo $GLOBALS['U']['username']; ?></li>
                    <li>
                        <a href="<?php echo HomeUrl('index.php/main:user/'); ?>">
                            <span class="am-icon-user"></span>
                            <?php echo L('用户中心'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo HomeUrl('index.php/main:login/?action=logout'); ?>">
                            <span class="am-icon-sign-out"></span>
                            <?php echo L('注销'); ?>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="am-modal-actions-header">请选择</li>
                    <li>
                        <a href="<?php echo HomeUrl('index.php/main:login/'); ?>">
                            <span class="am-icon-user"></span>
                            <?php echo L('登录'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo HomeUrl('index.php/main:login/?action=register'); ?>">
                            <span class="am-icon-user-plus"></span>
                            <?php echo L('注册'); ?>
                        </a>
                    </li>
                    <li class="am-modal-actions-danger">
                        <a href="<?php echo HomeUrl('index.php/main:login/?action=forgetpassword'); ?>">
                            <?php echo L('忘记密码'); ?>?
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="am-modal-actions-group">
                <button class="am-btn am-btn-secondary am-btn-block" data-am-modal-close>取消</button>
            </div>
        </div>

        <div data-am-widget="slider" class="am-slider am-slider-default" data-am-slider='{}' >
            <ul class="am-slides">
                <li>
                    <img src="<?php echo HomeUrl('themes/Mobile/images/bing-1.jpg'); ?>" />
                </li>
                <li>
                    <img src="<?php echo HomeUrl('themes/Mobile/images/bing-2.jpg'); ?>" />
                </li>
                <li>
                    <img src="<?php echo HomeUrl('themes/Mobile/images/bing-3.jpg'); ?>" />
                </li>
                <li>
                    <img src="<?php echo HomeUrl('themes/Mobile/images/bing-4.jpg'); ?>" />
                </li>
            </ul>
        </div>
        <ul data-am-widget="gallery" class="am-gallery am-avg-sm-4 am-avg-md-4 am-avg-lg-4 am-gallery-default box-shadow" data-am-gallery="{ pureview: true }">
        	<li>
        		<div class="am-gallery-item">
        			<a href="##" class="am-icon-btn am-success am-icon-comments"></a>
        			<h3 class="am-gallery-title">论坛</h3>
        		</div>
        	</li>
        	<li>
        		<div class="am-gallery-item">
        			<a href="#" class="am-icon-btn am-warning am-icon-cloud-download"></a>
        			<h3 class="am-gallery-title">下载</h3>
        		</div>
        	</li>
        	<li>
        		<div class="am-gallery-item">
        			<a href="#" class="am-icon-btn am-danger am-icon-graduation-cap"></a>
        			<h3 class="am-gallery-title">教材</h3>
        		</div>
        	</li>
        	<li>
        		<div class="am-gallery-item">
        			<a href="#" class="am-icon-btn am-primary am-icon-th-large"></a>
        			<h3 class="am-gallery-title">应用</h3>
        		</div>
        	</li>
        	<li>
        		<div class="am-gallery-item">
        			<a href="#" class="am-icon-btn am-secondary am-icon-play"></a>
        				<h3 class="am-gallery-title">视频</h3>
        			</a>
        		</div>
        	</li>
        	<li>
        		<div class="am-gallery-item">
        			<a href="#" class="am-icon-btn am-primary am-icon-leanpub"></a>
        				<h3 class="am-gallery-title">小说</h3>
        		</div>
        	</li>
        	<li>
        		<div class="am-gallery-item">
        			<a href="#" class="am-icon-btn am-default am-icon-microphone"></a>
        				<h3 class="am-gallery-title">聊天</h3>
        		</div>
        	</li>
        	<li>
        		<div class="am-gallery-item">
        			<a href="#" class="am-icon-btn am-success am-icon-music"></a>
        				<h3 class="am-gallery-title">音乐</h3>
        		</div>
        	</li>

        </ul>
        <div data-am-widget="tabs" class="am-tabs am-tabs-d2">
            <ul class="am-tabs-nav am-cf">
                <li class="am-active"><a href="[data-tab-panel-0]">最新</a></li>
                <li class=""><a href="[data-tab-panel-1]">热门</a></li>
                <li class=""><a href="[data-tab-panel-2]">动态</a></li>
            </ul>
            <div class="am-tabs-bd">
                <div data-tab-panel-0 class="am-tab-panel am-active">
                    【青春】那时候有多好，任雨打湿裙角。忍不住哼起，心爱的旋律。绿油油的树叶，自由地在说笑。燕子忙归巢，风铃在舞蹈。经过青春的草地，彩虹忽然升起。即使视线渐渐模糊，它也在我心里。就像爱过的旋律，没人能抹去。因为生命存在失望，歌唱，所以才要歌唱。
                </div>
                <div data-tab-panel-1 class="am-tab-panel ">
                    【彩虹】那时候有多好，任雨打湿裙角。忍不住哼起，心爱的旋律。绿油油的树叶，自由地在说笑。燕子忙归巢，风铃在舞蹈。经过青春的草地，彩虹忽然升起。即使视线渐渐模糊，它也在我心里。就像爱过的旋律，没人能抹去。因为生命存在失望，歌唱，所以才要歌唱。
                </div>
                <div data-tab-panel-2 class="am-tab-panel ">
                    【歌唱】那时候有多好，任雨打湿裙角。忍不住哼起，心爱的旋律。绿油油的树叶，自由地在说笑。燕子忙归巢，风铃在舞蹈。经过青春的草地，彩虹忽然升起。即使视线渐渐模糊，它也在我心里。就像爱过的旋律，没人能抹去。因为生命存在失望，歌唱，所以才要歌唱。
                </div>
            </div>
        </div>
        <div data-am-widget="titlebar" class="am-titlebar am-titlebar-default">
        	<h2 class="am-titlebar-title">帖子列表</h2>
        	<nav class="am-titlebar-nav">
        		<a href="#more" class="">more &raquo;</a>
        	</nav>
        </div>
        <div data-am-widget="list_news" class="am-list-news am-list-news-default">
            <ul class="am-list">
                <li class="am-g am-list-item-dated">
                    <a href="##" class="am-list-item-hd ">我很囧，你保重....晒晒旅行中的那些囧！</a>
                    <span class="am-list-date">2013-09-18</span>
                </li>
                <li class="am-g am-list-item-dated">
                    <a href="##" class="am-list-item-hd ">我最喜欢的一张画</a>
                    <span class="am-list-date">2013-10-14</span>
                </li>
                <li class="am-g am-list-item-dated">
                    <a href="##" class="am-list-item-hd ">“你的旅行，是什么颜色？” 晒照片，换北欧梦幻极光之旅！</a>
                    <span class="am-list-date">2013-11-18</span>
                </li>
            </ul>
        </div>
        <footer data-am-widget="footer" class="am-footer am-footer-default">
            <div class="am-footer-switch">
                <span class="am-footer-ysp" data-rel="mobile" data-am-modal="{target: '#am-switch-mode'}">手机版</span>
                <span class="am-footer-divider"> | </span>
                <a id="godesktop" data-rel="desktop" class="am-footer-desktop" href="javascript:">电脑版</a>
            </div>
            <div class="am-footer-miscs ">
                <p>由 <a href="http://53109774.qzone.qq.com/" title="Crazy" target="_blank" class="">Crazy</a>
                提供技术支持</p>
                <p><?php echo sprintf(L('版权所有 年 作者'), date('Y'), $GLOBALS['C']['sitename']); ?></p>
                <p>京ICP备13033158</p>
            </div>
        </footer>
        <div id="am-footer-modal" class="am-modal am-modal-no-btn am-switch-mode-m am-switch-mode-m-default">
            <div class="am-modal-dialog">
                <div class="am-modal-hd am-modal-footer-hd">
                <a href="javascript:void(0)" data-dismiss="modal" class="am-close am-close-spin " data-am-modal-close>&times;</a>
            </div>
            <div class="am-modal-bd">
                您当前正在使用
                <span class="am-switch-mode-owner">手机版</span>
                <span class="am-switch-mode-slogan">主题</span>
                </div>
            </div>
        </div>
<?php include T('footer'); ?>