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
            <h1 class="am-header-title"><?php echo L('邮件设置'); ?></h1>
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
				<form action="<?php echo AdminActionUrl('mail'); ?>" method="POST" role="form" class="am-form">
				  	<legend><?php echo L('邮件设置'); ?></legend>
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('邮件发送'); ?></label>
				    	<p class="am-text-xs"><?php echo L('邮件发送 说明'); ?></p>
						<select name="send_mail" data-am-selected>
							<option value="1"<?php echo $send_mail_on; ?>><?php echo L('开启'); ?></option>
							<option value="0"<?php echo $send_mail_off; ?>><?php echo L('关闭'); ?></option>
						</select>
				  	</div>
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('使用SMTP发送邮件'); ?></label>
				    	<p class="am-text-xs"><?php echo L('使用SMTP发送邮件 说明'); ?></p>
						<select name="smtp" data-am-selected>
							<option value="1"<?php echo $smtp_on; ?>><?php echo L('开启'); ?></option>
							<option value="0"<?php echo $smtp_off; ?>><?php echo L('关闭'); ?></option>
						</select>
				  	</div>				
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('系统邮件地址'); ?></label>
				    	<input type="text" name="system_mail" value="<?php echo $GLOBALS['C']['system_mail']; ?>" />
				  	</div>
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('使用SSL发送邮件'); ?></label>
				    	<p class="am-text-xs"><?php echo L('使用SMTP发送邮件 说明'); ?></p>
						<select name="smtp_secure" data-am-selected>
							<option value="1"<?php echo $smtp_secure_on; ?>><?php echo L('开启'); ?></option>
							<option value="0"<?php echo $smtp_secure_off; ?>><?php echo L('关闭'); ?></option>
						</select>
				  	</div>
				  	<legend><?php echo L('SMTP信息'); ?></legend>
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('SMTP服务器'); ?></label>
				    	<p class="am-text-xs"><?php echo L('SMTP服务器 说明'); ?></p>
						<input type="text" name="smtp_host" value="<?php echo $GLOBALS['C']['smtp_host']; ?>" placeholder="例如：smtp.domain.com" />
				  	</div>
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('SMTP端口'); ?></label>
				    	<p class="am-text-xs"><?php echo L('SMTP端口 说明'); ?></p>
						<input type="text" name="smtp_port" value="<?php echo $GLOBALS['C']['smtp_port']; ?>" placeholder="25" />
				  	</div>
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('SMTP用户'); ?></label>
				    	<p class="am-text-xs"><?php echo L('SMTP用户 说明'); ?></p>
						<input type="text" name="smtp_username" value="<?php echo $GLOBALS['C']['smtp_username']; ?>" placeholder="user" />
				  	</div>
				  	<div class="am-form-group">
				    	<label for=""><?php echo L('SMTP密码'); ?></label>
				    	<p class="am-text-xs"><?php echo L('SMTP密码 说明'); ?></p>
						<input type="text" name="smtp_password" value="<?php echo $GLOBALS['C']['smtp_password']; ?>" placeholder="***" />
				  	</div>
				  	<input type="submit" class="am-btn am-btn-primary am-btn-block" name="submit" value="<?php echo L('保存'); ?>" />
				</form>
			</div>
		</div>
<?php include T('admin_footer', true); ?>