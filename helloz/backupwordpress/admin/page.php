<div class="wrap">

	<?php if ( hmbkp_possible() ) : ?>

		<?php include_once( HMBKP_PLUGIN_PATH . 'admin/backups.php' ); ?>

		<p class="howto"><?php printf( __( '如果您发现 BackUpWordPress 有用, 请 %1$s 访问插件主页 %2$s.', 'backupwordpress' ), '<a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/backupwordpress">', '</a>' ); ?></p>
		
		<p class="howto">由<a href = "http://www.xiaoz.me" title = "访问小z博客" target = "_blank">小z博客</a>进行汉化，若有不正确之处还请多多指正，我的QQ:337003006</p>

		<?php include_once( HMBKP_PLUGIN_PATH . 'admin/upsell.php' ); ?>

	<?php endif; ?>

</div>
