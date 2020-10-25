<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/ include T('admin_header', true); ?>
        <header data-am-widget="header" class="am-header am-header-default">
            <div class="am-header-left am-header-nav">
                <a href="<?php echo AdminUrl('admin.php'); ?>" class="">
                    <i class="am-header-icon am-icon-arrow-left"></i>
                </a>
            </div>
            <h1 class="am-header-title"><?php echo L('用户列表'); ?></h1>
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
                        <a href="<?php echo AdminUrl('admin.php'); ?>">
                            <span class="am-icon-cog"></span>
                            <?php echo L('管理'); ?>
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
		<div class="am-g">
			<div class="am-u-sm-12 am-u-sm-centered">
				<form action="<?php echo AdminActionUrl('users&mode=search'); ?>" method="POST" role="form" class="am-form">				
				    <br />
				    <div class="am-input-group">
				      	<input type="text" class="am-form-field" name="k" value="" placeholder="<?php echo L('邮箱 ID 用户名'); ?>">
				      	<span class="am-input-group-btn">
				        	<input type="submit" class="am-btn am-btn-default" value="<?php echo L('搜索用户'); ?>" />
				      	</span>
				    </div>
				</form>
				<br />
				<section class="am-panel am-panel-secondary">
				  	<header class="am-panel-hd">
				    	<h3 class="am-panel-title"><?php echo L('用户列表'); ?></h3>
				  	</header>
				  	<div class="am-panel-bd">
						<p class="am-text-xs"><?php echo L('管理指定用户 说明'); ?></p>
				  	</div>
				  	<ul class="am-list am-list-border">
<?php foreach ($UserList as $User): ?>
				  	  	<li><a href="<?php echo AdminActionUrl('users&mode=info&user=' . $User['id']); ?>"><?php echo $User['username']; ?></a></li>
<?php endforeach; ?>
				  	</ul>
				  	<footer class="am-panel-footer"><?php echo $P->Box(); ?></footer>
				</section>
			</div>
		</div>

<?php include T('admin_footer', true); ?>