<?php
/*
*   Package:        PHPCrazy
*   Link:           http://53109774.qzone.qq.com/
*   Author:         Crazy <mailzhangyun@qq.com>
*   Copyright:      2014-2015 Crazy
*   License:        Please read the LICENSE file.
*/ include T('header'); ?>
        <header data-am-widget="header" class="am-header am-header-default">
            <div class="am-header-left am-header-nav">
              <a href="<?php echo HomeUrl(); ?>" class="" data-am-modal="{target: '#my-actions'}">
                <i class="am-header-icon am-icon-arrow-left"></i>
              </a>
            </div>
            <h1 class="am-header-title"><?php echo $GLOBALS['U']['username']; ?></h1>
            <div class="am-header-right am-header-nav">
                <a href="<?php echo HomeUrl('index.php/user:EditUserProfile/'); ?>" class="">
                    <i class="am-header-icon am-icon-edit"></i>
                </a>
            </div>
        </header>
        <br />
		<div class="am-g">
		  	<div class="am-u-sm-12 am-u-sm-centered">
				<div class="am-panel am-panel-secondary">
		  			<div class="am-panel-hd">用户资料</div>
		  			<ul class="am-list am-list-static am-list-border">
		  			  <li><strong><?php echo L('ID'); ?></strong>：<?php echo $GLOBALS['U']['id']; ?></li>
		  			  <li><strong><?php echo L('邮箱'); ?></strong>：<?php echo $GLOBALS['U']['email']; ?></li>
		  			  <li><strong><?php echo L('注册时间'); ?></strong>：<?php echo date('Y-m-d H:i', $GLOBALS['U']['regtime']); ?></li>
		  			</ul>
				</div>
				<?php if ($Auth[ADMIN]): ?>
				<a class="am-btn am-btn-danger am-btn-block" href="<?php echo HomeUrl(ADMIN_FILE); ?>"><i class="am-icon-cogs"></i>  <?php echo L('网站后台管理'); ?></a>
				<?php endif; ?>
                <a class="am-btn am-btn-default am-btn-block" href="<?php echo HomeUrl('index.php/main:login/?action=logout'); ?>"><?php echo L('注销登录'); ?></a>
		  	</div>
		</div>
<?php include T('footer'); ?>