<div id="hmbkp-constants">

	<p><?php printf( __( 'You can %1$s any of the following %2$s in your %3$s to control advanced settings. %4$s. Defined %5$s will be highlighted.', 'backupwordpress' ), '<code>define</code>', '<code>' . __( 'Constants', 'backupwordpress' ) . '</code>', '<code>wp-config.php</code>', '<a href="http://codex.wordpress.org/Editing_wp-config.php">' . __( 'The Codex can help', 'backupwordpress' ) . '</a>', '<code>' . __( 'Constants', 'backupwordpress' ) . '</code>' ); ?></p>

	<table class="widefat">

		<tr<?php if ( defined( 'HMBKP_PATH' ) ) { ?> class="hmbkp_active"<?php } ?>>

			<td><code>HMBKP_PATH</code></td>

			<td>

				<?php if ( defined( 'HMBKP_PATH' ) ) { ?>
					<p><?php printf( __( 'You\'ve set it to: %s', 'backupwordpress' ), '<code>' . HMBKP_PATH . '</code>' ); ?></p>
				<?php } ?>

				<p><?php printf( __( '文件夹你想存储您的备份文件的路径, defaults to %s.', 'backupwordpress' ), '<code>' . esc_html( hmbkp_path_default() ) . '</code>' ); ?> <?php _e( 'e.g.', 'backupwordpress' ); ?> <code>define( 'HMBKP_PATH', '/home/willmot/backups' );</code></p>

			</td>

		</tr>

		<tr<?php if ( defined( 'HMBKP_MYSQLDUMP_PATH' ) ) { ?> class="hmbkp_active"<?php } ?>>

			<td><code>HMBKP_MYSQLDUMP_PATH</code></td>

			<td>

				<?php if ( defined( 'HMBKP_MYSQLDUMP_PATH' ) ) { ?>
					<p><?php printf( __( 'You\'ve set it to: %s', 'backupwordpress' ), '<code>' . HMBKP_MYSQLDUMP_PATH . '</code>' ); ?></p>
				<?php } ?>

				<p><?php printf( __( '您的路径 %1$s 可执行的。 将用于 %2$s 如果可用的备份的一部分。', 'backupwordpress' ), '<code>mysqldump</code>', '<code>' . __( 'database', 'backupwordpress' ) . '</code>' ); ?> <?php _e( 'e.g.', 'backupwordpress' ); ?> <code>define( 'HMBKP_MYSQLDUMP_PATH', '/opt/local/bin/mysqldump' );</code></p>

			</td>

		</tr>

		<tr<?php if ( defined( 'HMBKP_ZIP_PATH' ) ) { ?> class="hmbkp_active"<?php } ?>>

			<td><code>HMBKP_ZIP_PATH</code></td>

			<td>

				<?php if ( defined( 'HMBKP_ZIP_PATH' ) ) { ?>
					<p><?php printf( __( 'You\'ve set it to: %s', 'backupwordpress' ), '<code>' . HMBKP_ZIP_PATH . '</code>' ); ?></p>
				<?php } ?>

				<p><?php printf( __( '您的路径 %1$s 可执行的。 将用于压缩为.zip %2$s 和 %3$s 如果可用。', 'backupwordpress' ), '<code>zip</code>', '<code>' . __( 'files', 'backupwordpress' ) . '</code>', '<code>' . __( 'database', 'backupwordpress' ) . '</code>' ); ?> <?php _e( 'e.g.', 'backupwordpress' ); ?> <code>define( 'HMBKP_ZIP_PATH', '/opt/local/bin/zip' );</code></p>

			</td>

		</tr>

		<tr<?php if ( defined( 'HMBKP_EXCLUDE' ) ) { ?> class="hmbkp_active"<?php } ?>>

			<td><code>HMBKP_EXCLUDE</code></td>

			<td>

				<?php if ( defined( 'HMBKP_EXCLUDE' ) ) { ?>
					<p><?php printf( __( 'You\'ve set it to: %s', 'backupwordpress' ), '<code>' . HMBKP_EXCLUDE . '</code>' ); ?></p>
				<?php } ?>

				<p><?php _e( '以逗号分隔的文件或目录列表排除，备份目录自动排除。', 'backupwordpress' ); ?> <?php _e( 'e.g.', 'backupwordpress' ); ?> <code>define( 'HMBKP_EXCLUDE', '/wp-content/uploads/, /stats/, .svn/, *.txt' );</code></p>

			</td>

		</tr>

		<tr<?php if ( defined( 'HMBKP_CAPABILITY' ) ) { ?> class="hmbkp_active"<?php } ?>>

			<td><code>HMBKP_CAPABILITY</code></td>

			<td>

				<?php if ( defined( 'HMBKP_CAPABILITY' ) ) { ?>
					<p><?php printf( __( 'You\'ve set it to: %s', 'backupwordpress' ), '<code>' . HMBKP_CAPABILITY . '</code>' ); ?></p>
				<?php } ?>

				<p><?php printf( __( '能使用时调用 %1$s. Defaults to %2$s.', 'backupwordpress' ), '<code>add_menu_page</code>', '<code>manage_options</code>' ); ?> <?php _e( 'e.g.', 'backupwordpress' ); ?> <code>define( 'HMBKP_CAPABILITY', 'edit_posts' );</code></p>

			</td>

		</tr>

		<tr<?php if ( defined( 'HMBKP_ROOT' ) ) { ?> class="hmbkp_active"<?php } ?>>

			<td><code>HMBKP_ROOT</code></td>

			<td>

				<?php if ( defined( 'HMBKP_ROOT' ) ) { ?>
					<p><?php printf( __( 'You\'ve set it to: %s', 'backupwordpress' ), '<code>' . HMBKP_ROOT . '</code>' ); ?></p>
				<?php } ?>

				<p><?php printf( __( '根目录，备份。默认为： %s.', 'backupwordpress' ), '<code>' . HM_Backup::get_home_path() . '</code>' ); ?> <?php _e( 'e.g.', 'backupwordpress' ); ?> <code>define( 'HMBKP_ROOT', ABSPATH . 'wp/' );</code></p>

			</td>

		</tr>

		<tr<?php if ( defined( 'HMBKP_SCHEDULE_TIME' ) && HMBKP_SCHEDULE_TIME !== '11pm' ) { ?> class="hmbkp_active"<?php } ?>>

			<td><code>HMBKP_SCHEDULE_TIME</code></td>

			<td>

				<?php if ( defined( 'HMBKP_SCHEDULE_TIME' ) && HMBKP_SCHEDULE_TIME !== '11pm' ) { ?>
					<p><?php printf( __( 'You\'ve set it to: %s', 'backupwordpress' ), '<code>' . HMBKP_SCHEDULE_TIME . '</code>' ); ?></p>
				<?php } ?>

				<p><?php printf( __( '按您的计划时间运行，默认为： %s.', 'backupwordpress' ), '<code>23:00</code>' ); ?> <?php _e( 'e.g.', 'backupwordpress' ); ?> <code>define( 'HMBKP_SCHEDULE_TIME', '07:30' );</code></p>

			</td>

		</tr>

		<?php foreach ( HMBKP_Services::get_services() as $file => $service )
			echo wp_kses_post( call_user_func( array( $service, 'constant' ) ) ); ?>

	</table>

</div>