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
                <a href="<?php echo AdminActionUrl('users'); ?>" class="">
                    <i class="am-header-icon am-icon-arrow-left"></i>
                </a>
            </div>
            <h1 class="am-header-title"><?php echo L($row['username']); ?></h1>
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

        <?php if ($submit): include T('error_box'); endif; ?>
		<div class="am-g">
			<div class="am-u-sm-12 am-u-sm-centered">
				<form action="<?php echo AdminActionUrl('users&mode=info&user='.$row['id']); ?>" method="POST" role="form" class="am-form">
				  	<legend><?php echo L($row['username']); ?></legend>
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('ID'); ?>:</label>
				   		<strong><?php echo $row['id']; ?></strong>
				  	</div>
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('用户名'); ?></label>
				    	<p class="am-text-xs"><?php echo L('输入用户名说明'); ?></p>
				   		<input type="text" name="username" value="<?php echo $row['username']; ?>" />
				  	</div>				  	
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('邮箱'); ?></label>
				   		<input type="text" name="email" value="<?php echo $row['email']; ?>" />
				  	</div>	
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('权限'); ?></label>
				    	<p class="am-text-xs"><?php echo L('修改权限 说明'); ?></p>
				   		<?php echo Select($GLOBALS['lang']['Auth'], $row['auth'], 'name="auth" data-am-selected'); ?>
				  	</div>
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('密码'); ?></label>
				    	<p class="am-text-xs"><?php echo L('管理更改密码 说明'); ?></p>
				   		<input type="password" name="password" value="" />
				  	</div>
				  	<input type="submit" class="am-btn am-btn-primary am-btn-block" name="submit" value="<?php echo L('保存'); ?>" />
				</form>




<?php include T('admin_footer', true); ?>