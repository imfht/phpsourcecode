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
                <a href="<?php echo AdminUrl('admin.php'); ?>" class="">
                    <i class="am-header-icon am-icon-arrow-left"></i>
                </a>
            </div>
            <h1 class="am-header-title"><?php echo L('常规设置'); ?></h1>
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
				<form action="<?php echo AdminActionUrl('config'); ?>" method="POST" role="form" class="am-form">
				  	<legend><?php echo L('基本设置'); ?></legend>
			  		<div class="am-form-group">
			    		<label for=""><?php echo L('网站标题'); ?></label>
			    		<input type="text" name="sitename" value="<?php echo $GLOBALS['C']['sitename']; ?>" placeholder="<?php echo L('网站标题 说明'); ?>" />
			  		</div>
					<div class="am-form-group">
						<label for=""><?php echo L('外观设置'); ?></label>
						<p class="am-text-xs"><?php echo L('主题设置 说明'); ?></p>
						<?php echo ThemeSel($GLOBALS['C']['theme'], 'name="theme" data-am-selected'); ?>
					</div>
					<div class="am-form-group">
						<label for=""><?php echo L('时区设置'); ?></label>
						<p class="am-text-xs"><?php echo sprintf(L('时区设置 说明'), $GLOBALS['lang']['timezone'][$GLOBALS['C']['timezone']], date($GLOBALS['C']['date_var'])); ?></p>
						<?php echo Select($GLOBALS['lang']['timezone'], $GLOBALS['C']['timezone'], 'name="timezone" data-am-selected'); ?>
					</div>
					<div class="am-form-group">
						<label for=""><?php echo L('时间格式'); ?></label>
						<p class="am-text-xs"><?php echo L('时间格式 说明'); ?></p>
						<input type="text" name="date_var" value="<?php echo $GLOBALS['C']['date_var']; ?>" placeholder="例如: Y-m-d H:i" />
					</div>
					<div class="am-form-group">
						<label for=""><?php echo L('语言'); ?></label>
						<br />
						<?php echo LangSel($GLOBALS['C']['lang'], 'name="lang" data-am-selected'); ?>
					</div>
					<div class="am-form-group">
						<label for=""><?php echo L('HTTPS'); ?></label>
						<p class="am-text-xs"><?php echo L('HTTPS 说明'); ?></p>
						<select name="http_secure" data-am-selected>
						  <option value="1" <?php echo $http_secure_on; ?>><?php echo L('开启'); ?></option>
						  <option value="0" <?php echo $http_secure_off; ?>><?php echo L('关闭'); ?></option>
						</select>
					</div>
					<legend><?php echo L('SEO优化'); ?></legend>
					<div class="am-form-group">
						<label for=""><?php echo L('网站作者'); ?></label>
						<input type="text" name="author" value="<?php echo $GLOBALS['C']['author']; ?>" placeholder="<?php echo L('网站作者 说明'); ?>" />
					</div>
					<div class="am-form-group">
						<label for=""><?php echo L('网站关键词'); ?></label>
						<input type="text" name="keywords" value="<?php echo $GLOBALS['C']['keywords']; ?>" placeholder="<?php echo L('网站关键词 说明'); ?>" />
					</div>
					<div class="am-form-group">
						<label for=""><?php echo L('网站描述'); ?></label>
						<textarea rows="5" name="description" placeholder="<?php echo L('网站关键词 说明'); ?>"><?php echo $GLOBALS['C']['description']; ?></textarea>
					</div>
				  	<input type="submit" class="am-btn am-btn-primary am-btn-block" name="submit" value="<?php echo L('保存'); ?>" />
				</form>
			</div>
		</div>
<?php include T('admin_footer', true); ?>