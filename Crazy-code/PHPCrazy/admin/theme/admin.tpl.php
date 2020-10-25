<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/ include T('admin_header'); ?>
        <header data-am-widget="header" class="am-header am-header-default">
            <div class="am-header-left am-header-nav">
                <a href="<?php echo HomeUrl(); ?>" class="">
                    <i class="am-header-icon am-icon-home"></i>
                </a>
            </div>
            <h1 class="am-header-title"><?php echo L('管理'); ?></h1>
            <div class="am-header-right am-header-nav">
                <a href="#user-link" class="" data-am-modal="{target: '#my-actions'}">
                    <i class="am-header-icon am-icon-bars"></i>
                </a>
            </div>
        </header>
        <div class="am-modal-actions" id="my-actions">
            <div class="am-modal-actions-group">
                <ul class="am-list">
                    <li class="am-modal-actions-header"><?php echo $GLOBALS['U']['username']; ?></li>
                    <li>
                        <a href="<?php echo HomeUrl('index.php/main:user/'); ?>">
                            <span class="am-icon-user"></span>
                            <?php echo L('用户中心'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo HomeUrl(); ?>">
                            <span class="am-icon-home"></span>
                            <?php echo L('首页'); ?>
                        </a>
                    </li>                    
                </ul>
            </div>
            <div class="am-modal-actions-group">
                <button class="am-btn am-btn-secondary am-btn-block" data-am-modal-close>取消</button>
            </div>
        </div>
        <section class="am-panel am-panel-default">
          	<header class="am-panel-hd">
            	<h3 class="am-panel-title"><?php echo L('设置'); ?></h3>
          	</header>
			<div class="am-panel-bd">
				<p><?php echo L('后台面板 欢迎'); ?></p>
			</div>

<?php foreach ($Module as $k => $v): ?>
			
        	<ul class="am-list am-list-border">
        		<?php foreach ($v as $name => $action): ?>

          		<li><a href="<?php echo AdminActionUrl($action); ?>"><?php echo $name; ?></a></li>

          		<?php endforeach; ?>
        	</ul>
<?php endforeach; ?>
        </section>

<?php include T('admin_footer'); ?>